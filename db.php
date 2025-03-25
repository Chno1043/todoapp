<?php
// ここを自分のMySQLの設定に変更！
$host = 'mysql80.yknotodolist.sakura.ne.jp'; // MySQLが動いているサーバー
$dbname = 'yknotodolist_todo_app'; // 使いたいデータベース名
$username = 'yknotodolist_todo_app'; // MySQLのユーザー名（デフォルトは 'root'）
$password = 'HMSMykno1225'; // 自分のMySQLのパスワード（設定したもの）

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES utf8mb4");
    // echo "データベース接続成功！";
} catch (PDOException $e) {
    die("データベース接続エラー: " . $e->getMessage());
}
?>

