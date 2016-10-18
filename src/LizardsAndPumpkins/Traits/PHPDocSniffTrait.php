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
}
