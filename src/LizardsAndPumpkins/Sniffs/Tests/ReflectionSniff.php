<?php

class LizardsAndPumpkins_Sniffs_Tests_ReflectionSniff implements PHP_CodeSniffer_Sniff
{
    private $reflectionClasses = ['ReflectionClass', 'ReflectionMethod', 'ReflectionProperty'];

    /**
     * @return int[]
     */
    public function register() : array
    {
        return [T_STRING];
    }

    /**
     * @param PHP_CodeSniffer_File $file
     * @param int $tokenIndex
     */
    public function process(PHP_CodeSniffer_File $file, $tokenIndex)
    {
        $tokens = $file->getTokens();

        if (in_array($tokens[$tokenIndex]['content'], $this->reflectionClasses)) {
            $file->addWarning('Reflection usage detected.', $tokenIndex);
        }
    }
}
