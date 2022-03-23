<?php
require_once("../sqlConnect.php");
if(isset($_POST['username']) && isset($_POST['password']))
{
    $genHash = hash("sha512",$_POST['password']);
    if(GetHashForUserByName($_POST['username']) === $genHash)
    {
        $token = hash("sha512",$_POST['username']+$genHash);
        setcookie("_session",$token,0,"/");
        CheckAndSetToken($_POST['username'],$token);
    }
}
(bool)$login = true;
if(!isset($_COOKIE['_session']))
{
    $login = true; //just to make sure
}
?>
<html>
<head>
    <link href="style/admin.css" type="text/css" rel="stylesheet">
    <link href="style/main.css" type="text/css" rel="stylesheet">
    <title>
        Admin
    </title>
</head>
<body>
<?php
if($login)
{
    echo '<form id="adminLogin" method="POST" action="index.php">
    <p class="loginFieldText">Username: </p><input type="text" name="username" class="loginField"/><br>
    <p class="loginFieldText">Password: </p><input type="password" name="password" class="loginField"/><br>
    <input type="submit" name="login"/>
    </form>';
}
else
{

}
?>
</body>
<footer>

</footer>
</html>