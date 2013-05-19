<?php

/**
 * 解答をチェックするクラス
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
  public function checkRow($cells)
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
       if (DEBUG) print_r($counter);

      // 同じ数字が2回以上出ていたら該当のインデックスを取って置く
      foreach ($row as $columnIndex => $cell) {
        if (!$this->blankp($cell)) {
          if ($counter[$cell] > 1) {
            $repetitionalIndex[] = [$rowIndex, $columnIndex];
          }
        }
      }
    }

    if (DEBUG) print_r($this->repetitionalIndex);
    if (DEBUG) print_r($repetitionalIndex);
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
  public function checkColumn($cells)
  {
    $repetitionalIndex = [];
    for ($columnIndex = 0; $columnIndex < 9; ++$columnIndex) {
      // 1-9まで各数字ごとの出現回数をカウントする
      $counter = [];
      for ($rowIndex = 0; $rowIndex < 9; ++$rowIndex) {
        if (!isset($cells[$rowIndex][$columnIndex])) {
          continue;
        }
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
        if (!isset($cells[$rowIndex][$columnIndex])) {
          continue;
        }
        $cell = $cells[$rowIndex][$columnIndex];
        if (!$this->blankp($cell)) {
          if ($counter[$cell] > 1) {
            $repetitionalIndex[] = [$rowIndex, $columnIndex];
          }
        }
      }
    }

    if (DEBUG) print_r($this->repetitionalIndex);
    if (DEBUG) print_r($repetitionalIndex);
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
  public function checkGroup($cells)
  {
    $repetitionalIndex = [];
    // 3*3 ごとのgroup
    for ($rowGroup = 0; $rowGroup < 3; ++$rowGroup) {
      for ($columnGroup = 0; $columnGroup < 3; ++$columnGroup) {
        // 1-9まで各数字ごとの出現回数をカウントする
        $counter = [];
        for ($rowIndex = 3 * $rowGroup; $rowIndex < 3 * ($rowGroup + 1); ++$rowIndex) {
          for ($columnIndex = 3 * $columnGroup; $columnIndex < 3 * ($columnGroup + 1); ++$columnIndex) {
            if (!isset($cells[$rowIndex][$columnIndex])) {
              continue;
            }
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
            if (!isset($cells[$rowIndex][$columnIndex])) {
              continue;
            }
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

    if (DEBUG) print_r($this->repetitionalIndex);
    if (DEBUG) print_r($repetitionalIndex);
    if (count($repetitionalIndex) == 0) {
      return true;
    }

    $this->repetitionalIndex = array_merge($this->repetitionalIndex, $repetitionalIndex);
    $this->messages[] = "ブロックに同じ数字があります。";
    return false;
  }

}

