<?php

class Brera_Sniffs_PHPDoc_ArrayAnnotationSniff implements PHP_CodeSniffer_Sniff
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

        $functionPHPDocAnnotationsIndices = $this->getPHPDocAnnotationsIndices($functionTokenIndex);

        foreach ($functionPHPDocAnnotationsIndices as $annotationIndex) {
            if (!in_array($this->tokens[$annotationIndex]['content'], ['@param', '@return'])) {
                continue;
            }

            $nextAnnotationToken = $this->getNextAnnotationStringTokenContent($annotationIndex);

            if ('array ' === substr($nextAnnotationToken, 0, 6) || 'array' === $nextAnnotationToken) {
                $this->file->addError('Function PHPDoc must not contain "array" annotations', $annotationIndex);
            }
        }
    }

    /**
     * @param int $annotationIndex
     * @return string
     */
    private function getNextAnnotationStringTokenContent($annotationIndex)
    {
        $nextTokenIndex = $this->file->findNext(T_DOC_COMMENT_WHITESPACE, $annotationIndex + 1, null, true);

        if ($this->tokens[$nextTokenIndex]['line'] !== $this->tokens[$annotationIndex]['line']) {
            return '';
        }

        if (T_DOC_COMMENT_STRING !== $this->tokens[$nextTokenIndex]['code']) {
            return '';
        }

        return $this->tokens[$nextTokenIndex]['content'];
    }
}
