<?php
session_start();
require_once(__DIR__ . '/../config/config.php');
require_once(__DIR__ . '/../lib/functions.php');
require_once(__DIR__ . '/../lib/Controller/Todo_Done.php');

$todoApp = new \MyApp\Todo();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['memo'] != '') {
        $todoApp->insert_memo();
    } else {
        $error['memo'] = 'blank';
    }
}

if (isset($_REQUEST['id']) && isset($_SESSION['id']) && $_SESSION['time'] + 1800 > time()) {
    $_SESSION['time'] = time();
    // update_conf.phpからmemo.phpに戻る時に、GETパラメーターを使うため
    $_SESSION['post_id'] = $_REQUEST['id'];
    // todo/index.phpに戻るため、todoのタイトルを取得するため。
    $get_todo = $todoApp->get_todo();
    // memoを表示するため
    $get_memos = $todoApp->get_memos();
} else {
    header('Location: ../');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Memoリスト</title>
    <link rel="stylesheet" href="../asset/css/todo_done.css">
</head>

<body>
    <!-- header読み込み -->
    <?php
   $path = '../';
   include(__DIR__ . '/../_inc/header.php');
   ?>
    <div class="memo_container">
        <div class="container">
            <p class="btn_box memo_back_box">
                <a href="index.php?calender_date=<?= h($get_todo['calender_date']); ?>"
                    class="btn memo_back back_color">Todoリストへ戻る</a>
            </p>
            <h1 class="main_title memo_main_title">Memoリスト</h1>
            <p class="explain">『<?= h($get_todo['title']); ?>』に関して、ご自由にお書きください！</p>
            <form action="" method="post" class="form memo_form">
                <textarea name="memo" class="memo_textarea" rows="10"></textarea>
                <?php if ($error['memo'] == 'blank'): ?>
                            <p class="error">メモが入力されていません</p>
                            <?php endif; ?>
                <p class="submit_memo_box">
                    <input type="submit" class="submit design_memo_submit" value="メモを追加"></<input>
                </p>
                <!-- CSRF対策 -->
                <input type="hidden" class="token" name="token" value="<?= h($_SESSION['token']); ?>">
            </form>
        </div> <!-- .container -->
        <div class="container">
            <?php foreach ($get_memos as $get_memo): ?>
            <div class="list_one memo_<?= h($get_memo->id); ?>">
                <ul class="memos">
                    <li class="list_memo">
                        <?= h($get_memo->memo); ?>
                    </li>
                </ul>
                <div class="ed_de_box" data-id="<?= h($get_memo->id); ?>">
                    <p class="edit_btn edit_memo_box">
                        <a href="update_conf.php?memo_id=<?= h($get_memo->id); ?>"
                            class="todo_row_btn link_edit edit_memo">編集</a>
                    </p>
                    <p class=" delete_btn_box delete_memo_box">
                        <button class="todo_row_btn delete_btn shape_btn delete_memo">削除</button>
                    </p>
                </div> <!-- .ed_de_box -->
            </div> <!-- .list_one -->
            <?php endforeach; ?>
        </div> <!-- .container -->
    </div> <!-- .memo_container -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="../asset/js/todo_done.js"></script>
</body>

</html>