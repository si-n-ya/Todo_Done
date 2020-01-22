<?php
session_start();
require_once(__DIR__ . '/config/config.php');
require_once(__DIR__ . '/lib/functions.php');
require_once(__DIR__ . '/lib/Controller/Todo_Done.php');

$todoApp = new MyApp\Todo();

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // 通常ログイン時
      if (isset($_POST['normal_login'])) {
          // クッキーに値が入っている時
          if ($_COOKIE['email'] !== '') {
              $email = $_COOKIE['email'];
          }
          $email = $_POST['email']; // submitした時の動き
          if ($_POST['email'] !== '' && $_POST['password'] !== '') {
              $login = $todoApp->login();

              // login情報が一致した時(取得できた時)
              if ($login) {
                  $_SESSION['id'] = $login['id'];
                  $_SESSION['time'] = time();
  
                  // 自動的にログインにチェックが入っている時
                  if ($_POST['save'] === 'on') {
                      setcookie('email', $_POST['email'], time()+60*60*24*14);
                  }

                  header('Location: ./');
                  exit;
              } else {
                  $error['login'] = 'failed';
              }
          } else {
              $error['login'] = 'blank';
          }
          // ゲストログイン時
      } elseif ($_POST['guest_login']) {
          $_POST['email'] = 'aaa@com';
          $_POST['password'] = '1111';
          $login = $todoApp->login();

          if ($login) {
              $_SESSION['id'] = $login['id'];
              $_SESSION['time'] = time();

              // 自動的にログインにチェックが入っている時
              if ($_POST['save'] === 'on') {
                  setcookie('email', $_POST['email'], time()+60*60*24*14);
              }

              header('Location: ./');
              exit;
          }
      }
  }
  ?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ログイン</title>
    <link rel="stylesheet" href="asset/css/todo_done.css">
</head>

<body>
    <div class="container login_container">
        <h1 class="main_title">ログイン</h1>
        <div class="login_form_box">
            <form action="" method="post" class="login_form">
                <p><?php if ($error['login'] === 'blank'): ?>
                    <p class="error">※メールアドレスとパスワードをご記入ください</p>
                    <?php endif; ?>
                    <?php if ($error['login'] === 'failed'): ?>
                    <p class="error">※ログインに失敗しました。<br>
                        メールアドレスとパスワードを正しくご記入ください</p>
                    <?php endif; ?></p>
                <dl>
                    <dt class="login_dt"><label for="email">メールアドレス</label></dt>
                    <dd class="login_dd">
                        <input type="email" name="email" id="email" size="30" maxlength="255" class="login_input"
                            value="<?= h($email); ?>">
                    </dd>
                    <dt class="login_dt"><label for="password">パスワード</label></dt>
                    <dd class="login_dd">
                        <input type="password" name="password" id="password" size="30" maxlength="255"
                            class="login_input">
                    </dd>
                    <dd>
                        <input type="checkbox" id="save" class="login_save" name="save" value="on">
                        <label for="save">次回からは自動的にログインする</label>
                    </dd>
                </dl>
                <!-- 通常ログイン時 -->
                <p class="login_submit_box"><input type="submit" class="submit login_submit normal_login"
                        name="normal_login" value="ログイン"></p>
                <!-- ゲストログイン時 -->
                <p class="login_submit_box"><input type="submit" class="submit login_submit guest_login"
                        name="guest_login" value="ゲストユーザーとしてログイン"></p>
                <!-- CSRF対策 -->
                <input type="hidden" class="token" name="token" value="<?= $_SESSION['token'] ?>">
            </form>
        </div> <!-- .content -->
    </div> <!-- .container -->
</body>

</html>