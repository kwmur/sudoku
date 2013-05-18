<?php
session_start();

define('DEBUG', true);
//define('DEBUG', false);


/**
 * Sudokuの9*9マスを作成するためのクラス
 *
 */
class Sudoku {

  private $cells = [];

  /**
   * ランダムに数字が入力されている9*9のマスを作成し返す
   *
   * @return 9*9の配列
   */
  public function makeCells()
  {
    $this->cells = [];
    for ($i = 0; $i < 9; ++$i) {
      $this->cells[] = [];
      $range = range(1, 9);

      for ($j = 0; $j < 9; ++$j) {
        if (mt_rand(0, 1)) {
          $index = mt_rand(0, 8);
          $this->cells[$i][$j] = $range[$index];
          $range[$index] = null; // 1度出現した数字は削除
        }
        else {
          $this->cells[$i][$j] = null;
        }
      }
    }

    $this->removeGroupRepetition();
    $this->removeColumnRepetition();

    return $this->cells;
  }

  /**
   * 3*3のブロック内に同じ数字があったら削除する
   *
   */
  private function removeGroupRepetition()
  {
    // 3*3 ごとのgroup
    for ($rowGroup = 0; $rowGroup < 3; ++$rowGroup) {
      for ($colGroup = 0; $colGroup < 3; ++$colGroup) {
        $tmp = [];
        for ($row = 3 * $rowGroup; $row < 3 * ($rowGroup + 1); ++$row) {
          for ($column = 3 * $colGroup; $column < 3 * ($colGroup + 1); ++$column) {
            if (in_array($this->cells[$row][$column], $tmp)) {
              $this->cells[$row][$column] = null;
            }
            else {
              $tmp[] = $this->cells[$row][$column];
            }
          }
        }
      }
    }
  }

  /**
   * 縦の一列内に同じ数字があったら削除する
   *
   */
  private function removeColumnRepetition()
  {
    for ($column = 0; $column < 9; ++$column) {
      $tmp = [];
      for ($row = 0; $row < 9; ++$row) {
        if (in_array($this->cells[$row][$column], $tmp)) {
          $this->cells[$row][$column] = null;
        }
        else {
          $tmp[] = $this->cells[$row][$column];
        }
      }
    }
  }
}

/**
 * 9*9のセルをチェックする
 *
 */
class SudokuChecker {

  private $messages = [];

  private $repetitionalIndex = [];

  /**
   * 作成した画面表示用メッセージを返す
   *
   * @return メッセージの配列
   */
  public function messages()
  {
    return $this->messages;
  }

  /**
   * 同じ数字が入力されているインデックスを返す
   *
   * @return indexの配列
   */
  public function repetitionalIndex()
  {
    return $this->repetitionalIndex;
  }

  /**
   * 解答のチェックを実施し、結果を返す
   *
   * @return 正解の場合true、誤りの場合false
   */
  public function check($cells)
  {
    $result = true;
    if (!$this->checkBlank($cells)) {
      $result = false;
    }
    if (!$this->checkRow($cells)) {
      $result = false;
    }
    if (!$this->checkColumn($cells)) {
      $result = false;
    }
    if (!$this->checkGroup($cells)) {
      $result = false;
    }

    if ($result) {
      $this->messages[] = '☆★正解！！☆★';
    }

    return $result;
  }

  /**
   * 数字が入力されていないマスがあるかチェックする
   *
   * @param $cells 9*9の配列
   */
  private function checkBlank($cells)
  {
    foreach ($cells as $row) {
      foreach ($row as $column) {
        if ($this->blankp($column)) {
          $this->messages[] = "空欄があります。全ての欄に1～9までの数字を入力してください。";
          return false;
        }
      }
    }
    return true;
  }

  /**
   * 渡されたマスが空か調べて結果を返す
   *
   * @param $item 1マス
   * @return 空欄の場合はtrueを返す、空欄ではない場合はfalseを返す
   */
  private function blankp($item)
  {
    return !(isset($item) && !empty($item));
  }

  /**
   * 行に数字の重複がないかチェックする
   *
   * @param $cells 9*9の配列
   * @return 同じ数字があったらfalse、同じ数字がなかければtrue
   */
  private function checkRow($cells)
  {
    $repetitionalIndex = [];
    foreach ($cells as $rowIndex => $row) {
      // 1-9まで各数字ごとの出現回数をカウントする
      $counter = [];
      foreach ($row as $columnIndex => $cell) {
        if (!$this->blankp($cell)) {
          if (isset($counter[$cell])) {
            $counter[$cell]++;
          }
          else {
            $counter[$cell] = 1;
          }
        }
      }
      // if (DEBUG) print_r($counter);

      // 同じ数字が2回以上出ていたら該当のインデックスを取って置く
      foreach ($row as $columnIndex => $cell) {
        if (!$this->blankp($cell)) {
          if ($counter[$cell] > 1) {
            $repetitionalIndex[] = [$rowIndex, $columnIndex];
          }
        }
      }
    }

    //if (DEBUG) print_r($this->repetitionalIndex);
    //if (DEBUG) print_r($repetitionalIndex);
    if (count($repetitionalIndex) == 0) {
      return true;
    }

    $this->repetitionalIndex = array_merge($this->repetitionalIndex, $repetitionalIndex);
    $this->messages[] = "行に同じ数字があります。";
    return false;
  }

  /**
   * 列に数字の重複がないかチェックする
   *
   * @param $cells 9*9の配列
   * @return 同じ数字があったらfalse、同じ数字がなかければtrue
   */
  private function checkColumn($cells)
  {
    $repetitionalIndex = [];
    for ($columnIndex = 0; $columnIndex < 9; ++$columnIndex) {
      // 1-9まで各数字ごとの出現回数をカウントする
      $counter = [];
      for ($rowIndex = 0; $rowIndex < 9; ++$rowIndex) {
        $cell = $cells[$rowIndex][$columnIndex];

        if (!$this->blankp($cell)) {
          if (isset($counter[$cell])) {
            $counter[$cell]++;
          }
          else {
            $counter[$cell] = 1;
          }
        }
      }

      // 同じ数字が2回以上出ていたら該当のインデックスを取って置く
      for ($rowIndex = 0; $rowIndex < 9; ++$rowIndex) {
        $cell = $cells[$rowIndex][$columnIndex];
        if (!$this->blankp($cell)) {
          if ($counter[$cell] > 1) {
            $repetitionalIndex[] = [$rowIndex, $columnIndex];
          }
        }
      }
    }

    //if (DEBUG) print_r($this->repetitionalIndex);
    //if (DEBUG) print_r($repetitionalIndex);
    if (count($repetitionalIndex) == 0) {
      return true;
    }

    $this->repetitionalIndex = array_merge($this->repetitionalIndex, $repetitionalIndex);
    $this->messages[] = "列に同じ数字があります。";
    return false;
  }


  /**
   * 3*3のブロックに数字の重複がないかチェックする
   *
   * @param $cells 9*9の配列
   * @return 同じ数字があったらfalse、同じ数字がなかければtrue
   */
  private function checkGroup($cells)
  {
    $repetitionalIndex = [];
    // 3*3 ごとのgroup
    for ($rowGroup = 0; $rowGroup < 3; ++$rowGroup) {
      for ($columnGroup = 0; $columnGroup < 3; ++$columnGroup) {
        // 1-9まで各数字ごとの出現回数をカウントする
        $counter = [];
        for ($rowIndex = 3 * $rowGroup; $rowIndex < 3 * ($rowGroup + 1); ++$rowIndex) {
          for ($columnIndex = 3 * $columnGroup; $columnIndex < 3 * ($columnGroup + 1); ++$columnIndex) {
            $cell = $cells[$rowIndex][$columnIndex];
            if (!$this->blankp($cell)) {
              if (isset($counter[$cell])) {
                $counter[$cell]++;
              }
              else {
                $counter[$cell] = 1;
              }
            }
          }
        }

        // 同じ数字が2回以上出ていたら該当のインデックスを取って置く
        for ($rowIndex = 3 * $rowGroup; $rowIndex < 3 * ($rowGroup + 1); ++$rowIndex) {
          for ($columnIndex = 3 * $columnGroup; $columnIndex < 3 * ($columnGroup + 1); ++$columnIndex) {
            $cell = $cells[$rowIndex][$columnIndex];
            if (!$this->blankp($cell)) {
              if ($counter[$cell] > 1) {
                $repetitionalIndex[] = [$rowIndex, $columnIndex];
              }
            }
          }
        }
      }
    }

    //if (DEBUG) print_r($this->repetitionalIndex);
    //if (DEBUG) print_r($repetitionalIndex);
    if (count($repetitionalIndex) == 0) {
      return true;
    }

    $this->repetitionalIndex = array_merge($this->repetitionalIndex, $repetitionalIndex);
    $this->messages[] = "ブロックに同じ数字があります。";
    return false;
  }

}


class SudokuAnswerer {

  public function answer($cells, $readOnlyCells)
  {

  }

}


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
elseif (isset($_POST['show_answer'])) {
  // TODO 解答表示機能を作成する
  $answerer = new SudokuAnswerer();
  $cells = $answerer->answer($_POST['cells'], $_SESSION["read_only_cells"]);
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
<style TYPE="text/css"> 
<!-- 
body {
  padding-left: 40px;
}

table {
  border-collapse: collapse; /* 枠線の表示方法 */ 
  border: 1px #1C79C6 solid; /* テーブル全体の枠線（太さ・色・スタイル） */ 
}

th {
  background-color: #dddddd;
  border: 1px #1C79C6 solid;
  width: 35px;
  height: 35px;
}

td {
  border: 1px #1C79C6 solid; /* セルの枠線（太さ・色・スタイル） */ 
  xpadding 5px;
}

input[type=number] {
  text-align: right;
  width: 28px;
  height: 25px;
  margin: 5px;
}

input[type=number].readonly  {
  background-color: #ddddff;
}
--> 
</style>
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
  <input type="submit" name="show_answer" style="float: right;" value="解くのは無理。答えを見る。" />
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
<pre><?php var_dump($cells); ?></pre>
</body>
</html>

