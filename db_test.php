<?php
require 'db.php'; // データベース接続

// テスト用のSQLクエリ
try {
    $stmt = $pdo->query("SELECT '接続成功！' AS message");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo $result['message']; // 「接続成功！」が表示されればOK！
} catch (PDOException $e) {
    echo "エラー: " . $e->getMessage();
}
?>