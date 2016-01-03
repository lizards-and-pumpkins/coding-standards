<?php

class LizardsAndPumpkins_Sniffs_Variable_EmptySniff implements PHP_CodeSniffer_Sniff
{
    /**
     * @return int[]
     */
    public function register()
    {
        return [T_EMPTY];
    }

    /**
     * @param PHP_CodeSniffer_File $file
     * @param int $tokenIndex
     */
    public function process(PHP_CodeSniffer_File $file, $tokenIndex)
    {
        $file->addError('empty() is disallowed, please use explicit comparison instead.', $tokenIndex);
    }
}
