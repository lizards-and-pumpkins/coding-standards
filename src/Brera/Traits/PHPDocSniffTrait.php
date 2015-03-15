<?php

trait Brera_Traits_PHPDocSniffTrait
{
    /**
     * @var PHP_CodeSniffer_File
     */
    protected $file;

    /**
     * @var mixed[]
     */
    protected $tokens;

    /**
     * @param int $functionTokenIndex
     * @return bool
     */
    protected function isTest($functionTokenIndex)
    {
        if (!$this->phpDocExists($functionTokenIndex)) {
            return false;
        }

        $functionPHPDocAnnotationsIndices = $this->getPHPDocAnnotationsIndices($functionTokenIndex);

        if (empty($functionPHPDocAnnotationsIndices)) {
            return false;
        }

        return '@test' === $this->tokens[$functionPHPDocAnnotationsIndices[0]]['content'];
    }

    /**
     * @param int $functionTokenIndex
     * @return bool
     */
    protected function phpDocIsRequired($functionTokenIndex)
    {
        return $this->functionReturnsNonVoid($functionTokenIndex) ||
               $this->functionHasParameters($functionTokenIndex) ||
               $this->functionThrowsAnException($functionTokenIndex);
    }

    /**
     * @param $functionTokenIndex
     * @return bool
     */
    protected function phpDocExists($functionTokenIndex)
    {
        $commentEndIndex = $this->getPhpDocEndTokenIndex($functionTokenIndex);

        return T_DOC_COMMENT_CLOSE_TAG === $this->tokens[$commentEndIndex]['code'];
    }

    /**
     * @param int $functionTokenIndex
     * @return bool|int
     */
    protected function getPhpDocEndTokenIndex($functionTokenIndex)
    {
        $searchTypes = array_merge(PHP_CodeSniffer_Tokens::$methodPrefixes, [T_WHITESPACE]);

        return $this->file->findPrevious($searchTypes, $functionTokenIndex - 1, null, true);
    }

    /**
     * @param int $functionTokenIndex
     * @return int[]
     */
    protected function getPHPDocAnnotationsIndices($functionTokenIndex)
    {
        $commentEndIndex = $this->getPhpDocEndTokenIndex($functionTokenIndex);
        $commentStartIndex = $this->tokens[$commentEndIndex]['comment_opener'];

        if (!isset($this->tokens[$commentStartIndex]['comment_tags'])) {
            return [];
        }

        return $this->tokens[$commentStartIndex]['comment_tags'];
    }

    /**
     * @param int $functionTokenIndex
     * @return bool
     */
    private function functionReturnsNonVoid($functionTokenIndex)
    {
        if ($this->isInterfaceOrAbstractFunction($functionTokenIndex)) {
            return true;
        }

        $nonVoidReturnFound = false;

        $scopeOpenerIndex = $this->tokens[$functionTokenIndex]['scope_opener'];
        $scopeCloserIndex = $this->tokens[$functionTokenIndex]['scope_closer'];

        $currentIndex = $scopeOpenerIndex;

        while (!$nonVoidReturnFound && ($returnIndex = $this->getNextReturnIndex($currentIndex, $scopeCloserIndex))) {
            $currentIndex = $returnIndex;

            if ($nextNonEmptyTokenIndex = $this->getNextNonEmptyTokenIndex($returnIndex + 1)) {
                $nonVoidReturnFound = T_SEMICOLON !== $this->tokens[$nextNonEmptyTokenIndex]['code'];
            }
        }

        return $nonVoidReturnFound;
    }

    /**
     * @param int $functionTokenIndex
     * @return bool
     */
    private function functionHasParameters($functionTokenIndex)
    {
        $functionParameters = $this->file->getMethodParameters($functionTokenIndex);

        return !empty($functionParameters);
    }

    /**
     * @param $functionTokenIndex
     * @return bool
     */
    private function functionThrowsAnException($functionTokenIndex)
    {
        if ($this->isInterfaceOrAbstractFunction($functionTokenIndex)) {
            return true;
        }

        $scopeOpenerIndex = $this->tokens[$functionTokenIndex]['scope_opener'];
        $scopeCloserIndex = $this->tokens[$functionTokenIndex]['scope_closer'];

        return false !== $this->file->findNext(T_THROW, $scopeOpenerIndex + 1, $scopeCloserIndex - 1);
    }

    /**
     * @param int $startTokenIndex
     * @param int $endTokenIndex
     * @return int
     */
    private function getNextReturnIndex($startTokenIndex, $endTokenIndex)
    {
        return (int) $this->file->findNext(T_RETURN, $startTokenIndex + 1, $endTokenIndex - 1);
    }

    /**
     * @param int $functionTokenIndex
     * @return bool
     */
    private function isInterfaceOrAbstractFunction($functionTokenIndex)
    {
        return T_FUNCTION === $this->tokens[$functionTokenIndex]['code'] &&
               !isset($this->tokens[$functionTokenIndex]['scope_opener']);
    }

    /**
     * @param int $startTokenIndex
     * @return int
     */
    private function getNextNonEmptyTokenIndex($startTokenIndex)
    {
        $searchTypes = array_merge(PHP_CodeSniffer_Tokens::$emptyTokens, [T_WHITESPACE]);

        return (int) $this->file->findNext($searchTypes, $startTokenIndex, null, true);
    }
}
