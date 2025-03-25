<?php
require 'db.php';
session_start();
session_regenerate_id(true);

if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = uniqid();
}
$session_id = $_SESSION['session_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 🔥 デバッグ用（フォームから送られてきたデータを確認！）
    var_dump($_POST);
    
    $reward_id = isset($_POST['reward_id']) ? (int)trim($_POST['reward_id']) : 0;

    if ($reward_id <= 0) {
        die("エラー: 無効な reward_id です！（フォームのデータが渡っていない可能性あり）");
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM rewards WHERE id = ? AND session_id = ?");
        $stmt->execute([$reward_id, $session_id]);

        // 成功したらリダイレクト！
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        die("SQLエラー: " . $e->getMessage());
    }
}
?>