<?php

declare(strict_types=1);

class LizardsAndPumpkins_Sniffs_PHPDoc_CloseLineSniff implements PHP_CodeSniffer_Sniff
{
    use LizardsAndPumpkins_Traits_PHPDocSniffTrait;

    /**
     * @return int[]
     */
    public function register()
    {
        return [T_FUNCTION];
    }

    /**
     * @param PHP_CodeSniffer_File $file
     * @param int $functionTokenIndex
     */
    public function process(PHP_CodeSniffer_File $file, $functionTokenIndex)
    {
        $this->file = $file;
        $this->tokens = $file->getTokens();

        if (!$this->phpDocExists($functionTokenIndex)) {
            return;
        }

        if ($this->isThereAnyContentBeforeClosingPHPDocToken($functionTokenIndex)) {
            $this->file->addError(
                'The close PHPDoc tag must be the only content on the line',
                $this->getPhpDocEndTokenIndex($functionTokenIndex)
            );
        }
    }

    private function isThereAnyContentBeforeClosingPHPDocToken(int $functionTokenIndex) : bool
    {
        $commentCloseTokenIndex = $this->getPhpDocEndTokenIndex($functionTokenIndex);

        if ('*/' !== $this->tokens[$commentCloseTokenIndex]['content']) {
            return true;
        }

        $previousIndex = $this->file->findPrevious([T_DOC_COMMENT_WHITESPACE], $commentCloseTokenIndex - 1, null, true);

        return $this->tokens[$previousIndex]['line'] === $this->tokens[$commentCloseTokenIndex]['line'];
    }
}
