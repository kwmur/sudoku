<?php
session_start();

//define('DEBUG', true);
define('DEBUG', false);

require_once 'Sudoku.class.php';
require_once 'SudokuChecker.class.php';
require_once 'SudokuAnswerer.class.php';

$cells = null;
$messages = [];
$repetitionalIndex = [];

if (isset($_POST['solve'])) {
  // 解答時
  $checker = new SudokuChecker();
  $cells = $_POST['cells'];
  $result = $checker->check($cells);
  $messages = $checker->messages();
  $repetitionalIndex = $checker->repetitionalIndex();
}
else {
  // New Game
  $cells = (new Sudoku)->makeCells();
  unset($_SESSION["read_only_cells"]);
  foreach ($cells as $rowIndex => $rows) {
    foreach ($rows as $columnIndex => $cell) {
      if (isset($cell) && !empty($cell)) {
        $_SESSION["read_only_cells"][$rowIndex][$columnIndex] = true;
      }
      else {
        $_SESSION["read_only_cells"][$rowIndex][$columnIndex] = false;
      }
    }
  }
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>SUDOKU.PHP</title>
  <link rel="stylesheet" type="text/css" href="css/sudoku.css">
</head>
<body>
<h1>SUDOKU.PHP</h1>
<form action="sudoku.php" method="post">
  <table>
    <tr><th></th>
<?php
  for ($i = 0, $count = count($cells); $i < $count; ++$i) {
    echo "<th>" . ($i + 1) . "</th>";
  }
?>
    </tr>
<?php
  foreach ($cells as $rowIndex => $row) {
    echo "<tr><th>" . ($rowIndex + 1)  . "</th>\n";
    foreach ($row as $colIndex => $value) {

      echo '<td';
      if (in_array([$rowIndex, $colIndex], $repetitionalIndex)) {
        // 重複しているところは赤背景
        echo ' style="background-color: #ff55cc;"';
      }
      elseif (floor($rowIndex / 3) == 1 && floor($colIndex / 3) == 1) {
        // DO NOTHING
        // 奇数のグループは白背景
      }
      elseif (floor($rowIndex / 3) == 1 || floor($colIndex / 3) == 1) {
        // 偶数のグループは緑背景
        echo ' style="background-color: #55ffcc;"';
      }
      echo '>';

      echo '<input type="number" name="cells' . "[$rowIndex][$colIndex]" . '" value="' . $value . '" size="1" min="1" max="9" pattern="^[1-9]$" maxlength="1"';
      if ($_SESSION["read_only_cells"][$rowIndex][$colIndex]) {
        echo ' readonly="readonly" class="readonly"';
      }
      echo ' /></td>';
    }
    echo "</tr>";
  }
?>
  </table>
  <input type="submit" name="solve" value="解答する" />
</form>
<form action="sudoku.php" method="get">
  <input type="submit" name="change_question" value="別の問題にする" />
</form>
<?php
  foreach ($messages as $message) {
    echo '<p class="message">' . "$message</p>\n";
  }
?>
<?php
if (DEBUG) {
  echo "<pre>\n";
  var_dump($cells);
  echo "</pre>\n";
}
?>
</body>
</html>

