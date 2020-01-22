<?php
session_start();
require_once(__DIR__ . "/lib/Controller/Calender.php");
require_once(__DIR__ . "/lib/functions.php");
require_once(__DIR__ . "/config/config.php");

if (isset($_SESSION['id']) && $_SESSION['time'] + 1800 > time()) {
    $_SESSION['time'] = time();
    $cal = new \MyApp\Calender();
    $user = $cal->userName;
} else {
    header('Location: ./logout.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Todo_Doneカレンダー</title>
    <link rel="stylesheet" href="asset/css/todo_done.css">
</head>

<body>
    <!-- header読み込み -->
    <?php
    $path = './';
    include(__DIR__ . '/_inc/header.php');
    ?>
    <main class="container">
        <p class="explain"><?= h($user['name']); ?>さん、カレンダーの日付をクリックして、Todoリストを作ろう！</p>
        <table border="1" class="table">
            <thead>
                <tr>
                    <th class="calender_head"><a href="index.php?t=<?php echo h($cal->prev); ?>">&laquo</a></th>
                    <th colspan="5" class="calender_head"><?php echo h($cal->yearMonth); ?></th>
                    <th class="calender_head"><a href="index.php?t=<?php echo h($cal->next); ?>">&raquo</a></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="week sun">Sun</td>
                    <td class="week">Mon</td>
                    <td class="week">Tue</td>
                    <td class="week">Wed</td>
                    <td class="week">Thu</td>
                    <td class="week">Fri</td>
                    <td class="week sat">Sat</td>
                </tr>
                <?php $cal->show(); ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="7" class="calender_foot"><a href="index.php" class="today_link">Today</a></th>
                </tr>
            </tfoot>
        </table>
    </main>
</body>

</html>