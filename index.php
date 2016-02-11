<?php
require __DIR__.'/vendor/autoload.php';
error_reporting(E_ALL);
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
$data1 = [
    "фрукты"  => [
        "a" => "апельсин",
        "b" => "банан",
        "c" => "яблоко",
        "d" => "абрикос",
        "e" => "гранат"
    ],
    "числа"   => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
    "дырки"   => [
        "первая",
        5 => "вторая",
        "третья"
    ]];
$data2 = [
    "овощи"  => [
        "a" => "картошка",
        "b" => "банан",
        "c" => "арбуз",
        "d" => "абрикос",
        "e" => "гранат"
    ],
    "числа"   => [1, 2, 3, 5, 6, 7, 8, 9, 10, 11],
    "дырки"   => [
        "первая",
        4 => "вторая",
        'третья' => 'бублик'
    ]];

$dataOld = json_encode($data1, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
$dataNew = json_encode($data2, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

// ----------------------

$d = new NewDiff();

$old = ''; //file_get_contents('files/old');
$new = file_get_contents('files/new');

$ts = microtime_float();
$dif = $d->makeDiff($dataOld, $dataNew);
$tf1 = microtime_float();
$result = $d->patch($dataOld, $dif);
$tf2 = microtime_float();

echo 'diff = '.($tf1-$ts).' sec'.'<br/>';
echo 'patch = '.($tf2-$tf1).' sec'.'<br/>';
echo '<br/>';
echo $result;