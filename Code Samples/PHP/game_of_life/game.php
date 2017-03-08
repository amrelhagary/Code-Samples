<?php
require_once  "helper.php";


if($argc != 2) {
  echo "you must provide input file \n";
  exit;
}

$file = $argv[1];

if(!file_exists($file)) {
  echo "file not exits \n";
  exit;
}

$handle = fopen($file, "r");
if($handle) {
  $matrix = array();
  $c = 0;
  // read the file and populate the values into a matrix
  while(($line = fgets($handle)) != false) {
    $line = trim($line);
    $row = str_split($line);
    $matrix[$c] = $row;
    $c++;
  }

  // next population live of dead
  $next_matrix = array();

  // loop through the matrix
  for($i = 0; $i < sizeof($matrix); $i++) {
    for($j = 0; $j < sizeof($matrix); $j++) {
      // get each cell
      $cell = (int) $matrix[$i][$j];
      // get adjacent's cells
      $adjacent = get_adjacent_cells($i, $j, $matrix);
      // compute current cell live or dead
      $live_or_dead = live_or_dead($cell, count_lives($adjacent));
      // populate the new value in the new matrix
      $next_matrix[$i][$j] = $live_or_dead;
    }
  }

  fclose($handle);


  print_matrix($matrix);
  echo "-------------------- \n";
  print_matrix($next_matrix);
}
else{
  echo "error opening the file \n";
  exit;
}