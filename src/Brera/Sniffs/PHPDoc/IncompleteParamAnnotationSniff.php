<?php

class Brera_Sniffs_PHPDoc_IncompleteParamAnnotationSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * @return int[]
     */
    public function register()
    {
        return [T_DOC_COMMENT_TAG];
    }

    /**
     * @param PHP_CodeSniffer_File $file
     * @param int $annotationTokenIndex
     */
    public function process(PHP_CodeSniffer_File $file, $annotationTokenIndex)
    {
        $tokens = $file->getTokens();
        $errorMessage = 'Parameter annotation is incomplete';

        if ('@param' !== $tokens[$annotationTokenIndex]['content']) {
            return;
        }

        $nextTokenIndex = $file->findNext([T_DOC_COMMENT_WHITESPACE], $annotationTokenIndex + 1, null, true);
        if (T_DOC_COMMENT_STRING !== $tokens[$nextTokenIndex]['code'] ||
            $tokens[$annotationTokenIndex]['line'] !== $tokens[$nextTokenIndex]['line']
        ) {
            $file->addError($errorMessage, $annotationTokenIndex);
            return;
        }

        $subTokens = explode(' ', $tokens[$nextTokenIndex]['content']);
        if (count($subTokens) < 2 || '$' === substr($subTokens[0], 0, 1) || '$' !== substr($subTokens[1], 0, 1)) {
            $file->addError($errorMessage, $annotationTokenIndex);
        }
    }
}
