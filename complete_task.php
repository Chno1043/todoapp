<?php
$lang = $_GET['lang'] ?? $_POST['lang'] ?? 'ja';
$trans = require "lang_$lang.php";

require 'db.php';
session_start();
session_regenerate_id(true);

if(!isset($_SESSION['session_id'])){
    $_SESSION['session_id'] = uniqid();
}

$session_id = $_SESSION['session_id'];

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // 🔥 `task_id` を取得
    $task_id = isset($_POST['task_id']) ? (int)$_POST['task_id'] : 0;

     // タスクIDが正しいかチェック
     if($task_id > 0){
        try{
            // 🎯 タスクを「完了」に更新
            $stmt = $pdo->prepare("UPDATE tasks SET status = '完了' WHERE id = ? AND session_id = ?");
            $stmt->execute([$task_id, $session_id]);

            // 🎀 現在のリボン数を再取得（タスクの完了数をカウント）
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE status = '完了' AND session_id = ?");
            $stmt->execute([$session_id]);
            $ribbons = $stmt-> fetchColumn() ?? 0;

             // 🚀 成功したらメインページへリダイレクト（リボン数を反映！）
             header("Location: index.php?lang=$lang");
             exit();
        }catch (PDOException $e) {
            die("SQLエラー: " . $e->getMessage());
        }
    }else{
        die("エラー：無効なtask_idです!");
    }
        
}
?>