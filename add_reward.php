<?php
$lang = $_GET['lang'] ?? $_POST['lang'] ?? 'ja';
$trans = require "lang_$lang.php";

require 'db.php';
session_start();
session_regenerate_id(true);

if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = uniqid();
}
$session_id = $_SESSION['session_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reward_text = trim($_POST['reward_text']); //ご褒美入力
    $required_ribbons = isset($_POST['required_ribbons']) ? (int)$_POST['required_ribbons']:0;
    //必要な🎀の数入力？ 

    if ($reward_text === '' || $required_ribbons <= 0) {
        die("エラー: 無効なご褒美の内容またはリボン数です！");
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO rewards (reward_text, required_ribbons, session_id) VALUES (?, ?, ?)");
        $stmt->execute([$reward_text, $required_ribbons, $session_id]);

        header("Location: index.php?lang=$lang");
        exit();
    } catch (PDOException $e) {
        die("SQLエラー: " . $e->getMessage());
    }
}
?>