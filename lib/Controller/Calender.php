<?php
namespace MyApp;

class Calender
{
    public $prev;
    public $next;
    public $yearMonth;
    public $userName;
    private $_db;
    private $_thisMonth;
    private $_calender_date;

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
            echo 'データベース接続エラー' . $e->getMessage();
            exit;
        }

        try {
            if (!isset($_GET['t']) || !preg_match('/\A\d{4}-\d{2}\z/', $_GET['t'])) {
                throw new \Exception();
            }
            $this->_thisMonth = new \DateTime($_GET['t']);
        } catch (\Exception $e) {
            // 今月の最初の日付(1日)を取得
            $this->_thisMonth = new \DateTime('first day of this month');
        }

        $this->userName = $this->_userName();
        $this->prev = $this->_createPrevLink();
        $this->next = $this->_createNextLink();
        // 今月を、英語の月と西暦で取得
        $this->yearMonth = $this->_thisMonth->format('F Y');
    }

    // ログインした人の名前を表示するためのSELECT文
    public function _userName()
    {
        $sql = "SELECT * FROM users WHERE id=?";
        $users = $this->_db->prepare($sql);
        $users->execute([$_SESSION['id']]);
        return $users->fetch();
    }
    
    // 前月のカレンダーをgetパラメーターの't'で表す
    private function _createPrevLink()
    {
        // 今月の英語の月と西暦をコピー
        $dt = clone $this->_thisMonth;
        // $dtを前月の西暦と2桁の月に変える
        return $dt->modify('-1 month')->format('Y-m');
    }

    // 次の月のカレンダーをgetパラメーターの't'で表す
    private function _createNextLink()
    {
        // 今月の英語の月と西暦をコピー
        $dt = clone $this->_thisMonth;
        // $dtを前月の西暦と2桁の月に変える
        return $dt->modify('+1 month')->format('Y-m');
    }

    // カレンダーの日付部分を表示
    public function show()
    {
        $tail = $this->_getTail();
        $body = $this->_getBody();
        $head = $this->_getHead();
        // カレンダーの最初と最後に<tr>を置く(<td>は、while文で作っているため)
        $html = '<tr>' . $tail . $body . $head . '</tr>';
        echo $html;
    }

    // 前月のから土曜日まで、来月の日付を表示
    private function _getTail()
    {
        $tail = '';
        // 前月の最終日を取得
        $lastDayOfPrevMonth = new \DateTime('last day of ' . $this->yearMonth . ' -1 month');
        // format('w')が 0(日), 1(月), 2(火), 3(水), 4(木), 5(金) の間、前月最終日から1日ずつ引いた日付を日曜日から順に表示。6(土)になったら終わり。
        // 新しくできた日付(sprintf)の後に$tailを連結し、大きい日付を後ろに持ってくる。
        while ($lastDayOfPrevMonth->format('w') < 6) {
            $tail = sprintf('<td class="gray date">%d</td>', $lastDayOfPrevMonth->format('d')) . $tail;
            // 前月の最終日から1日づつ引いていく
            $lastDayOfPrevMonth->sub(new \DateInterval('P1D'));
        }
        return $tail;
    }

    private function _getBody()
    {
        $body = '';
        // 今月の最初の日(1日)から今月の最終日までを取得
        $period = new \DatePeriod(
            new \DateTime('first day of ' . $this->yearMonth),
            new \DateInterval('P1D'),
          // DatePeriodの期間の終わりは含まないので、今月の最終日を取得
          new \DateTime('first day of ' . $this->yearMonth . ' +1 month')
        );

        // 今日の日付を分からせるため、今日の日付を取得
        $today = new \DateTime('today');
        foreach ($period as $day) {
            // カレンダーの日付に予定を表示するためのSELECT文
            // calender_dateはformatから、user_idはsessionから取得
            $sql = "SELECT * FROM todos WHERE calender_date=? AND user_id=?";
            $stmt = $this->_db->prepare($sql);
            $stmt->execute([
                $day->format('Y-n-j'),
                $_SESSION['id']
                ]);
            $calender = $stmt->fetch();

            // user_idとcalender_dateが一致したtodoテーブルのtitleカラムが空でなければ、3文字のみ取得。空だと、$titleを空にする
            if (!empty($calender['title'])) {
                $title = mb_substr($calender['title'], 0, 3, "utf-8") . '&#133;';
            } else {
                $title = '';
            }

            // format('w')が0(日曜日)の時、</tr><tr> で一行を終え、新しい行にする
            if ($day->format('w') === '0') {
                $body .= "</tr><tr>";
            }
            // $day->format('Y-m-d')(カレンダーの日付) と $today->format('Y-m-d')(今日の)日付が一致すれば、$todayClassに'today'を代入
            $todayClass = ($day->format('Y-m-d') === $today->format('Y-m-d')) ? 'today' : '';
            // 土曜日と日曜日の日付の色を変えるため、<td>に$day->format("w")で曜日毎にclass名を変える
            // 今日の日付がforeachで来た時に、<td>のclass名に$todayClassの'today'をいれる
            // $titleでtodoテーブルのtitleカラムの値を表示
            $body .= sprintf('<td class="youbi_%d %s date"><a href="todo_done/index.php?calender_date=%d-%d-%d" class="link_date">%d<p class="title">%s</p></a></td>', $day->format("w"), $todayClass, $day->format('Y'), $day->format('n'), $day->format('j'), $day->format('j'), $title);
        }
        return $body;
    }

    // 今月の最終日から土曜日まで、来月の日付を表示
    private function _getHead()
    {
        $head = '';
        // 来月の最初の日(1日)を取得
        $firstDayOfNextMonth = new \DateTime('first day of ' . $this->yearMonth . ' +1 month');
        // format('w')が 1(月), 2(火), 3(水), 4(木), 5(金), 6(土)の間、来月の1日からの日付を表示。0(日曜日)になったら終わりで、show()での</tr>で一行にする。
        while ($firstDayOfNextMonth->format('w') > 0) {
            $head .= sprintf('<td class="gray date">%d</td>', $firstDayOfNextMonth->format('d'));
            // 来月の1日から1日ずつ足していく
            $firstDayOfNextMonth->add(new \DateInterval('P1D'));
        }
        return $head;
    }
}