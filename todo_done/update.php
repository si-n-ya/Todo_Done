<?php
session_start();
require_once(__DIR__ . '/../config/config.php');
require_once(__DIR__ . '/../lib/functions.php');
require_once(__DIR__ . '/../lib/Controller/Todo_Done.php');

$todoApp = new \MyApp\Todo();
$doneApp = new \MyApp\Done();

$calender_date = $_SESSION['calender_date'];
if (isset($_SESSION['todo_id']) && isset($_SESSION['todo_conf'])) {
    // todoリストの編集
    $todoApp->update();
    unset($_SESSION['todo_id']);
    unset($_SESSION['todo_conf']);
} elseif (isset($_SESSION['done_id']) && isset($_SESSION['done_conf'])) {
    // doneリストの編集
    $doneApp->update();
    unset($_SESSION['done_id']);
    unset($_SESSION['done_conf']);
} elseif (isset($_SESSION['memo']) && isset($_SESSION['memo_id'])) {
    // memoリストの編集
    $todoApp->update_memo();
    unset($_SESSION['memo']);
    unset($_SESSION['memo_id']);
} else {
    unset($_SESSION['calender_date']);
    header('Location: index.php?calender_date=' . $calender_date);
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>
        <?php if ($_REQUEST['todo_date']): ?>
        Todoリストのアップデート
        <?php elseif ($_REQUEST['done_date']): ?>
        Doneリストのアップデート
        <?php elseif ($_REQUEST['todo_id']): ?>
        Memoリストのアップデート
        <?php endif; ?>
    </title>
    <link rel="stylesheet" href="../asset/css/todo_done.css">
</head>

<body>
    <!-- header読み込み -->
    <?php
   $path = '../';
   include(__DIR__ . '/../_inc/header.php');
   ?>
    <!-- todoリストの編集完了 -->
    <?php if (isset($_REQUEST['todo_date'])): ?>
    <p class="edit_done_text">Todoリストの編集が完了しました</p>
    <p class="btn_box back_box"><a href="index.php?calender_date=<?= h($_REQUEST['todo_date']); ?>"
            class="btn back_color">Todoリストへ戻る</a></p>
    <!-- doneリストの編集完了 -->
    <?php elseif (isset($_REQUEST['done_date'])): ?>
    <p class="edit_done_text">Doneリストの編集が完了しました</p>
    <p class="btn_box back_box"><a href="index.php?calender_date=<?= h($_REQUEST['done_date']); ?>"
            class="btn back_color">Doneリストへ戻る</a></p>
    <!-- メモの編集完了 -->
    <?php elseif (isset($_REQUEST['todo_id'])): ?>
    <p class="edit_done_text">メモの編集が完了しました</p>
    <p class="btn_box back_box"><a href="memo.php?id=<?= h($_REQUEST['todo_id']); ?>" class="btn back_color">メモ一覧へ戻る</a>
    </p>
    <?php endif; ?>
</body>

</html>