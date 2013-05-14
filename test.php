<?php


function removeGroupRepetition($cells)
{
  // 3*3 ごとのgroup
  for ($group = 0; $group < 3; ++$group) {
    echo "group = $group\n";
    $tmp = [];
    for ($row = 3 * $group; $row < 3 * ($group + 1); ++$row) {
      echo "	row = $row\n";
      for ($column = 3 * $group; $column < 3 * ($group + 1); ++$column) {
        echo "		column = $column\n";
        if (!in_array($cells[$row][$column], $tmp)) {
          $tmp[] = $cells[$row][$column];
          echo "		if = $column\n";
        }
        else {
          echo "		else = $column\n";
          $cells[$row][$column] = null;
        }
      }
    }
    print_r($tmp);
  }
  return $cells;
}

$cells = [
  [1, 1, 1, 1, 1, 1, 1, 1, 1],
  [1, 1, 1, 1, 1, 1, 1, 1, 1],
  [1, 1, 1, 1, 1, 1, 1, 1, 1],
  [1, 1, 1, 1, 1, 1, 1, 1, 1],
  [1, 1, 1, 1, 1, 1, 1, 1, 1],
  [1, 1, 1, 1, 1, 1, 1, 1, 1],
  [1, 1, 1, 1, 1, 1, 1, 1, 1],
  [1, 1, 1, 1, 1, 1, 1, 1, 1],
  [1, 1, 1, 1, 1, 1, 1, 1, 1]
];

$cells = removeGroupRepetition($cells);
#print_r($cells);

