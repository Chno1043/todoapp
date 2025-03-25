<?php
$lang = $_GET['lang'] ?? $_POST['lang'] ?? 'ja';
$trans = require "lang_$lang.php";

require 'db.php';
session_start();
session_regenerate_id(true);


if ($_SERVER["REQUEST_METHOD"]== "POST"){
    $task_id = $_POST['task_id'];

    //タスクを消す
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->execute([$task_id]);

   header("Location: index.php?lang=$lang");
   exit();
}

?>