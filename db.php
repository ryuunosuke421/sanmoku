<?php
try {
    // データベース接続
    $pdo = new PDO("mysql:host=localhost; dbname=sata_db; charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
    // クエリ
    $stmt = $pdo->query("
        SELECT
            my_turn,
            result
        FROM
            tic_tac_toe_results
    ");
    
    // 結果を取得
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 先攻
    $firstWins = 0; // 先攻勝ち
    $firstLose = 0; // 先攻負け
    $firstdraws = 0; // 先攻引き分け
    
    // 後攻
    $secondWins = 0; // 後攻勝ち
    $secondLose = 0; // 後攻負け
    $seconddraws = 0; // 後攻引き分け
    
    // 結果を分類
    foreach ($results as $row) {
        
        if ($row['result'] === 'circle' && $row['my_turn'] === 'first') { // 先攻勝ち
            $f_win[] = $row['result'];
            $firstWins = count($f_win);
            
        } elseif ($row['result'] === 'cross' && $row['my_turn'] === 'first') { // 先攻負け
            $f_lose[] = $row['result'];
            $firstLose = count($f_lose);
            
        } elseif ($row['result'] === 'cross' && $row['my_turn'] === 'second') { // 後攻勝ち
            $s_win[] = $row['result'];
            $secondWins = count($s_win);
            
        } elseif ($row['result'] === 'circle' && $row['my_turn'] === 'second') { // 後攻負け
            $s_lose[] = $row['result'];
            $secondLose = count($s_lose);
            
        } elseif ($row['result'] === 'DRAW' && $row['my_turn'] === 'first') { // 先攻引き分け
            $f_drawa[] = $row['result'];
            $firstdraws = count($f_drawa);
            
        } elseif ($row['result'] === 'DRAW' && $row['my_turn'] === 'second') { // 後攻引き分け
            $s_draws[] = $row['result'];
            $seconddraws = count($s_draws);
        }
    }
} catch (PDOException $Exception) {
    die('接続エラー：' . $Exception->getMessage());
}
