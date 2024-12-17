<?php
session_start();
require_once 'db.php';

// ゲームスタート！
if (isset($_POST['start'])) {
    $_SESSION['Player'] = '〇'; // 先行のプレイヤー
    for ($i = 0; $i < 9; $i ++) {
        $_SESSION["cell_$i"] = null; // 9つのセルの初期化
    }
    $_SESSION["count"] = 0; // カウントを初期化
    $_SESSION['message'] = null;
}

// プレイヤーの初期化を確認
if (! isset($_SESSION['Player'])) {
    $_SESSION['Player'] = '〇'; // セッションにプレイヤー情報がなければデフォルト値を設定
}

// プレイヤーの〇✕を切り替える
function switchPlayer()
{
    if (isset($_SESSION['Player']) && $_SESSION['Player'] === '〇') {
        $_SESSION['Player'] = '✕';
    } else {
        $_SESSION['Player'] = '〇';
    }
}

// セルの表示変更
if ($_SERVER['REQUEST_METHOD'] === 'POST') { // サーバー情報および実行時の環境情報を取得する
    for ($i = 0; $i < 9; $i ++) {
        $cellKey = "cell_$i";

        // セッションキーが未設定の場合は初期化する
        if (! isset($_SESSION[$cellKey])) {
            $_SESSION[$cellKey] = null;
        }

        if (isset($_POST[$cellKey]) && $_SESSION[$cellKey] === null) {
            $_SESSION[$cellKey] = $_SESSION['Player']; // 現在のプレイヤーの〇✕を代入
            switchPlayer(); // プレイヤーを切り替え

            // 相手のターン
            // まずは、cell０～９のうち、nullを探し、配列に入れる。
            $emptyCells = [];
            for ($j = 0; $j < 9; $j ++) {
                if (! isset($_SESSION["cell_$j"])) {
                    $_SESSION["cell_$j"] = null;
                }
                if ($_SESSION["cell_$j"] === null) {
                    $emptyCells[] = $j;
                }
            }
            // さっき作った配列からランダムにインデックスをとりだす。
            // もし、$emptyCellsが空ではなかったら？
            if (! empty($emptyCells)) {
                $randIndex = array_rand($emptyCells);
                $compCellKey = "cell_" . $emptyCells[$randIndex];
                $_SESSION[$compCellKey] = $_SESSION['Player'];

                $winner = checkWinner();

                if ($winner) {
                    if ($winner === 'draw') {
                        $_SESSION['message'] = '引き分けです！';
                    } else {
                        $_SESSION['message'] = "勝者は{$winner}！";

                        $_SESSION['message'] = styleMessage($_SESSION['message']);
                    }
                } else {
                    switchPlayer();
                }
                break; // クリックされたセルだけを更新

                // $emptyCellsが空→つまり、マスに空きがない
            } else {
                $winner = checkWinner();
                if ($winner) {
                    if ($winner === 'draw') {
                        $_SESSION['message'] = '引き分けだ！';
                    } else {
                        $_SESSION['message'] = "勝者は{$winner}！";

                        $_SESSION['message'] = styleMessage($_SESSION['message']);
                    }
                }
            }
        }
    }
}

// 勝利判定関数
function checkWinner()
{
    $winningPatterns = [
        [0,3,6],[1,4,7],[2,5,8],
        [0,1,2],[3,4,5],[6,7,8],
        [0,4,8],[2,4,6]
    ];

    foreach ($winningPatterns as $pattern) {
        [$a,$b,$c] = $pattern;
        if (isset($_SESSION["cell_$a"], $_SESSION["cell_$b"], $_SESSION["cell_$c"]) && $_SESSION["cell_$a"] !== null && $_SESSION["cell_$a"] === $_SESSION["cell_$b"] && $_SESSION["cell_$b"] === $_SESSION["cell_$c"]) {

            $winner = $_SESSION["cell_$a"]; // 勝者を取得 ('〇' または '✕')
            saveResultToDB($winner); // 勝敗をDBに保存
            return $winner; // 勝者を返す
        }
    }

    for ($i = 0; $i < 9; $i ++) {
        if ($_SESSION["cell_$i"] === null) {
            return null; // ゲーム続行
        }
    }

    saveResultToDB("DRAW"); // 引き分けの場合を保存
    return 'draw'; // 引き分け
}

// データベースに送る関数
function saveResultToDB($winner)
{
    global $pdo; // グローバルスコープの $pdo を使用

    echo '保存される winner の値: ' . htmlspecialchars($winner) . '<br>';

    try {
        $stmt = $pdo->prepare("INSERT INTO tic_tac_toe_results (winner) VALUES (:winner)");
        $stmt->execute([
            ":winner" => $winner
        ]);
    } catch (PDOException $e) {
        error_log('データベースエラー: ' . $e->getMessage()); // エラーを記録
        echo 'データベースエラー: ' . htmlspecialchars($e->getMessage());
    }
}

// メッセージを装飾する関数
function styleMessage($message)
{
    // 〇を青、✕を赤にする
    $message = preg_replace('/〇/', '<span class="blue">〇</span>', $message);
    $message = preg_replace('/✕/', '<span class="red">✕</span>', $message);

    return $message;
}

// リセットボタン処理
if (isset($_POST['reset'])) {
    session_unset();
    session_destroy();
    session_start();
    $_SESSION['message'] = null;
}
?>
<!doctype html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>三目並べ</title>
<link rel="stylesheet" href="../Tictactoe/style.css">
</head>

<body>
  <header>
    <div class="header-content">
      <div class="title">
        <h1>三目並べ</h1>
      </div>
    </div>
  </header>

  <div class="sub_main">
    <div class="display">
      <p class="start"><?php if(isset($_POST['start'])) {echo "試合開始！";} ?></p>
      <p class="message"><?php echo isset($_SESSION['message']) ? $_SESSION['message'] : ''; ?></p>
    </div>
  </div>

  <div class="main">
    <form method="post">
      <div class="board">

        
        <?php for ($i = 0; $i < 9; $i ++) :?>
          <div class="cell">
          <button type="submit" class="btn" name="cell_<?php echo $i; ?>">
            <?php
            if (isset($_SESSION["cell_$i"])) {
                echo "<span class='mark'>{$_SESSION["cell_$i"]}</span>";
            }
            ?>
            </button>
        </div>
        <?php endfor; ?>

      </div>

      <div class="sr_btn">
        <button type="submit" name="start" class="s_btn">ゲームスタート！</button>
        <button type="submit" name="reset" class="r_btn">リセット</button>
      </div>

      <table>
        <tbody>
          <tr>
            <th>勝利数</th>
            <th>〇(プレイヤー)</th>
            <th>✕（CPU）</th>
            <th>引き分け</th>
          </tr>
          <tr>
            <th>通算</th>
            <td><?php echo $playerWins; ?></td>
            <td><?php echo $cpuWins; ?></td>
            <td><?php echo $draws; ?></td>
          </tr>
        </tbody>
      </table>

    </form>
  </div>

  <footer>
    <div class="copy">
      <small>Copyright © sata Co., Ltd. All rights reserved.</small>
    </div>
  </footer>

  <script src="js/script.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

  <script>
$(document).ready(function(){
    $(".mark:contains('〇')").addClass("circle-style");
    $(".mark:contains('✕')").addClass("cross-style");
});
</script>

</body>
</html>