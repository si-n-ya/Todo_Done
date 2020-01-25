<?php
session_start();
require_once(__DIR__ . '/../config/config.php');
require_once(__DIR__ . '/../lib/functions.php');
require_once(__DIR__ . '/../lib/Controller/Todo_Done.php');

if (!isset($_REQUEST['calender_date'])) {
    header('Location: ../');
    exit;
}

$todoApp = new \MyApp\Todo();
$doneApp = new \MyApp\Done();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // todoリストの処理
    if (isset($_POST['todo_submit'])) {
        // $_SESSION['modal']をoff
        $_SESSION['modal'] = 'off';

        // $_POST['new_todo']が空ではない時INSERT
        if ($_POST['new_todo'] != '') {
            $todoApp->insert();
        } else {
            $error['new_todo'] = 'blank';
        }
    }

    // doneリストの処理
    if (isset($_POST['done_submit'])) {
        // $_SESSION['modal']をon
        $_SESSION['modal'] = 'on';
        
        // $_POST['new_done']が空ではない時INSERT
        if ($_POST['new_done'] != '') {
            $result = $doneApp->insert();
        } else {
            $error['new_done'] = 'blank';
        }
    }
}

if (isset($_SESSION['id']) && $_SESSION['time'] + 1800 > time()) {
    $_SESSION['time'] = time();
    // update_conf.phpでGETパラメーターがない時に、このページにジャンプし、その時GETパラメーターで$_SESSION['calender_date']を使うため
    $_SESSION['calender_date'] = $_REQUEST['calender_date'];
    $todos = $todoApp->getAll();
    $done_all = $doneApp->getAll();
    $day = new DateTime($_REQUEST['calender_date']);
    $day = $day->format('Y年n月j日');
} else {
    header('Location: ../logout.php');
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <title>Todo_Done</title>
    <link rel="stylesheet" href="../asset/css/todo_done.css">
</head>

<body>
    <!-- header読み込み -->
    <?php
   $path = '../';
   include(__DIR__ . '/../_inc/header.php');
   ?>
    <main class="main">
        <div class="todo_all_container body_color">
            <div class="container">
                <p class="calender_date"><?= $day; ?></p>
                <div class="btn_container">
                    <p class="btn_box back_btn_box back_color_box">
                        <a href="../" class="btn back_color">カレンダーに戻る</a>
                    </p>
                    <p class="btn_box modal_btn_box">
                        <button class="modal_btn">Doneリストを表示</button>
                    </p>
                </div> <!-- .btn_container -->
                <!-- Todoリスト -->
                <h1 class="main_title todo_main_title">Todoリスト</h1>
                <form action="" method="post" class="form todo_form">
                    <dl>
                        <dt class="todo_form_dt"><label for="todo_new_time">何時に？</label></dt>
                        <dd class="todo_time_box">
                            <select id="todo_new_time" class="todo_new_time" name="time">
                                <?php for ($i=1; $i<=24; $i++): ?>
                                <option value="<?= $i; ?>" <?= $_POST['time'] == $i ? 'selected': ''; ?>><?= $i; ?>:00
                                </option>
                                <?php endfor; ?>
                            </select>
                        </dd>
                        <dt class="todo_form_dt"><label for="new_todo">何をする？</label></dt>
                        <dd class="new_todo_box">
                            <input type="text" class="new_todo" name="new_todo" value="<?= $_POST['new_todo']; ?>">
                            <?php if ($error['new_todo'] == 'blank'): ?>
                            <p class="error new_todo_error">何をする？が入力されていません</p>
                            <?php endif; ?>
                        </dd>
                    </dl>
                    <p class="submit_todo_box">
                        <input type="submit" class="submit design_todo_submit" name="todo_submit" value="Todoを追加">
                    </p>
                    <input type="hidden" class="token" name="token" value="<?= $_SESSION['token'] ?>">
                </form>
            </div> <!-- .container -->
            <div class="container todo_container">
                <ul class="todos">
                    <!-- 1:00〜24:00まで時間表示 -->
                    <?php for ($i=1; $i<=24; $i++): ?>
                    <li class="todo_list">
                        <span class="todo_time"><?= $i; ?>:00</span>
                        <div class="todo_row_box">
                            <!-- タイトルの後に、メモが書かれていれば、「...」をつけるため、todoテーブルのtodo_idとuser_idを条件にし、titleカラムの件数が1以上であれば、「...」をつけて、なければ、空。
                            $_SESSIONを使うのは、Todo.phpに$todoの値を入れると、NULLになるため、$_SESSION                          で運ぶ -->
                            <?php foreach ($todos as $todo) : ?>
                            <?php
                            $_SESSION['t_id'] = $todo->id;
                            $_SESSION['u_id'] = $todo->user_id;
                            $memo_count = $todoApp->memo_count();
                            ?>
                            <!-- 時間とtodoの時間が一致すれば -->
                            <?php if ($i == $todo->time): ?>
                            <div data-id="<?= h($todo->id); ?>" class="todo_row todo_<?= h($todo->id); ?>">
                                <div class="todo_title_box">
                                    <input type="checkbox" class="check_update" <?php if ($todo->state === '1') {
                                echo 'checked';
                            } ?>>
                                    <span class="todo_title <?php if ($todo->state === '1') {
                                echo 'todo_finish';
                            } ?>">
                                        <a href="memo.php?id=<?= h($todo->id); ?>"
                                            class="link_title"><?= h($todo->title); ?><?= $memo_count > 0 ? ' &bull; &bull; &bull;' : ''; ?>
                                        </a>
                                    </span>
                                </div> <!-- .todo_title_box -->
                                <div class="todo_btn_layout">
                                    <span class="todo_btn_box edit_btn">
                                        <a href="update_conf.php?todo_id=<?= h($todo->id); ?>"
                                            class="todo_row_btn link_edit">編集</a>
                                    </span>
                                    <span class="todo_btn_box delete_btn_box">
                                        <button class="todo_row_btn delete_btn shape_btn">削除</button>
                                    </span>
                                </div> <!-- .btn_layout -->
                            </div> <!-- .todo_row -->
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </div> <!-- .todo_row_box -->
                    </li>
                    <?php endfor; ?>
                </ul>
            </div> <!-- .todo_container -->
        </div> <!-- .todo_all_container -->

        <!-- doneリスト -->
        <div class="modal <?= $_SESSION['modal'] == 'on' ? 'is_modal_active': ''; ?>">
        </div> <!-- .modal -->
        <div class="done_all_container">
            <div class="modal_contents <?= $_SESSION['modal'] == 'on' ? 'is_modal_contents_active': ''; ?>">
                <!-- $_SESSION['modal']を削除 -->
                <?php unset($_SESSION['modal']); ?>
                <div class="container">
                    <div class="close_box">
                        <span class="close"></span>
                    </div> <!-- .close_box -->
                    <h1 class="main_title done_main_title">Doneリスト</h1>
                    <form action="" method="post" class="form done_form">
                        <dl>
                            <dt class="done_form_dt"><label for="done_new_time">何時に？</label></dt>
                            <dd class="done_time_box">
                                <select id="done_new_time" class="done_new_time" name="done_new_time">
                                    <!-- 1:00〜24:00までの時間表示 -->
                                    <?php for ($i=1; $i<=24; $i++): ?>
                                    <option value="<?= $i; ?>"><?= $i; ?>:00</option>
                                    <?php endfor; ?>
                                </select>
                            </dd>
                            <dt class="done_form_dt"><label for="new_done">何をした？</label></dt>
                            <dd class="new_done_box">
                                <input type="text" id="new_done" class="new_done" name="new_done">
                                <?php if ($error['new_done'] == 'blank'): ?>
                                <p class="error new_done_error">何をした？が入力されていません</p>
                                <?php endif; ?>
                            </dd>
                        </dl>
                        <p class="submit_done_box">
                            <input type="submit" class="submit design_done_submit" name="done_submit" value="Doneを追加">
                        </p>
                        <input type="hidden" class="token" name="token" value="<?= $_SESSION['token'] ?>">
                    </form>
                </div> <!-- .container -->
                <div class="container done_container">
                    <ul class="dones">
                        <?php for ($i=1; $i<=24; $i++): ?>
                        <li class="done_list">
                            <span class="done_time"><?= $i; ?>:00</span>
                            <div class="done_row_box">
                                <?php foreach ($done_all as $done) : ?>
                                <!-- 時間とdoneの時間が一致すれば表示 -->
                                <?php if ($i == $done->time): ?>
                                <div data-id="<?= h($done->id); ?>" class="done_row done_<?= h($done->id); ?>">
                                    <div class="done_title_box">
                                        <span class="done_title">
                                            <?= h($done->title); ?>
                                        </span>
                                    </div> <!-- .done_title_box -->
                                    <div class="done_btn_layout">
                                        <span class="done_btn_box edit_btn">
                                            <a href="update_conf.php?done_id=<?= h($done->id); ?>"
                                                class="done_row_btn link_edit">編集</a>
                                        </span>
                                        <span class="done_btn_box delete_btn_box">
                                            <button class="done_row_btn delete_btn shape_btn">削除</button>
                                        </span>
                                    </div> <!-- .btn_layout -->
                                </div> <!-- .todo_row -->
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </div> <!-- .todo_row_box -->
                        </li>
                        <?php endfor; ?>
                    </ul>
                </div> <!-- .done_container -->
            </div> <!-- .modal_contents -->
        </div> <!-- done_all_container -->
    </main>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="../asset/js/todo_done.js"></script>
</body>

</html>