<?php

function judge()
{
}



function makeCells()
{
  $cells = array();
  for ($i = 0; $i < 10; ++$i) {
    $cells[] = array();
    for ($j = 0; $j < 10; ++$j) {
      #$cells[$i][$j] = mt_rand(0, 9);
      $cells[$i][$j] = mt_rand(0, 1) ? mt_rand(0, 9) : null;
    }
  }

  return $cells;
}

print_r(makeCells());



