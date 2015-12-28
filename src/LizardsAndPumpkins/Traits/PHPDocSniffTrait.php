<?php

trait LizardsAndPumpkins_Traits_PHPDocSniffTrait
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
    final protected function phpDocIsRequired($functionTokenIndex)
    {
        return $this->functionReturnsNonVoid($functionTokenIndex) ||
               $this->functionHasUntypedParameters($functionTokenIndex);
    }

    /**
     * @param $functionTokenIndex
     * @return bool
     */
    final protected function phpDocExists($functionTokenIndex)
    {
        $commentEndIndex = $this->getPhpDocEndTokenIndex($functionTokenIndex);

        return T_DOC_COMMENT_CLOSE_TAG === $this->tokens[$commentEndIndex]['code'];
    }

    /**
     * @param int $functionTokenIndex
     * @return bool|int
     */
    final protected function getPhpDocEndTokenIndex($functionTokenIndex)
    {
        $searchTypes = array_merge(PHP_CodeSniffer_Tokens::$methodPrefixes, [T_WHITESPACE]);

        return $this->file->findPrevious($searchTypes, $functionTokenIndex - 1, null, true);
    }

    /**
     * @param int $functionTokenIndex
     * @return int[]
     */
    final protected function getPHPDocAnnotationsIndices($functionTokenIndex)
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
            $nextNonEmptyTokenIndex = $this->getNextNonEmptyTokenIndex($returnIndex + 1);

            if ($nextNonEmptyTokenIndex > 0 && !$this->isInsideOfClosure($nextNonEmptyTokenIndex)) {
                $nonVoidReturnFound = T_SEMICOLON !== $this->tokens[$nextNonEmptyTokenIndex]['code'];
            }
        }

        return $nonVoidReturnFound;
    }

    /**
     * @param int $functionTokenIndex
     * @return bool
     */
    private function functionHasUntypedParameters($functionTokenIndex)
    {
        $functionParameters = $this->file->getMethodParameters($functionTokenIndex);

        if (empty($functionParameters)) {
            return false;
        }

        $hasUntypedParameter = false;

        while (!$hasUntypedParameter && list(, $parameter)= each($functionParameters)) {
            $hasUntypedParameter = empty($parameter['type_hint']) || 'array' === $parameter['type_hint'];
        }

        return $hasUntypedParameter;
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

    /**
     * @param int $tokenIndex
     * @return bool
     */
    private function isInsideOfClosure($tokenIndex)
    {
        return in_array(T_CLOSURE, $this->tokens[$tokenIndex]['conditions']);
    }
}
