<?php

class Brera_Sniffs_PHPDoc_OpenLineSniff implements PHP_CodeSniffer_Sniff
{
    use Brera_Traits_PHPDocSniffTrait;

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

        if ($this->isThereAnyContentAfterOpenPHPDocToken($functionTokenIndex)) {
            $commentEndIndex = $this->getPhpDocEndTokenIndex($functionTokenIndex);
            $file->addError(
                'The open PHPDoc tag must be the only content on the line',
                $this->tokens[$commentEndIndex]['comment_opener']
            );
        }
    }

    private function isThereAnyContentAfterOpenPHPDocToken($functionTokenIndex)
    {
        $commentEndIndex = $this->getPhpDocEndTokenIndex($functionTokenIndex);
        $commentStartIndex = $this->tokens[$commentEndIndex]['comment_opener'];

        $searchType = [T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_STAR];
        $nextNotEmptyTokenIndex = $this->file->findNext($searchType, $commentStartIndex + 1, $commentEndIndex, true);

        return $this->tokens[$nextNotEmptyTokenIndex]['line'] === $this->tokens[$commentStartIndex]['line'];
    }
}
