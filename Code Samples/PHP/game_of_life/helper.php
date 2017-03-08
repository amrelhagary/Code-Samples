<?php


// get adjacent's cells
function get_adjacent_cells($i, $j, $matrix) {
  $adjacent_cells = array();
  $adjacent_cells[0][0] = (isset($matrix[$i-1][$j-1]) ? $matrix[$i-1][$j-1] : 0);
  $adjacent_cells[0][1] = (isset($matrix[$i-1][$j]) ? $matrix[$i-1][$j] : 0);
  $adjacent_cells[0][2] = (isset($matrix[$i-1][$j+1]) ? $matrix[$i-1][$j+1] : 0);

  $adjacent_cells[1][0] = (isset($matrix[$i][$j-1]) ? $matrix[$i][$j-1] : 0);
  // $adjacent_cells[1][1] = (isset($matrix[$i][$j]) ? $matrix[$i][$j] : 0);
  $adjacent_cells[1][2] = (isset($matrix[$i][$j+1]) ? $matrix[$i][$j+1] : 0);

  $adjacent_cells[2][0] = (isset($matrix[$i+1][$j-1]) ? $matrix[$i+1][$j-1] : 0);
  $adjacent_cells[2][1] = (isset($matrix[$i+1][$j]) ? $matrix[$i+1][$j] : 0);
  $adjacent_cells[2][2] = (isset($matrix[$i+1][$j+1]) ? $matrix[$i+1][$j+1] : 0);

  return $adjacent_cells;
}

// count live adjacent cells
function count_lives($adjacent_cells) {
  $count = 0;
  // count one's
  for($i = 0; $i < sizeof($adjacent_cells); $i++) {
    for($j = 0; $j < sizeof($adjacent_cells); $j++) {
      if($adjacent_cells[$i][$j] == 1) {
        $count++;
      }
    }
  }

  return $count;
}

// check if the cell in next population live or dead
function live_or_dead($cell, $count) {
  // 1. Any live cell with fewer than two live neighbors dies, as if caused by under­population.
  if($cell == 1 && $count < 2) {
    return 0;
  }
  // 2. Any live cell with two or three live neighbors lives on to the next generation.
  elseif($cell == 1 && ($count == 2 || $count == 3)) {
    return 1;
  }
  // 3.Any live cell with more than three live neighbors dies, as if by over­population.
  elseif($cell == 1 && $count > 3) {
    return 0;
  }
  // 4.Any dead cell with exactly three live neighbors becomes a live cell, as if by reproduction
  elseif($cell == 0 && $count == 3) {
    return 1;
  }else{
    return 0;
  }
}

// print matrix
function print_matrix($matrix) {
  for($i = 0; $i < sizeof($matrix); $i++) {
    for($j = 0; $j < sizeof($matrix); $j++) {
      echo $matrix[$i][$j] . " ";
    }
    echo "\n";
  }
}