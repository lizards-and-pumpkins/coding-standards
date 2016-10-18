<?php

declare(strict_types=1);

class LizardsAndPumpkins_Sniffs_Tests_GetMockBuilderSniff implements PHP_CodeSniffer_Sniff
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

        $nextStringTokenIndex = $this->getNextStringTokenIndexIgnoringMethodArguments($file, $tokenIndex);
        if ('disableOriginalConstructor' !== $tokens[$nextStringTokenIndex]['content']) {
            return;
        }

        $nextStringTokenIndex = $this->getNextStringTokenIndexIgnoringMethodArguments($file, $nextStringTokenIndex);
        if ('getMock' !== $tokens[$nextStringTokenIndex]['content']) {
            return;
        }

        $file->addError(
            'createMock() must be used for disabling original constructor',
            $tokenIndex
        );
    }

    /**
     * @param PHP_CodeSniffer_File $file
     * @param int $fromTokenIndex
     * @return bool|int
     */
    private function getNextStringTokenIndexIgnoringMethodArguments(PHP_CodeSniffer_File $file, int $fromTokenIndex)
    {
        $nextClosingParenthesesTokenIndex = $file->findNext(T_CLOSE_PARENTHESIS, $fromTokenIndex + 1);
        return $file->findNext(T_STRING, $nextClosingParenthesesTokenIndex + 1);
    }
}
