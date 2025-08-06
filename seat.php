<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$cols = 0;
$rows = 0;
$show_table = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $cols = intval($_POST["cols"]);
    $rows = intval($_POST["rows"]);
    if ($cols > 0 && $rows > 0) {
        $show_table = true;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>席の設定 - せきがえメーカーZ</title>
    <style>
        table {
            border-collapse: collapse;
            margin-top: 20px;
        }
        td {
            border: 1px solid #999;
            width: 60px;
            height: 60px;
            text-align: center;
        }
        .button {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>席の設定</h1>

    <form method="POST">
        <label>列数（横）: <input type="number" name="cols" value="<?= htmlspecialchars($cols) ?>" min="1" required></label>
        <br>
        <label>行数（縦）: <input type="number" name="rows" value="<?= htmlspecialchars($rows) ?>" min="1" required></label>
        <br>
        <button type="submit">プレビュー</button>
    </form>

    <?php if ($show_table): ?>
        <h2>席のプレビュー</h2>
        <table>
            <?php for ($r = 0; $r < $rows; $r++): ?>
                <tr>
                    <?php for ($c = 0; $c < $cols; $c++): ?>
                        <td><?= $r + 1 ?>-<?= $c + 1 ?></td>
                    <?php endfor; ?>
                </tr>
            <?php endfor; ?>
        </table>

        <div class="button">
            <form action="preference_input.php" method="GET">
                <button type="submit">条件を設定する</button>
            </form>
        </div>
    <?php endif; ?>
</body>
</html>

