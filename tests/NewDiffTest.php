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