<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');

date_default_timezone_set('Asia/Shanghai');

require '../Plus/Plus.php';

$plus = Plus::getInstance();


//$benchmark = new Plus_Com_Benchmark();

$plus->boot()->dispatch();

//echo "<br />cost:", $benchmark->cost(), 's';