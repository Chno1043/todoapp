<?php
$lang = $_GET['lang'] ?? $_POST['lang'] ?? 'ja';
$trans = require "lang_$lang.php";

require 'db.php';
session_start();
session_regenerate_id(true);

if(!isset($_GET['task_id'])){
    die("タスクが見つかりません！");
}
$task_id = $_GET['task_id'];


$stmt = $pdo-> prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->execute([$task_id]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$task){
    die("タスクが存在しません！");
}

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $new_task_name = $_POST['task_name'];
    $new_end_date = $_POST['end_date'];


    // データベースを更新
    $stmt = $pdo->prepare("UPDATE tasks SET task_name = ?, end_date = ? WHERE id = ?");
    $stmt->execute([$new_task_name, $new_end_date, $task_id]);

    header("Location: index.php?lang=$lang");
    exit();
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $trans['edit_task_title']; ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="edit-container">
        <h1><?= $trans['edit_task_title']; ?></h1>
        <form action="" method="POST">
            <input type="hidden" name="lang" value="<?= $lang ?>"> 
            
            <input type="text" name="task_name" value="<?= htmlspecialchars($task['task_name']); ?>" required>
            <input type="date" name="end_date" value="<?= htmlspecialchars($task['end_date']); ?>">
            <button type="submit"><?= $trans['update']; ?></button>
    </form>

        <!-- 削除ボタン -->
    <form action="delete_task.php?lang=<?=$lang?>" method="POST" onsubmit="return confirm('<?= $trans['confirm_delete']; ?>');">
        <input type="hidden" name="task_id" value="<?= $task['id']; ?>">
        <button type="submit" class="delete-btn">🗑 <?= $trans['delete']; ?></button>
    </form>
    <a href="index.php?lang=<?= $lang ?>" class="back-link"><?= $trans['back']; ?></a>
</body>
</html>