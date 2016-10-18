<?php

declare(strict_types=1);

class LizardsAndPumpkins_Sniffs_Array_ArrayPushSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * @return int[]
     */
    public function register()
    {
        return [T_STRING];
    }

    /**
     * @param PHP_CodeSniffer_File $file
     * @param int $functionTokenIndex
     */
    public function process(PHP_CodeSniffer_File $file, $functionTokenIndex)
    {
        $tokens = $file->getTokens();

        if ('array_push' !== $tokens[$functionTokenIndex]['content']) {
            return;
        }

        $file->addError('array_push() is disallowed. Use assignment instead.', $functionTokenIndex);
    }
}
