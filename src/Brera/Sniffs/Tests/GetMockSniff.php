<?php

class Brera_Sniffs_Tests_GetMockSniff implements PHP_CodeSniffer_Sniff
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

        if ('getMock' !== $tokens[$tokenIndex]['content']) {
            return;
        }

        if (!$this->isFunctionalCall($file, $tokenIndex, $tokens)) {
            return;
        }

        if ($this->isFunctionDeclaration($file, $tokenIndex, $tokens)) {
            return;
        }

        $arguments = $this->getMethodArgumentsStartingFromSecond($file, $tokenIndex, $tokens);

        if (empty($arguments)) {
            return;
        }
        
        if ((isset($arguments[0]) && !$this->isTokenAnEmptyArray($arguments[0])) ||
            (isset($arguments[1]) && !$this->isTokenAnEmptyArray($arguments[1])) ||
            (isset($arguments[2]) && !$this->isTokenAnEmptyString($arguments[2])) ||
            (isset($arguments[3]) && T_FALSE !== $arguments[3]['code'])
        ) {
            $file->addError(
                'Optional arguments of getMock() can be only used to disable original constructor',
                $tokenIndex
            );
        }
    }

    /**
     * @param PHP_CodeSniffer_File $file
     * @param $tokenIndex
     * @param mixed[] $tokens
     * @return bool
     */
    private function isFunctionalCall(PHP_CodeSniffer_File $file, $tokenIndex, array $tokens)
    {
        $openBracketIndex = $file->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, $tokenIndex + 1, null, true);

        $isFollowedByOpenParenthesis = T_OPEN_PARENTHESIS === $tokens[$openBracketIndex]['code'];
        $parenthesisCloserExists = isset($tokens[$openBracketIndex]['parenthesis_closer']);

        return $isFollowedByOpenParenthesis && $parenthesisCloserExists;
    }

    /**
     * @param PHP_CodeSniffer_File $file
     * @param $tokenIndex
     * @param mixed[] $tokens
     * @return bool
     */
    private function isFunctionDeclaration(PHP_CodeSniffer_File $file, $tokenIndex, array $tokens)
    {
        $searchTypes = array_merge(PHP_CodeSniffer_Tokens::$emptyTokens, [T_BITWISE_AND]);
        $previousTokenIndex = $file->findPrevious($searchTypes, $tokenIndex - 1, null, true);

        return $tokens[$previousTokenIndex]['code'] === T_FUNCTION;
    }

    /**
     * @param PHP_CodeSniffer_File $file
     * @param $tokenIndex
     * @param mixed[] $tokens
     * @return mixed[]
     */
    private function getMethodArgumentsStartingFromSecond(PHP_CodeSniffer_File $file, $tokenIndex, array $tokens)
    {
        $openBracketIndex = $file->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, $tokenIndex + 1, null, true);
        $closeBracketIndex = $tokens[$openBracketIndex]['parenthesis_closer'];
        $end = $file->findEndOfStatement($openBracketIndex + 1);

        $arguments = [];

        while ($tokens[$end]['code'] === T_COMMA) {
            $next = $file->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, $end + 1, $closeBracketIndex, true);
            $arguments[] = $tokens[$next];
            $end = $file->findEndOfStatement($next);
        }

        return $arguments;
    }

    /**
     * @param mixed[] $token
     * @return bool
     */
    private function isTokenAnEmptyArray(array $token)
    {
        if (!in_array($token['code'], [T_OPEN_SHORT_ARRAY, T_ARRAY])) {
            return false;
        }

        if (isset($token['bracket_closer']) && isset($token['bracket_opener'])) {
            return  1 === $token['bracket_closer'] - $token['bracket_opener'];
        }

        if (isset($token['parenthesis_closer']) && isset($token['parenthesis_opener'])) {
            return  1 === $token['parenthesis_closer'] - $token['parenthesis_opener'];
        }

        return false;
    }

    /**
     * @param mixed[] $token
     * @return bool
     */
    private function isTokenAnEmptyString(array $token)
    {
        return T_CONSTANT_ENCAPSED_STRING === $token['code'] && in_array($token['content'], ['\'\'', '""']);
    }
}
