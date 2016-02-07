<?php

class NewDiffTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var NewDiff df
     */
    protected $df;

    public function setUp()
    {
        $this->df = new NewDiff();
    }

    public function testPatch()
    {
        $old = file_get_contents('files/old');
        $new = file_get_contents('files/new');
        $diff = $this->df->makeDiff($old, $new);
        $this->assertEquals($new, $this->df->patch($old, $diff));
    }

}