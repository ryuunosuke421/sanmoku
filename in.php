<?php
session_start();

// ゲームスタート！
if (isset($_POST['start'])) {
    $_SESSION['Player'] = '〇'; // 先行プレイヤー
    for ($i = 0; $i < 9; $i ++) {
        $_SESSION["cell_$i"] = null; // 9つのセルを初期化
    }
    $_SESSION["count"] = 0; // カウントを初期化
    $_SESSION['message'] = null; // メッセージをクリア
}

// プレイヤーの初期化を確認
if (! isset($_SESSION['Player'])) {
    $_SESSION['Player'] = '〇'; // セッションにプレイヤー情報がなければデフォルト値を設定
}

// 勝敗判定の関数
function checkWinner()
{
    $winningPatterns = [
        [
            0,
            1,
            2
        ],
        [
            3,
            4,
            5
        ],
        [
            6,
            7,
            8
        ], // 横
        [
            0,
            3,
            6
        ],
        [
            1,
            4,
            7
        ],
        [
            2,
            5,
            8
        ], // 縦
        [
            0,
            4,
            8
        ],
        [
            2,
            4,
            6
        ] // 斜め
    ];

    foreach ($winningPatterns as $pattern) {
        [
            $a,
            $b,
            $c
        ] = $pattern;
        if (isset($_SESSION["cell_$a"], $_SESSION["cell_$b"], $_SESSION["cell_$c"]) && $_SESSION["cell_$a"] !== null && $_SESSION["cell_$a"] === $_SESSION["cell_$b"] && $_SESSION["cell_$b"] === $_SESSION["cell_$c"]) {
            return $_SESSION["cell_$a"]; // 勝者（〇または✕）を返す
        }
    }

    

    for ($i = 0; $i < 9; $i ++) {
        if ($_SESSION["cell_$i"] === null) {
            return null; // ゲーム続行
        }
    }

    return 'draw'; // 引き分け
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

// セルの表示変更(自分)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    for ($i = 0; $i < 9; $i ++) {
        $cellKey = "cell_$i";

        if (! isset($_SESSION[$cellKey])) {
            $_SESSION[$cellKey] = null;
        }

        if (isset($_POST[$cellKey]) && $_SESSION[$cellKey] === null) {
            $_SESSION[$cellKey] = $_SESSION['Player'];
            $winner = checkWinner();

            if ($winner) {
                if ($winner === 'draw') {
                    $_SESSION['message'] = '引き分けです！';
                } else {
                    $_SESSION['message'] = "勝者は {$winner} です！";
                }
            } else {
                switchPlayer();
            }
            break;
        }
    }
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
    <h1>三目並べ</h1>
    <form method="post">
      <br>
      <button type="submit" name="start">ゲームスタート！</button>
        <?php if (isset($_SESSION['message'])) { ?>
            <p class="message"><?php echo htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8'); ?></p>
        <?php } ?>
        <br>
      <br>
    </form>
  </header>

  <div class="main">
    <form method="post">
      <div class="board">
            <?php for ($i = 0; $i < 9; $i++) { ?>
                <div class="cell">
          <button type="submit" class="btn" name="cell_<?php echo $i; ?>">
                        <?php
                if (isset($_SESSION["cell_$i"])) {
                    echo "<span class='mark'>{$_SESSION["cell_$i"]}</span>";
                }
                ?>
                    </button>
        </div>
            <?php } ?>
        </div>
      <br>
      <button class="reset-btn" type="submit" name="reset">ゲームをリセット</button>
      <br>
    </form>
  </div>

  <footer> </footer>

  <script src="js/script.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script>
$(document).ready(function(){
    $(".mark:contains('〇')").addClass("circle-style");
    $(".mark:contains('✕')").addClass("cross-style");
});
</script>