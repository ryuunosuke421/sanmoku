<?php

try {
    // データベース接続
    $pdo = new PDO("mysql:host=localhost; dbname=sata_db; charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
    // 勝ち数、負け数、引き分け数を集計するクエリ
    $stmt = $pdo->query("
        SELECT
            winner,
            COUNT(*) AS count
        FROM
            tic_tac_toe_results
        GROUP BY
            winner
    ");
    
    // 集計結果を取得
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 初期化
    $playerWins = 0; // 〇（プレイヤー）の勝利数
    $cpuWins = 0; // ✕（CPU）の勝利数
    $draws = 0;  // 引き分け数
    
    // 結果を分類
    foreach ($results as $row) {
        if ($row['winner'] === 'O') {
            $playerWins = $row['count'];
        } elseif ($row['winner'] === 'X') {
            $cpuWins = $row['count'];
        } elseif ($row['winner'] === 'DRAW') {
            $draws = $row['count'];
        }
    }
} catch (PDOException $Exception) {
    die('接続エラー：' . $Exception->getMessage());
}
