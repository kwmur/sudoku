<?php


function removeGroupRepetition($cells)
{
  // 3*3 ごとのgroup
  for ($rowGroup = 0; $rowGroup < 3; ++$rowGroup) {
    echo "rowGroup = $rowGroup\n";
    echo "	row = $row\n";
    for ($colgroup = 0; $colgroup < 3; ++$colgroup) {
      $tmp = [];
      for ($row = 3 * $rowGroup; $row < 3 * ($rowGroup + 1); ++$row) {
        for ($column = 3 * $colgroup; $column < 3 * ($colgroup + 1); ++$column) {
          echo "		column = $column\n";
          if (in_array($cells[$row][$column], $tmp)) {
            echo "		if = $column\n";
            $cells[$row][$column] = null;
          }
          else {
            echo "		else = $column\n";
            $tmp[] = $cells[$row][$column];
          }
        }
      }
    }
    print_r($tmp);
  }
  return $cells;
}

$cells = [
  [1, 1, 1, 2, 2, 2, 3, 3, 3],
  [1, 1, 1, 2, 2, 2, 3, 3, 3],
  [1, 1, 1, 2, 2, 2, 3, 3, 3],
  [1, 1, 1, 1, 1, 1, 1, 1, 1],
  [1, 1, 1, 1, 1, 1, 1, 1, 1],
  [1, 1, 1, 1, 1, 1, 1, 1, 1],
  [1, 1, 1, 1, 1, 1, 1, 1, 1],
  [1, 1, 1, 1, 1, 1, 1, 1, 1],
  [1, 1, 1, 1, 1, 1, 1, 1, 1]
];

$cells = removeGroupRepetition($cells);
print_r($cells);

