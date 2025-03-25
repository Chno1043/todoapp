<?php
$lang = $_GET['lang'] ?? $_POST['lang'] ?? 'ja'; 
require "lang_$lang.php";

require 'db.php';// データベース接続
session_start();
session_regenerate_id(true);

if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = uniqid();
}
$session_id = $_SESSION['session_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task_name = $_POST['task']; //タスク入力
    $start_date = date('Y-m-d'); //開始日
    $end_date = NULL; //終了日
    $status = '未完了';


    $stmt = $pdo -> prepare("INSERT INTO tasks 
    (task_name, start_date, end_date,status, session_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$task_name, $start_date, $end_date, $status, $session_id]);

    header("Location: index.php?lang=$lang");
    exit();

}
?>