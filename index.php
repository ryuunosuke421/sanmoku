<?php
session_start();
require_once 'db.php';

// 初期関数
function start($turn)
{
    $_SESSION['Player'] = $turn; // プレイヤーの設定
    $_SESSION['computer'] = ""; // 後攻時使用

    for ($i = 0; $i < 9; $i ++) {
        $_SESSION["cell_$i"] = null; // マスの初期化
    }
    $_SESSION['message'] = null;

    // 後攻の場合、コンピュータが最初に動く
    if ($turn === 'cross') {
        $_SESSION['computer'] = "value";
        computerMove();
        switchPlayer();
    }
}

// 先攻ボタンクリック時
if (isset($_POST['circle_btn'])) {
    $_SESSION['move'] = 'first';
    start("circle");
}

// 後攻ボタンクリック時
if (isset($_POST['cross_btn'])) {
    $_SESSION['move'] = 'second';
    start("cross");
}

// 先攻・後攻を決めずにクリックした場合
if (! isset($_SESSION['Player'])) { 
    $_SESSION['Player'] = ""; 
}

// プレイヤーのcircle〇、cross✕を切り替える
function switchPlayer()
{
    if ($_SESSION['Player'] === "circle") {
        $_SESSION['Player'] = "cross";
    } else {
        $_SESSION['Player'] = "circle";
    }
}

// コンピュータの動き
function computerMove()
{
    // nullのマスを探して、配列に入れる
    $emptyCells = [];
    for ($i = 0; $i < 9; $i ++) {
        if ($_SESSION["cell_$i"] === null) {
            $emptyCells[] = $i;
        }
    }
    
    // 配列からランダムにindexを取り出す
    if (! empty($emptyCells)) {
        $randIndex = array_rand($emptyCells);
        $compCellKey = "cell_" . $emptyCells[$randIndex];

        if ($_SESSION['computer'] === "value") {
            $_SESSION[$compCellKey] = "circle"; // コンピュータ（〇）
        } else
            $_SESSION[$compCellKey] = "cross"; // コンピュータ（✕）
    }
}

// マスの表示変更
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 先攻・後攻を決めずにクリックした場合
    if ($_SESSION['Player'] === "") {
        $_SESSION['message'] = "先攻か後攻を選択してください！";
        $_SESSION['message'] = styleMessage_2($_SESSION['message']);
    } else {
        
        // プレイヤーの動き
        for ($i = 0; $i < 9; $i ++) {
            $cellKey = "cell_$i";
            
            
            if (! isset($_SESSION[$cellKey])) { // 設定されていない場合、初期化
                $_SESSION[$cellKey] = null;
            }

            // クリックしたマスが空白の場合
            if (isset($_POST[$cellKey]) && $_SESSION[$cellKey] === null) {
 
                if ($_SESSION['computer'] === "value") { // 後攻の場合
                    $_SESSION[$cellKey] = "cross";
                } else
                    $_SESSION[$cellKey] = "circle"; // 先攻の場合
                $winner = checkWinner();

                if ($winner) {
                    gameEnd($winner);
                    break;
                }

                switchPlayer(); // プレイヤーからコンピュータへ切り替え
                computerMove(); // コンピュータのターン

                $winner = checkWinner();
                if ($winner) {
                    gameEnd($winner);
                } else {
                    switchPlayer(); // プレイヤーのターンに切り替え
                }
                break;
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
        if (isset($_SESSION["cell_$a"], $_SESSION["cell_$b"], $_SESSION["cell_$c"]) &&
            $_SESSION["cell_$a"] !== null && $_SESSION["cell_$b"] !== null && $_SESSION["cell_$c"] !== null &&
            $_SESSION["cell_$a"] === $_SESSION["cell_$b"] && $_SESSION["cell_$b"] === $_SESSION["cell_$c"]) {

                $winner = $_SESSION["cell_$a"]; // 勝者を取得 (circle or cross)

            // 空いているマスを全て空白で埋める
            for ($i = 0; $i < 9; $i ++) {
                if ($_SESSION["cell_$i"] === null) {
                    $_SESSION["cell_$i"] = '';
                }
            }
            $move = $_SESSION['move'];
            saveResultDB($winner, $move); // 勝敗をDBに保存
            return $winner;
        }
    }

    for ($i = 0; $i < 9; $i ++) {
        // 勝敗判定がつかず、マスがすべて埋まった場合
        if ($_SESSION["cell_$i"] === null) {
            return null;
        }
    }
    $move = $_SESSION['move'];
    saveResultDB("DRAW", $move); // 引き分けをDBに保存
    return 'draw';
}

// ゲーム終了時の処理
function gameEnd($winner)
{
    if ($winner === 'draw') {
        $_SESSION['message'] = '引き分けです！';
    } else {
        $_SESSION['message'] = "{$winner}の勝ち！！";
    }
    $_SESSION['message'] = styleMessage($_SESSION['message']);
}

// dbに結果を保存
function saveResultDB($winner, $move)
{
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO tic_tac_toe_results (result, my_turn) VALUES (:result, :my_turn)");
        $stmt->execute([
            ':result' => $winner,
            ':my_turn' => $move
        ]);
    } catch (PDOException $e) {
        error_log('データベースエラー: ' . $e->getMessage());
    }
}

// メッセージを装飾する関数
function styleMessage($message)
{
    $message = preg_replace('/circle/', '<span class="blue">〇</span>', $message);
    $message = preg_replace('/cross/', '<span class="red">✕</span>', $message);
    return $message;
}

function styleMessage_2($message)
{
    $message = preg_replace('/先攻/', '<span class="blue">先攻</span>', $message);
    $message = preg_replace('/後攻/', '<span class="red">後攻</span>', $message);
    return $message;
}

// リセットボタン処理
if (isset($_POST['reset'])) {
    session_unset();
    session_destroy();
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
      <p class="start"><?php if(isset($_POST['circle_btn'])) {echo "あなたは'<span class='blue'>" . htmlspecialchars('先攻') . "</span>'です";} ?></p>
      <p class="start"><?php if(isset($_POST['cross_btn'])) {echo "あなたは'<span class='red'>" . htmlspecialchars('後攻') . "</span>'です";} ?></p>
      <p class="message"><?php echo isset($_SESSION['message']) ? $_SESSION['message'] : ''; ?></p>
    </div>
  </div>

  <div class="main">
    <form method="post">

      <div class="board">
        <?php for ($i = 0; $i < 9; $i ++) :?>
          <div class="cell">
          <button type="submit" class="btn" name="cell_<?php echo htmlspecialchars($i); ?>">
            <?php
            if (isset($_SESSION["cell_$i"])) {
                if ($_SESSION["cell_$i"] === "circle") {
                    echo "<span class='mark circle-style'>〇</span>";
                } else if ($_SESSION["cell_$i"] === "cross") {
                    echo "<span class='mark cross-style'>✕</span>";
                }
            }
            ?>
            </button>
        </div>
        <?php endfor; ?>
      </div>

      <div class="sr_btn">
        <button type="submit" name="circle_btn" class="circle_btn">先<br></button>
        <button type="submit" name="cross_btn" class="cross_btn">後</button>
        <button type="submit" name="reset" class="s_btn">リセット</button>
      </div>

      <table>
        <tbody>
          <tr>
            <th>累計</th>
            <th>勝ち</th>
            <th>負け</th>
            <th>引き分け</th>
          </tr>
          <tr>
            <th class="first">先攻</th>
            <td><?php echo htmlspecialchars($firstWins, ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($firstLose, ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($firstdraws, ENT_QUOTES, 'UTF-8'); ?></td>
          </tr>
          <tr>
            <th class="second">後攻</th>
            <td><?php echo htmlspecialchars($secondWins, ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($secondLose, ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($seconddraws, ENT_QUOTES, 'UTF-8'); ?></td>
          </tr>
        </tbody>
      </table>

    </form>
    
    <div class="explanation">
    <p>※遊び方<br>
    1.先攻ボタンまたは後攻ボタンのいずれかをクリックしてください。<br>
    2.空いているマスをクリックすると、〇または✕が表示されます。<br>
    3.ゲームが終了したら、リセットボタンをクリックして再挑戦しましょう。<br></p>
    </div>
    
  </div>

  <footer>
    <div class="copy">
      <small>Copyright © sata Co., Ltd. All rights reserved.</small>
    </div>
  </footer>
</body>
</html>