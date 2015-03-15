<?php

class Brera_Sniffs_PHPDoc_UselessAnnotationSniff implements PHP_CodeSniffer_Sniff
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

        if ($this->isTest($functionTokenIndex)) {
            return;
        }

        $commentEndIndex = $this->getPhpDocEndTokenIndex($functionTokenIndex);
        $currentIndex = $this->tokens[$commentEndIndex]['comment_opener'];

        $currentLineNumber = $this->tokens[$currentIndex]['line'];
        $searchTypes = [T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_STAR];
        $allowedAnnotations = ['@see', '@param', '@return', '@throws'];

        while ($currentIndex = $this->file->findNext($searchTypes, $currentIndex + 1, $commentEndIndex - 1, true)) {
            if ($currentLineNumber !== $this->tokens[$currentIndex]['line']) {

                if (1 < $this->tokens[$currentIndex]['line'] - $currentLineNumber) {
                    $firstTokenIndex = $this->file->findFirstOnLine(T_DOC_COMMENT_WHITESPACE, $currentIndex);
                    $this->file->addError('PHPDoc must not contain blank lines', $firstTokenIndex - 1);
                }

                if (!in_array($this->tokens[$currentIndex]['content'], $allowedAnnotations)) {
                    $this->file->addError('PHPDoc must not contain useless lines', $currentIndex);
                }

                $currentLineNumber = $this->tokens[$currentIndex]['line'];
            }
        }

    }
}
