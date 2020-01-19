<?php
namespace MyApp;

class Todo
{
    protected $_db;
    public function __construct()
    {
        // データベース接続
        try {
            define('DSN', 'mysql:host=localhost;dbname=todo_app');
            define('DB_USERNAME', 'dbuser');
            define('DB_PASSWORD', '1111');
            $this->_db = new \PDO(DSN, DB_USERNAME, DB_PASSWORD);
            $this->_db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            echo "データベース接続エラー" . $e->getMessage();
            exit;
        }
        // トークン作成
        $this->_createToken();
    }

    // トークンの作成
    private function _createToken()
    {
        if (!isset($_SESSION['token'])) {
            $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(16));
        }
    }
    
    /*
      login.php
    */
    public function login()
    {
        // CSRF対策
        $this->_validateToken();
        // パスワードはハッシュ化した値
        $sql = "SELECT * FROM users WHERE email=? AND password=?";
        $stmt = $this->_db->prepare($sql);
        $stmt->execute([
        $_POST['email'],
        sha1($_POST['password'])
      ]);
        return $stmt->fetch();
    }

    /*
      todo/index.php
    */
    // todoリストのINSERT
    public function insert()
    {
        try {
            // CSRF対策
            $this->_validateToken();
            $this->_db->beginTransaction();
            
            $sql = "INSERT INTO todos SET title=?, time=?, calender_date=?, user_id=?";
            $stmt = $this->_db->prepare($sql);
            $stmt->execute([
               $_POST['new_todo'],
               $_POST['time'],
               $_REQUEST['calender_date'],
               $_SESSION['id']
            ]);

            $this->_db->commit();
            header('Location: index.php?calender_date=' . $_REQUEST['calender_date']);
            exit;
        } catch (\PDOException $e) {
            $this->_db->rollback();
            echo 'todoの登録エラー' . $e->getMessage();
            exit;
        }
    }

    // 登録したtodoリストをデータベースから取り出す
    public function getAll()
    {
        $sql = "SELECT t.* FROM todos AS t JOIN users AS u ON u.id=t.user_id WHERE calender_date=? AND user_id=?";
        $stmt = $this->_db->prepare($sql);
        $stmt->execute([
      $_REQUEST['calender_date'],
      $_SESSION['id']
      ]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ); // オブジェクトで返す
    }

    // メモの件数の取り出し
    public function memo_count()
    {
        $sql = "SELECT * FROM memos WHERE todo_id=? AND user_id=?";
        $stmt = $this->_db->prepare($sql);
        $stmt->execute([
      $_SESSION['t_id'],
      $_SESSION['u_id']
    ]);
        unset($_SESSION['t_id']);
        unset($_SESSION['u_id']);
        return $stmt->rowCount();
    }

    /*
      memo.php
    */
    // todoのideを条件に、todoリストの取得
    public function get_todo()
    {
        $sql = "SELECT * FROM todos WHERE id=? AND user_id=?";
        $stmt = $this->_db->prepare($sql);
        $stmt->execute([
      $_REQUEST['id'],
      $_SESSION['id']
    ]);
        return $stmt->fetch();
    }

    // メモを取得
    public function get_memos()
    {
        $sql = "SELECT * FROM memos WHERE todo_id=? AND user_id=? ORDER BY id DESC";
        $stmt = $this->_db->prepare($sql);
        $stmt->execute([
      $_REQUEST['id'],
      $_SESSION['id']
      ]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ); // オブジェクトで返す
    }

    // メモをデータベースにINSERT
    public function insert_memo()
    {
        try {
            // CSRF対策
            $this->_validateToken();
            $this->_db->beginTransaction();
            
            $sql = "INSERT INTO memos SET memo=?, todo_id=?, user_id=?";
            $stmt = $this->_db->prepare($sql);
            $stmt->execute([
              $_POST['memo'],
              $_REQUEST['id'],
              $_SESSION['id']
            ]);
            
            $this->_db->commit();
            header('Location: memo.php?id=' . $_REQUEST['id']);
            exit;
        } catch (\PDOException $e) {
            $this->_db->rollback();
            echo 'メモの登録エラー' . $e->getMessage();
            exit;
        }
    }

    /*
      update_conf.php
    */
    // todoの編集確認
    public function update_conf()
    {
        $sql = "SELECT * FROM todos WHERE id=?";
        $stmt = $this->_db->prepare($sql);
        $stmt->execute([
      $_REQUEST['todo_id']
    ]);
        return $stmt->fetch(); // 1件のみのため
    }

    // メモの編集確認
    public function update_memo_conf()
    {
        $sql = "SELECT * FROM memos WHERE id=?";
        $stmt = $this->_db->prepare($sql);
        $stmt->execute([
      $_REQUEST['memo_id']
    ]);
        return $stmt->fetch();
    }

    /*
     update.php
    */
    // todoの編集(アップデート)
    public function update()
    {
        try {
            $this->_db->beginTransaction();

            $sql = "UPDATE todos SET title=?, time=? WHERE id=?";
            $stmt = $this->_db->prepare($sql);
            $stmt->execute([
              $_SESSION['todo_conf']['conf_todo'],
              $_SESSION['todo_conf']['conf_time'],
              $_SESSION['todo_id']
            ]);
            $this->_db->commit();
        } catch (\PDOException $e) {
            $this->_db->rollback();
            echo 'todoの更新エラー' . $e->getMessage();
            exit;
        }
    }

    // メモの編集(アップデート)
    public function update_memo()
    {
        try {
            $this->_db->beginTransaction();

            $sql = "UPDATE memos SET memo=? WHERE id=?";
            $stmt = $this->_db->prepare($sql);
            $stmt->execute([
              $_SESSION['memo'],
              $_SESSION['memo_id']
            ]);

            $this->_db->commit();
        } catch (\PDOException $e) {
            $this->_db->rollback();
            echo 'メモの更新エラー' . $e->getMessage();
            exit;
        }
    }

    /*
      ajax.php
    */
    public function post()
    {
        // トークンのバリデーション
        $this->_validateToken();

        if (!isset($_POST['mode'])) {
            throw new \Exception('モードが存在しません');
        }

        switch ($_POST['mode']) {
      case 'update':
        return $this->check_update();
      case 'delete':
        return $this->_delete();
      case 'done_delete':
        return $this->_done_delete();
      case 'memo_delete':
        return $this->_memo_delete();
    }
    }

    protected function _validateToken()
    {
        if (
      !isset($_SESSION['token']) ||
      !isset($_POST['token']) ||
      $_SESSION['token'] !== $_POST['token']
    ) {
            throw new \Exception('不正な投稿です');
        }
    }

    // Todoをチェックした時(todo/index.php)
    private function check_update()
    {
        if (!isset($_POST['id'])) {
            throw new \Exception('updateのidが存在しません');
        }
        try {
            $this->_db->beginTransaction();

            // stateカラムの値が1か0にする
            $sql = "UPDATE todos SET state = (state + 1) % 2 WHERE id=?";
            $stmt = $this->_db->prepare($sql);
            $stmt->execute([
          $_POST['id']
        ]);

            $sql = "SELECT * FROM todos WHERE id=?";
            $stmt = $this->_db->prepare($sql);
            $stmt->execute([
          $_POST['id']
        ]);
            $result = $stmt->fetch();
        
            $this->_db->commit();
            return [
       'state' => $result['state']
    ]; // 配列で返すため
        } catch (\PDOException $e) {
            $this->_db->rollback();
            echo 'Todoチェック時の更新エラー' . $e->getMessage();
            exit;
        }
    }

    // Todoの削除
    private function _delete()
    {
        if (!isset($_POST['id'])) {
            throw new \Exception('deleteのidが存在しません');
        }
        try {
            $this->_db->beginTransaction();

            $sql = "DELETE FROM todos WHERE id=?";
            $stmt = $this->_db->prepare($sql);
            $stmt->execute([
          $_POST['id']
        ]);

            $this->_db->commit();
            return []; // 返り値はないため、空の配列を返す
        } catch (\PDOException $e) {
            $this->_db->rollback();
            echo 'todoの削除エラー' . $e->getMessage();
            exit;
        }
    }

    // doneの削除(todo/index.php)
    public function _done_delete()
    {
        if (!isset($_POST['id'])) {
            throw new \Exception('doneのdeleteのidが存在しません');
        }
        try {
            $this->_db->beginTransaction();

            $sql = "DELETE FROM done WHERE id=?";
            $stmt = $this->_db->prepare($sql);
            $stmt->execute([
          $_POST['id']
        ]);

            $this->_db->commit();
            return []; // 返り値はないため、空の配列を返す
        } catch (\PDOException $e) {
            $this->_db->rollback();
            echo 'doneの削除エラー' . $e->getMessage();
            exit;
        }
    }

    // memoの削除(memo.php)
    private function _memo_delete()
    {
        if (!isset($_POST['memo_id'])) {
            throw new \Exception('memoのdeleteのidが存在しません');
        }
        try {
            $this->_db->beginTransaction();

            $sql = "DELETE FROM memos WHERE id=?";
            $stmt = $this->_db->prepare($sql);
            $stmt->execute([
          $_POST['memo_id']
        ]);

            $this->_db->commit();
            return [];
        } catch (\PDOException $e) {
            $this->_db->rollback();
            echo 'memoの削除エラー' . $e->getMessage();
            exit;
        }
    }
}

/*
  doneリスト(todo/index.php)
*/
class Done extends Todo
{
    // doneリストのINSERT
    public function insert()
    {
        try {
            // CSRF対策
            $this->_validateToken();
            $this->_db->beginTransaction();
            
            $sql = "INSERT INTO done SET title=?, time=?, calender_date=?, user_id=?";
            $stmt = $this->_db->prepare($sql);
            $stmt->execute([
          $_POST['new_done'],
          $_POST['done_new_time'],
          $_REQUEST['calender_date'],
          $_SESSION['id']
        ]);
        
            $this->_db->commit();
            // $_SESSION['modal']をonにする
            $_SESSION['modal'] = 'on';
            header('Location: index.php?calender_date=' . $_REQUEST['calender_date']);
            exit;
        } catch (\PDOException $e) {
            $this->_db->rollback();
            echo 'doneの登録エラー' . $e->getMessage();
            exit;
        }
    }

    // 登録したdoneリストをデータベースから取り出す
    public function getAll()
    {
        $sql = "SELECT * FROM done WHERE user_id=?";
        $stmt = $this->_db->prepare($sql);
        $stmt->execute([
      $_SESSION['id']
    ]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    // doneの編集確認
    public function update_conf()
    {
        $sql = "SELECT * FROM done WHERE id=?";
        $stmt = $this->_db->prepare($sql);
        $stmt->execute([
      $_REQUEST['done_id']
    ]);
        return $stmt->fetch();
    }

    // doneの編集(アップデート)
    public function update()
    {
        try {
            $this->_db->beginTransaction();

            $sql = "UPDATE done SET title=?, time=? WHERE id=?";
            $stmt = $this->_db->prepare($sql);
            $stmt->execute([
              $_SESSION['done_conf']['conf_done'],
              $_SESSION['done_conf']['conf_time'],
              $_SESSION['done_id']
            ]);

            $this->_db->commit();
        } catch (\PDOException $e) {
            $this->_db->rollback();
            echo 'doneの更新エラー' . $e->getMessage();
            exit;
        }
    }
}