<?php

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

