<?php
// ファイルパス設定
$students_file = "data/students.csv";
$conditions_file = "data/conditions.csv";

// POSTで条件が送信された場合
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["selected_name"])) {
    $name = $_POST["selected_name"];
    $seat_preference = $_POST["seat_preference"] ?? "";
    $sit_with = $_POST["sit_with"] ?? "";
    $avoid_with = $_POST["avoid_with"] ?? "";

    // 上書き保存：同じ名前なら上書き、それ以外は追加
    $new_lines = [];
    if (file_exists($conditions_file)) {
        $lines = file($conditions_file, FILE_IGNORE_NEW_LINES);
        foreach ($lines as $line) {
            $cols = str_getcsv($line);
            if ($cols[0] !== $name) {
                $new_lines[] = $line;
            }
        }
    }
    $new_lines[] = "$name,$seat_preference,$sit_with,$avoid_with";
    file_put_contents($conditions_file, implode("\n", $new_lines));
    $message = "条件を保存しました。";
}

// 生徒一覧の取得
$students = [];
if (file_exists($students_file)) {
    $students = file($students_file, FILE_IGNORE_NEW_LINES);
}

// 表示する生徒名
$selected_name = $_POST["selected_name"] ?? "";
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>条件入力 - せきがえメーカーZ</title>
    <style>
        button {
            margin: 5px;
            padding: 8px 16px;
        }
        .form-box {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ccc;
            width: 300px;
        }
    </style>
</head>
<body>
    <h1>生徒の条件を設定</h1>

    <?php if (!empty($message)) : ?>
        <p style="color: green;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    
    <form method="post">
        <?php foreach ($students as $student): ?>
            <button type="submit" name="selected_name" value="<?= htmlspecialchars($student) ?>">
                <?= htmlspecialchars($student) ?>
            </button>
        <?php endforeach; ?>
    </form>

    
    <?php if ($selected_name): ?>
        <div class="form-box">
            <h3><?= htmlspecialchars($selected_name) ?> さんの条件</h3>
            <form method="post">
                <input type="hidden" name="selected_name" value="<?= htmlspecialchars($selected_name) ?>">

                <label>席の希望：</label><br>
                <select name="seat_preference">
                    <option value="">指定なし</option>
                    <option value="前">前</option>
                    <option value="後ろ">後ろ</option>
                    <option value="窓際">窓際</option>
                </select><br><br>

                <label>隣になりたい人：</label><br>
                <select name="sit_with">
                    <option value="">指定なし</option>
                    <?php foreach ($students as $s): ?>
                        <?php if ($s !== $selected_name): ?>
                            <option value="<?= htmlspecialchars($s) ?>"><?= htmlspecialchars($s) ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select><br><br>

                <label>隣になりたくない人：</label><br>
                <select name="avoid_with">
                    <option value="">指定なし</option>
                    <?php foreach ($students as $s): ?>
                        <?php if ($s !== $selected_name): ?>
                            <option value="<?= htmlspecialchars($s) ?>"><?= htmlspecialchars($s) ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select><br><br>

                <button type="submit">条件を保存する</button>
            </form>
        </div>
    <?php endif; ?>
    <form action="result.php" method="get" style="margin-top: 30px;">
    <button type="submit">▶️ 結果を見る</button>
</form>
</body>
</html>
