<?php
$a = 0.58;
var_dump($a);
echo json_encode(['b' => $a]);
$c = 9.42;
$b = 10.00;
$b -= $c;
var_dump($b);
echo json_encode(['b' => $b]);