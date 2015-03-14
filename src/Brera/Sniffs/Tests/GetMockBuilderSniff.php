<?php

class Brera_Sniffs_Tests_GetMockBuilderSniff implements PHP_CodeSniffer_Sniff
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
     * @param int $tokenIndex
     */
    public function process(PHP_CodeSniffer_File $file, $tokenIndex)
    {
        $tokens = $file->getTokens();

        if ('getMockBuilder' !== $tokens[$tokenIndex]['content']) {
            return;
        }

        $nextStringTokenIndex = $file->findNext(T_STRING, $tokenIndex + 1);
        if ('disableOriginalConstructor' !== $tokens[$nextStringTokenIndex]['content']) {
            return;
        }

        $nextStringTokenIndex = $file->findNext(T_STRING, $nextStringTokenIndex + 1);
        if ('getMock' !== $tokens[$nextStringTokenIndex]['content']) {
            return;
        }

        $file->addError(
            'getMock(Foo::class, [], [], \'\', false) must be used for disabling original constructor',
            $tokenIndex
        );
    }
}
