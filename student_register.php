<?php
$csvFile = __DIR__ . "/data/students.csv";
$error = "";

// POSTで名前登録処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    if ($name === '') {
        $error = "名前を入力してください。";
    } else {
        $students = file_exists($csvFile) ? file($csvFile, FILE_IGNORE_NEW_LINES) : [];
        if (in_array($name, $students)) {
            $error = "この名前はすでに登録されています。";
        } else {
            file_put_contents($csvFile, $name . PHP_EOL, FILE_APPEND);
            header("Location: student_register.php");
            exit;
        }
    }
}

$students = file_exists($csvFile) ? file($csvFile, FILE_IGNORE_NEW_LINES) : [];
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8" />
    <title>生徒登録 - せきがえメーカーZ</title>
</head>
<body>
    <h1>生徒登録</h1>

    <form method="post">
        <input type="text" name="name" placeholder="名前を入力" required>
        <button type="submit">登録</button>
    </form>

    <?php if ($error): ?>
        <p style="color:red"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <h2>登録済みの生徒一覧</h2>
    <ul>
        <?php foreach ($students as $student): ?>
            <li><?= htmlspecialchars($student) ?></li>
        <?php endforeach; ?>
    </ul>

    <form action="seat.php" method="get">
        <button type="submit">席配置設定へ進む</button>
    </form>
</body>
</html>
