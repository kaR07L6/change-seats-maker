<?php
// 席のサイズ（列×行）を設定（必要に応じて変更してください）
$cols = 5;
$rows = 4;

$students_file = "data/students.csv";
$preferences_file = "data/preferences.json";

$students = [];
$preferences = [];

// 生徒読み込み
if (file_exists($students_file)) {
    $students = array_map('trim', file($students_file));
} else {
    die("生徒データファイルが見つかりません。");
}

// 条件読み込み
if (file_exists($preferences_file)) {
    $json = file_get_contents($preferences_file);
    $preferences = json_decode($json, true);
} else {
    // 条件ファイルがなければ空配列
    $preferences = [];
}

// 生徒のうち条件がある人とない人に分ける
$students_with_pref = [];
$students_without_pref = [];

foreach ($students as $student) {
    if (isset($preferences[$student]) && !empty($preferences[$student]['wish'])) {
        $students_with_pref[] = $student;
    } else {
        $students_without_pref[] = $student;
    }
}

// 座席表初期化（nullで空席）
$seat_map = array_fill(0, $rows, array_fill(0, $cols, null));

// 全座席リスト
$all_seats = [];
for ($r = 0; $r < $rows; $r++) {
    for ($c = 0; $c < $cols; $c++) {
        $all_seats[] = [$r, $c];
    }
}

// 利用済み席座標リスト
$used_seats = [];

// 条件ある生徒を優先配置
shuffle($students_with_pref);
foreach ($students_with_pref as $student) {
    $pref = $preferences[$student];

    $wish = isset($pref['wish']) ? strtolower($pref['wish']) : "";
    $want = $pref['want'] ?? "";
    $dont = $pref['dont'] ?? "";

    $candidates = $all_seats;

    // 希望席による絞り込み
    if ($wish === 'window') {
        $candidates = array_filter($candidates, function($seat) use ($cols) {
            return $seat[1] === 0 || $seat[1] === $cols - 1;
        });
    } elseif ($wish === 'front') {
        $candidates = array_filter($candidates, function($seat) {
            return $seat[0] === 0;
        });
    } elseif ($wish === 'back') {
        $candidates = array_filter($candidates, function($seat) use ($rows) {
            return $seat[0] === $rows - 1;
        });
    }

    // 利用済み席を除外
    $candidates = array_values(array_filter($candidates, function($seat) use ($used_seats) {
        foreach ($used_seats as $used) {
            if ($used[0] === $seat[0] && $used[1] === $seat[1]) return false;
        }
        return true;
    }));

    // 空席がなければ全空席から選ぶ
    if (empty($candidates)) {
        $candidates = array_values(array_filter($all_seats, function($seat) use ($used_seats) {
            foreach ($used_seats as $used) {
                if ($used[0] === $seat[0] && $used[1] === $seat[1]) return false;
            }
            return true;
        }));
    }

    if (!empty($candidates)) {
        $chosen = $candidates[array_rand($candidates)];
        $seat_map[$chosen[0]][$chosen[1]] = $student;
        $used_seats[] = $chosen;
    }
}

// 残りの生徒をランダム配置
$remaining_students = $students_without_pref;
shuffle($remaining_students);

$remaining_seats = array_values(array_filter($all_seats, function($seat) use ($used_seats) {
    foreach ($used_seats as $used) {
        if ($used[0] === $seat[0] && $used[1] === $seat[1]) return false;
    }
    return true;
}));

foreach ($remaining_students as $i => $student) {
    if (isset($remaining_seats[$i])) {
        [$r, $c] = $remaining_seats[$i];
        $seat_map[$r][$c] = $student;
    }
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8" />
    <title>席替え結果</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f0f0f0; padding: 20px; }
        h1 { text-align: center; margin-bottom: 20px; }
        table { margin: 0 auto; border-collapse: collapse; }
        td {
            border: 1px solid #999;
            width: 120px; height: 60px;
            text-align: center;
            vertical-align: middle;
            background: #fff;
            font-weight: bold;
        }
        .empty-seat {
            color: #aaa;
            font-style: italic;
        }
    </style>
</head>
<body>
    <h1>席替え結果</h1>
    <table>
        <?php foreach ($seat_map as $row): ?>
            <tr>
                <?php foreach ($row as $seat): ?>
                    <td>
                        <?php if ($seat): ?>
                            <?= htmlspecialchars($seat) ?>
                        <?php else: ?>
                            <span class="empty-seat">空席</span>
                        <?php endif; ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
