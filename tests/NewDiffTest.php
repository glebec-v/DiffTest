<?php

include __DIR__.'../vendor/autoload.php';

use GlebecV\NewDiff;

class NewDiffTest extends PHPUnit\Framework\TestCase
{
    /**
     * @var NewDiff df
     */
    protected $df;

    public function setUp()
    {
        $this->df = new NewDiff();
    }

    public function testPatchSimpleTextForward()
    {
        $old = file_get_contents('files/old');
        $new = file_get_contents('files/new');
        $diff = $this->df->makeDiff($old, $new);
        $this->assertEquals($new, $this->df->patch($old, $diff));
    }

    public function testPatchSimpleTextBack()
    {
        $old = file_get_contents('files/old');
        $new = file_get_contents('files/new');
        $diff = $this->df->makeDiff($old, $new, true);
        $this->assertEquals($old, $this->df->patch($new, $diff));
    }

    public function arrays()
    {
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
        $dt1 = json_encode($data1, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $dt2 = json_encode($data2, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        return [
            'forward' => [$dt1, $dt2],
            'backward' => [$dt2, $dt1],
        ];
    }

    /**
     * @dataProvider arrays
     */
    public function testPatchSimpleArray($a, $b)
    {
        $diff = $this->df->makeDiff($a, $b);
        $this->assertSame($b, str_replace(PHP_EOL, "\n", $this->df->patch($a, $diff)));
        // todo CRLF???? in NewDiff...
    }

    public function testPatchWithEmptySrc()
    {
        $old = '';
        $new = file_get_contents('files/new');
        $diff = $this->df->makeDiff($old, $new);
        $this->assertEquals($new, $this->df->patch($old, $diff));
        $diff = $this->df->makeDiff($old, $new, true);
        $this->assertEquals($old, $this->df->patch($new, $diff));
    }

}