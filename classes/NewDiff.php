<?php

class NewDiff
{
    protected $differ;

    //prefixes
    const START_AREA = '@@ @@';
    const OP_MINUS = '-';
    const OP_PLUS = '+';
    const CONTEXT = ' ';

    public function __construct()
    {
        $this->differ = new \SebastianBergmann\Diff\Differ('', true);
    }

    public function makeDiff($src, $dst, $reversed = false)
    {
        return $reversed ? $this->differ->diff($dst, $src) : $this->differ->diff($src, $dst);
    }

    public function patch($src, $patch)
    {
        $src = preg_replace("/(\\r\\n|\\n)/", PHP_EOL, $src);
        $patches = explode("\n", $patch);
        $previous = '';
        $insert = [];
        foreach ($patches as $line) {
            if (0 == strcmp(self::START_AREA, $line)) {
                continue;
            }
            $clearedLine = mb_substr($line, 1, iconv_strlen($line) - 1);
            switch (mb_substr($line, 0, 1)) {
                case self::OP_MINUS:
                    // check if $line is the first in $src
                    if (mb_strpos($src, $clearedLine) + iconv_strlen($clearedLine) === iconv_strlen($src)) {
                        $src = str_replace($clearedLine, '', $src);
                    }
                    $src = str_replace($clearedLine . PHP_EOL, '', $src);
                    break;
                case self::OP_PLUS:
                    if (empty($previous)) {
                        $src = $clearedLine . PHP_EOL . $src;
                    }
                    else {
                        $insert[$previous] = $clearedLine . PHP_EOL;
                    }
                    $previous = $clearedLine;
                    break;
                case self::CONTEXT:
                    $previous = $clearedLine;
            }
        }
        if (0 != count($insert)) {
            foreach ($insert as $strAfter => $insertion) {
                $offset = mb_strpos($src, $strAfter) + iconv_strlen($strAfter) +1;
                $left = rtrim(mb_substr($src, 0, $offset)) . PHP_EOL;
                $right = mb_substr($src, $offset + 1, iconv_strlen($src) - $offset);
                $src = $left . $insertion . $right;
            }
        }
        return $src;
    }
}