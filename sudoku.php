<?php

function judge()
{
}

function removeGroupRepetition($cells)
{
  // 3*3 ごとのgroup
  for ($group = 0; $group < 3; ++$group) {
    $tmp = [];
    for ($row = 3 * $group; $row < 3 * ($group + 1); ++$row) {
      for ($column = 3 * $group; $column < 3 * ($group + 1); ++$column) {
        if (!in_array($cells[$row][$column], $tmp)) {
          $tmp[] = $cells[$row][$column];
        }
        else {
          $cells[$row][$column] = null;
        }
      }
    }
  }
  return $cells;
}

function removeColumnRepetition($cells)
{
  for ($column = 0; $column < 9; ++$column) {
    $tmp = [];
    for ($row = 0; $row < 9; ++$row) {
      if (!in_array($cells[$row][$column], $tmp)) {
        $tmp[] = $cells[$row][$column];
      }
      else {
        $cells[$row][$column] = null;
      }
    }
  }
  return $cells;
}

function makeCells()
{
  $cells = array();
  for ($i = 0; $i < 9; ++$i) {
    $cells[] = array();
    $range = range(1, 9);

#    printf('$range = ');
#    print_r($range);
#    var_dump($range);

    for ($j = 0; $j < 9; ++$j) {
      if (mt_rand(0, 1)) {
        $index = mt_rand(0, 8);
        $cells[$i][$j] = $range[$index];
        $range[$index] = null; // 1度出現した数字は削除
      }
      else {
        $cells[$i][$j] = null;
      }
    }
  }

//  $cells = removeColumnRepetition($cells);
  $cells = removeGroupRepetition($cells);

  return $cells;
}

#print_r(makeCells());
$cells = makeCells();

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>sudoku</title>
<style TYPE="text/css"> 
<!-- 
body {
  xtext-align: center;
}

table { 
  width: 400px; /* テーブルの横幅 */ 
  border-collapse: collapse; /* 枠線の表示方法 */ 
  border: 1px #1C79C6 solid; /* テーブル全体の枠線（太さ・色・スタイル） */ 
} 

td { 
  border: 1px #1C79C6 solid; /* セルの枠線（太さ・色・スタイル） */ 
} 


input[type=number] {
  text-align: right;
  width: 18px;
}

input[type=number].readonly  {
  background-color: #ddddff;
}
--> 
</style>
</head>
<body>
  <table>
    <tr><th></th>
<?php
  for ($i = 0, $count = count($cells); $i < $count; ++$i) {
    echo "<th>$i</th>";
  }
?>
  </tr>
<?php
  foreach ($cells as $index => $row) {
    echo "<tr><th>" . ($index + 0)  . "</th>";
    foreach ($row as $value) {
      if (isset($value)) {
        echo '<td><input type="number" value="' .  $value . '" size="1" readonly="readonly" class="readonly" /></td>';
      }
      else {
        echo '<td><input type="number" value="' . $value . '" size="1"/></td>';
      }
    }
    echo '</tr>';
  }
?>
  </table>
  <input type="submit" value="Check!" />
  <input type="submit" value="Shuffle !" />
</body>
</html>

