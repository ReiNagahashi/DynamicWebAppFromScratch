<?php

use Database\MySQLWrapper;

set_include_path(get_include_path() . PATH_SEPARATOR . realpath(__DIR__ . '/..'));
spl_autoload_extensions(".php");
spl_autoload_register();

$mysqli = new MySQLWrapper();
$charset = $mysqli->get_charset();
if($charset === null) throw new Exception('Charset could be read');

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$query = "SELECT * FROM users WHERE username = '$username' AND password = '$password';";
echo $query;
$result = $mysqli->query($query);
// 取得してきたデータを連想配列として持つ
$userData = $result->fetch_assoc();

if($userData){
    $login_username = $userData['username'];
    $login_email = $userData['email'];
    $login_role = $userData['role'];

    echo "ログイン成功";
    echo "こんにちは、$login_username<br/>";
    if($login_role == 'admin'){
        echo "role: admin でログインしています。<br/>";
        echo "password: $password<br/>";
    }
}else{
    echo "ログイン失敗";
}
