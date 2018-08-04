<?php

namespace GlebecV;

use SebastianBergmann\Diff\Differ;

class NewDiff
{
    protected $differ;

    //prefixes
    const START_AREA = '@@ @@';
    const OP_MINUS = '-';
    const OP_PLUS = '+';
    const CONTEXT = ' ';
    const OP_ADD_ALL = '~';

    public function __construct()
    {
        $this->differ = new Differ();
    }

    /**
     * @param array|string $src
     * @param array|string $dst
     * @param bool         $reversed
     *
     * @return string
     */
    public function makeDiff($src, $dst, $reversed = false): string
    {
        return $reversed ? $this->differ->diff($dst, $src) : $this->differ->diff($src, $dst);
    }

    /**
     * @param string $src
     * @param string $patch
     *
     * @return mixed|null|string|string[]
     */
    public function patch($src, $patch)
    {
        if (empty($patch)) {
            return $src;
        }
        $src = preg_replace("/(\\r\\n|\\n)/", PHP_EOL, $src);
        $patches = explode("\n", $patch);
        $previous = '';
        $insert = [];

        foreach ($patches as $line) {
            if (0 == strcmp(self::START_AREA, $line)) {
                continue;
            }
            $clearedLine = mb_substr($line, 1, iconv_strlen($line) - 1);
            if (empty($clearedLine)) {
                continue;
            }
            $mode = !empty($src) ? mb_substr($line, 0, 1) : self::OP_ADD_ALL;
            switch ($mode) {
                case self::OP_MINUS:
                    // check if $line is the last in $src
                    if (mb_strpos($src, $clearedLine) + iconv_strlen($clearedLine) === iconv_strlen($src)) {
                        $src = rtrim(str_replace($clearedLine, '', $src));
                    }
                    $src = str_replace($clearedLine . PHP_EOL, '', $src);
                    break;
                case self::OP_ADD_ALL:
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
                $right = rtrim(mb_substr($src, $offset + 1, iconv_strlen($src) - $offset));
                if (empty($right)) {
                    $insertion = rtrim($insertion);
                }
                $src = $left . $insertion . $right;
            }
        }
        return $src;
    }
}