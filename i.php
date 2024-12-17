<?php
session_start();

// ゲーム開始時の初期設定
if (isset($_POST['start'])) {
    $_SESSION['Player'] = '✕';
    $_SESSION['board'] = array_fill(0, 9, null); // ボードの初期化
}

// プレイヤーの切り替え
function switchPlayer() {
    if (isset($_SESSION['Player'])) {
        $_SESSION['Player'] = $_SESSION['Player'] === '✕' ? '〇' : '✕';
    }
}

// セルの更新
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['board'])) {
    foreach ($_SESSION['board'] as $key => $value) {
        $cellKey = "cell_$key";
        if (isset($_POST[$cellKey]) && $_SESSION['board'][$key] === null) {
            $_SESSION['board'][$key] = $_SESSION['Player'];
            switchPlayer();
            break;
        }
    }
}

// リセットボタンが押されたとき
if (isset($_POST['reset'])) {
    $_SESSION = array();
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
    <h1>三目並べ</h1>
    <form method="post">
        <button type="submit" name="start">ゲームスタート！</button>
    </form>
</header>

<div class="main">
    <form method="post">
        <div class="board">
            <?php
            // ボードを生成
            for ($i = 0; $i < 9; $i++) {
                echo '<div class="cell" id="cell_' . $i . '">';
                echo '<button type="submit" class="btn" name="cell_' . $i . '">';
                echo htmlspecialchars($_SESSION['board'][$i] ?? '');
                echo '</button>';
                echo '</div>';
            }
            ?>
        </div>
        <br>
        <button class="reset-btn" type="submit" name="reset">ゲームをリセット</button>
    </form>
</div>

<footer></footer>

<script src="js/script.js"></script>

</body>
</html>
