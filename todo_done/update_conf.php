<?php
session_start();
require_once(__DIR__ . '/../config/config.php');
require_once(__DIR__ . '/../lib/functions.php');
require_once(__DIR__ . '/../lib/Controller/Todo_Done.php');

$todoApp = new \MyApp\Todo();
$doneApp = new \MyApp\Done();

// todoリストからの編集
if (isset($_REQUEST['todo_id'])) {
    $conf = $todoApp->update_conf();
// doneリストからの編集
} elseif (isset($_REQUEST['done_id'])) {
    $conf = $doneApp->update_conf();
    // todo/index.phpに戻る時、Doneリストを表示するため
    $_SESSION['modal'] = 'on';
// memoリストからの編集
} elseif (isset($_REQUEST['memo_id'])) {
    $conf = $todoApp->update_memo_conf();
} else {
    header('Location: index.php?calender_date=' . $_SESSION['calender_date']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // todoリストからの編集
    if ($_REQUEST['todo_id']) {
        $_SESSION['todo_id'] = $_REQUEST['todo_id'];
        $_SESSION['todo_conf'] = $_POST;
        header('Location: update.php?todo_date=' . $conf['calender_date']);
        exit;
    // doneリストからの編集
    } elseif ($_REQUEST['done_id']) {
        $_SESSION['done_id'] = $_REQUEST['done_id'];
        $_SESSION['done_conf'] = $_POST;

        header('Location: update.php?done_date=' . $conf['calender_date']);
        exit;
    // memoリストからの編集
    } elseif ($_REQUEST['memo_id']) {
        $_SESSION['memo_id'] = $_REQUEST['memo_id'];
        $_SESSION['memo'] = $_POST['memo'];

        header('Location: update.php?todo_id=' . $conf['todo_id']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>
        <?php if (isset($_REQUEST['todo_id'])): ?>
        Todoリストの編集確認
        <?php elseif (isset($_REQUEST['done_id'])): ?>
        Doneリストの編集確認
        <?php elseif (isset($_REQUEST['memo_id'])): ?>
        Memoリストの編集確認
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
    <div class="container">
        <div class="conf_container">
            <!-- todoリストの編集 -->
            <?php if (isset($_REQUEST['todo_id'])): ?>
            <h1 class="main_title todo_main_title conf_title">Todoリストの編集確認</h1>
            <form action="" method="post" class="form conf_form">
                <dl>
                    <dt class="todo_form_dt"><label for="conf_time">予定時刻</label></dt>
                    <dd class="todo_time_box">
                        <select id="conf_time" class="todo_new_time" name="conf_time">
                            <?php for ($i=1; $i<=24; $i++): ?>
                            <option value="<?= $i; ?>" <?= $conf['time'] == $i ? 'selected' : ''; ?>><?= $i; ?>:00
                            </option>
                            <?php endfor; ?>
                        </select>
                    </dd>
                    <dt class="todo_form_dt"><label for="conf_todo">Todo</label></dt>
                    <dd class="new_todo_box">
                        <input type="text" id="new_todo" class="new_todo" name="conf_todo"
                            value="<?= $conf['title']; ?>">
                    </dd>
                </dl>
                <p class="submit_todo_box">
                    <input type="submit" class="submit design_todo_submit" value="更新">
                </p>
                <p class="btn_box  back_color_box back_box">
                    <a href="index.php?calender_date=<?= h($_SESSION['calender_date']); ?>"
                        class="btn back_color">Todoリストへ戻る</a>
                </p>
            </form>
            <!-- doneリストの編集 -->
            <?php elseif (isset($_REQUEST['done_id'])): ?>
            <h1 class="main_title done_main_title conf_title">Doneリストの編集確認</h1>
            <form action="" method="post" class="form conf_form">
                <dl>
                    <dt class="done_form_dt"><label for="conf_time">予定時刻</label></dt>
                    <dd class="done_time_box">
                        <select id="conf_time" class="done_new_time" name="conf_time">
                            <?php for ($i=0; $i<24; $i++): ?>
                            <option value="<?= $i; ?>" <?= $conf['time'] == $i ? 'selected' : ''; ?>><?= $i; ?>:00
                            </option>
                            <?php endfor; ?>
                        </select>
                    </dd>
                    <dt class="done_form_dt"><label for="conf_done">Done</label></dt>
                    <dd class="new_done_box">
                        <input type="text" id="conf_done" class="new_done" name="conf_done"
                            value="<?= $conf['title']; ?>">
                    </dd>
                </dl>
                <p class="submit_done_box">
                    <input type="submit" class="submit design_done_submit" value="更新">
                </p>
                <p class="btn_box  back_color_box back_box">
                    <a href="index.php?calender_date=<?= h($_SESSION['calender_date']); ?>"
                        class="btn back_color">Doneリストへ戻る</a>
                </p>
            </form>
            <!-- メモの編集 -->
            <?php elseif (isset($_REQUEST['memo_id'])): ?>
            <h1 class="main_title memo_main_title conf_title">メモの編集確認</h1>
            <form action="" method="post" class="form conf_form">
                <div class="memo_conf_box">
                    <textarea name="memo" class="memo_textarea" cols="50" rows="10"><?= h($conf['memo']); ?></textarea>
                    <p class="submit_memo_box">
                        <input type="submit" class="submit design_memo_submit" value="更新">
                    </p>
                    <p class="btn_box back_color_box back_box">
                        <a href="memo.php?id=<?= h($_SESSION['post_id']); ?>" class="btn back_color">メモ一覧へ戻る</a>
                    </p>
                </div> <!-- .memo_conf_box -->
            </form>
            <?php endif; ?>
        </div> <!-- .conf_container -->
    </div> <!-- .container -->
</body>

</html>