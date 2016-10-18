<?php

declare(strict_types=1);

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

    private function phpDocIsRequired(int $functionTokenIndex) : bool
    {
        return $this->functionReturnsNonVoid($functionTokenIndex) ||
               $this->functionHasUntypedParameters($functionTokenIndex);
    }

    private function phpDocExists(int $functionTokenIndex) : bool
    {
        $commentEndIndex = $this->getPhpDocEndTokenIndex($functionTokenIndex);

        return T_DOC_COMMENT_CLOSE_TAG === $this->tokens[$commentEndIndex]['code'];
    }

    /**
     * @param int $functionTokenIndex
     * @return bool|int
     */
    private function getPhpDocEndTokenIndex(int $functionTokenIndex)
    {
        $searchTypes = array_merge(PHP_CodeSniffer_Tokens::$methodPrefixes, [T_WHITESPACE]);

        return $this->file->findPrevious($searchTypes, $functionTokenIndex - 1, null, true);
    }

    /**
     * @param int $functionTokenIndex
     * @return int[]
     */
    private function getPHPDocAnnotationsIndices(int $functionTokenIndex) : array
    {
        $commentEndIndex = $this->getPhpDocEndTokenIndex($functionTokenIndex);
        $commentStartIndex = $this->tokens[$commentEndIndex]['comment_opener'];

        if (!isset($this->tokens[$commentStartIndex]['comment_tags'])) {
            return [];
        }

        return $this->tokens[$commentStartIndex]['comment_tags'];
    }

    private function functionReturnsNonVoid(int $functionTokenIndex) : bool
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

    private function functionHasUntypedParameters(int $functionTokenIndex) : bool
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

    private function getNextReturnIndex(int $startTokenIndex, int $endTokenIndex) : int
    {
        return (int) $this->file->findNext(T_RETURN, $startTokenIndex + 1, $endTokenIndex - 1);
    }

    private function isInterfaceOrAbstractFunction(int $functionTokenIndex) : bool
    {
        return T_FUNCTION === $this->tokens[$functionTokenIndex]['code'] &&
               !isset($this->tokens[$functionTokenIndex]['scope_opener']);
    }

    private function getNextNonEmptyTokenIndex(int $startTokenIndex) : int
    {
        $searchTypes = array_merge(PHP_CodeSniffer_Tokens::$emptyTokens, [T_WHITESPACE]);

        return (int) $this->file->findNext($searchTypes, $startTokenIndex, null, true);
    }

    private function isInsideOfClosure(int $tokenIndex) : bool
    {
        return in_array(T_CLOSURE, $this->tokens[$tokenIndex]['conditions']);
    }

    /**
     * @return string[]
     */
    private function getAllowedAnnotations() : array
    {
        $annotationsAllowedInCode = [
            '@see',
            '@param',
            '@return',
        ];

        return array_merge($annotationsAllowedInCode, $this->getAnnotationsAllowedInTests());
    }

    /**
     * @return string[]
     */
    private function getAnnotationsAllowedInTests() : array
    {
        return [
            '@depends',
            '@dataProvider',
            '@runInSeparateProcess',
            '{@inheritdoc}',
            '@before',
            '@after',
            '@requires',
        ];
    }
}
