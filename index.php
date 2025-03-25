<?php

// 多言語設定
$lang = $_GET['lang'] ?? 'ja';
$trans = require "lang_$lang.php";

require 'db.php';  // データベース接続
session_start();
session_regenerate_id(true);// セッションIDを定期的に更新！

// ユーザー識別用の `session_id`
if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = uniqid();
}
$session_id = $_SESSION['session_id'];

// タスク一覧を取得
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE session_id = ?");
$stmt->execute([$session_id]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 🎀 現在のリボン数を取得
$stmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE status = '完了' AND session_id = ?");
$stmt->execute([$session_id]);
$ribbons = $stmt->fetchColumn() ?? 0;

// 🎁 ご褒美リストを取得
$stmt = $pdo->prepare("SELECT id, reward_text, required_ribbons FROM rewards WHERE session_id = ? ORDER BY required_ribbons ASC");
$stmt->execute([$session_id]);
$reward_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToDoリスト</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1><?= $trans['title']; ?></h1>
        <p><?= $trans['greeting']; ?></p>
        </header>
    <!-- 言語切り替え機能 -->
    <div class="language-wrapper">
        <input type="checkbox" id="toggle-lang" hidden>
        <label for="toggle-lang" class="lang-toggle">🌐</label>
        <div class="lang-dropdown">
            <a href="?lang=ja" class="<?= $lang === 'ja' ? 'active' : ''?>">🇯🇵 日本語</a>
            <a href="?lang=en" class="<?= $lang === 'en' ? 'active' : ''?>">🇬🇧 English</a>
            <a href="?lang=cn" class="<?= $lang === 'cn' ? 'active' : ''?>">🇹🇼 中文</a>
    </div>
</div>

    <div class="container">
        <form action="add_task.php?lang=<?= $lang ?> " method="POST">
            <input type="text" name="task"  class="task-input" placeholder="<?= $trans['placeholder_task']; ?>" required>
            <button type="submit"><?= $trans['add']; ?></button>
        </form>

        <section class="task-list">
            <h2><?= $trans['task_list']; ?></h2>
            <table>
                <thead>
                    <tr>
                        <th><?= $trans['task_header']; ?></th>
                        <th><?= $trans['start_date']; ?></th>
                        <th><?= $trans['end_date']; ?></th>
                        <th><?= $trans['status']; ?></th>
                        <th><?= $trans['edit']; ?></th>
                    </tr>
                </thead>
                <tbody id="task-list">
                    <?php foreach ($tasks as $task) : ?>
                    <tr>
                        <td><?= htmlspecialchars($task['task_name']); ?></td>
                        <td><?= htmlspecialchars($task['start_date']); ?></td>
                        <td><?= htmlspecialchars($task['end_date']); ?></td>
                        <td>
                            <?php if ($task['status'] === '未完了') : ?>
                                <form action="complete_task.php?lang=<?= $lang?>" method="POST" style="display:inline;">
                                    <input type="hidden" name="task_id" value="<?= $task['id']; ?>">
                                    <button type="submit" class="complete-btn"><?=$trans['status_complete'];?></button>
                                </form>
                            <?php else : ?>
                                ✅ <?=$trans['status_done']; ?>
                            <?php endif; ?>
                        </td>
                        <td><a href="edit_task.php?task_id=<?= $task['id']; ?>&lang=<?= $lang ?>" class="edit-btn"><?=$trans['edit'];?></a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section> <!-- パソコン表示のテーブル -->
        <!-- スマホのカード-->
        <div class="task-card-container">
            <?php foreach ($tasks as $task) : ?>
            <div class="task-card">
                <p><strong><?= htmlspecialchars($task['task_name']); ?></strong></p>
                <p><?= $trans['start_date']; ?>：<?= htmlspecialchars($task['start_date']); ?></p>
                <p><?=$trans['end_date']; ?>：<?= htmlspecialchars($task['end_date']); ?></p>

            <?php if ($task['status'] === '未完了') : ?>
                <p><?= $trans['status']; ?>：</p>
            <form action="complete_task.php?lang=<?= $lang ?>" method="POST" class="complete-form">
                <input type="hidden" name="task_id" value="<?= $task['id']; ?>">
                <button type="submit" class="complete-btn"><?= $trans['status_complete'];?></button>
            </form>
        <?php else : ?>
            <p><?=$trans['status'];?>：✅ <?= $trans['status_done'];?></p>
        <?php endif; ?>

      <p>
        <a href="edit_task.php?task_id=<?= $task['id']; ?>&lang=<?= $lang ?>" class="edit-btn"><?= $trans['edit'];?></a>
      </p>
    </div>
  <?php endforeach; ?>
</div>


        <!-- 🎀 ご褒美コーナー -->
        <section class="reward">
            <h2><?= $trans['reward_section'];?> </h2>
            <p><?= $trans['reward_message'];?></p>

            <!-- 🎀リボンのカウンター -->
            <div class="ribbon-counter">
                <h3><?= $trans['ribbons_now'];?></h3>
                <p id="ribbon-count"> <?= $ribbons; ?> 🎀</p>
            </div>

            <!-- ご褒美追加フォーム -->
            <form action="add_reward.php?lang=<?= $lang?>" method="POST">
                <input type="text" name="reward_text" placeholder="<?= $trans['placeholder_reward']; ?>" required>
                <input type="number" name="required_ribbons" placeholder="<?= $trans['placeholder_required_ribbons']; ?>" required>
                <button type="submit"><?= $trans['add']; ?></button>
            </form>

            <!-- ご褒美リスト（データベースから取得） -->
            <ul>
                <?php foreach ($reward_list as $reward) : ?>
                    <?php
                    // !-- 🔵 リボン数が条件を満たしていたら、水色にする！ -->
                    $is_unlocked = ($ribbons >= $reward['required_ribbons']);
                    $reward_class = $is_unlocked ? 'unlocked-reward' : 'locked-reward';
                    ?>
                    <li class="<?= $reward_class; ?>">
                    <strong><?= sprintf($trans['reward_requires'], $reward['required_ribbons']); ?></strong>
                        <?= htmlspecialchars($reward['reward_text']); ?>
                        <?php if ($is_unlocked) : ?>
                            <span><?= $trans['great_job'];?></span>
                    <?php endif; ?>

                <!-- ❌ 削除ボタン -->
                 <form action="delete_reward.php" method="POST" style="display:inline;">
                    <input type="hidden" name="reward_id" value="<?= htmlspecialchars($reward['id']); ?>">
                    <button type="submit">❌ <?= $trans['delete'];?></button>
                    </form>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
    </div>

    <footer>
        <p>&copy; LCC ToDoリスト</p>
        <p>今日も一日おつかれさま🐼💖</p>
    </footer>
</body>
</html>