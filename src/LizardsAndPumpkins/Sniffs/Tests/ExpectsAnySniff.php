<?php


class LizardsAndPumpkins_Sniffs_Tests_ExpectsAnySniff implements PHP_CodeSniffer_Sniff
{
    /**
     * @var mixed[]
     */
    private $assertFramesBefore = [
        ['content' => '->'],
    ];

    /**
     * @var mixed[]
     */
    private $assertFramesAfter = [
        ['content' => '('],
        ['code' => T_VARIABLE, 'content' => '$this'],
        ['content' => '->'],
        ['code' => T_STRING, 'content' => 'any'],
        ['content' => '('],
        ['content' => ')'],
        ['content' => ')'],
    ];

    /**
     * @var string
     */
    private $message = 'Setting expects($this->any()) on mocks can (and should) be omitted since PHPUnit version 4';

    /**
     * @var int
     */
    private $tokenIndex;

    /**
     * @var array[]
     */
    private $tokenStack;

    /**
     * @return int[]
     */
    public function register()
    {
        return [T_STRING];
    }

    /**
     * @param PHP_CodeSniffer_File $file
     * @param int $tokenStackIndex
     */
    public function process(PHP_CodeSniffer_File $file, $tokenStackIndex)
    {
        $this->tokenIndex = $tokenStackIndex;
        $this->tokenStack = $file->getTokens();

        if (!$this->isMatchesExpectsAnyMethodCall()) {
            return;
        }
        $canFix = $this->addWarningMessage($file);

        if (true === $canFix) {
            $this->removeExpectsAnyTokensIfEnabled($file);
        }
    }

    /**
     * @return bool
     */
    private function isMatchesExpectsAnyMethodCall()
    {
        if ('expects' !== $this->tokenStack[$this->tokenIndex]['content']) {
            return false;
        }
        $matchStartIndex = $this->getStartIndexIfMatches($this->assertFramesBefore);
        $matchEndIndex = $this->getEndIndexIfMatches($this->assertFramesAfter);
        if (false === $matchStartIndex || false === $matchEndIndex) {
            return false;
        }
        return true;
    }

    /**
     * @param array[] $assertBefore
     * @return bool
     */
    private function getStartIndexIfMatches(array $assertBefore)
    {
        $finder = $this->getMatchingIndexFinder('backward');
        return $finder(array_reverse($assertBefore));
    }

    /**
     * @param array[] $assertAfter
     * @return int|bool
     */
    private function getEndIndexIfMatches(array $assertAfter)
    {
        $finder = $this->getMatchingIndexFinder('forward');
        return $finder($assertAfter);
    }

    /**
     * @param string $direction
     * @return callable
     */
    private function getMatchingIndexFinder($direction = 'forward')
    {
        $op = 'backward' === $direction ? -1 : 1;
        
        $finder = function (array $frameSpecList, $stackIndex = null) use ($op, &$finder) {
            if (empty($frameSpecList)) {
                $foundMatchingIndex = is_null($stackIndex) ? $this->tokenIndex : $stackIndex - $op;
                return $foundMatchingIndex;
            }
            $index = is_null($stackIndex) ? $this->tokenIndex + $op : $stackIndex;
            if ($index < 0 || !isset($this->tokenStack[$index])) {
                return false;
            }
            $nextTokenIndex = $index + $op;
            if ($this->isWhitespace($index)) {
                return $finder($frameSpecList, $nextTokenIndex);
            }
            if (!$this->isMatchingFrame($frameSpecList[0], $this->tokenStack[$index])) {
                return false;
            }
            return $finder(array_slice($frameSpecList, 1), $nextTokenIndex);
        };
        return $finder;
    }

    /**
     * @param mixed[] $tokenSpec
     * @param mixed[] $tokenToCheck
     * @return bool
     */
    private function isMatchingFrame(array $tokenSpec, array $tokenToCheck)
    {
        foreach ($tokenSpec as $key => $value) {
            if (!isset($tokenToCheck[$key])) {
                return false;
            }
            if ($tokenToCheck[$key] !== $value) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param int $tokenIndex
     * @return bool
     */
    private function isWhitespace($tokenIndex)
    {
        return isset($this->tokenStack[$tokenIndex]) && $this->tokenStack[$tokenIndex]['type'] === 'T_WHITESPACE';
    }

    /**
     * @param int $tokenIndex
     * @return bool
     */
    private function isNewline($tokenIndex)
    {
        return isset($this->tokenStack[$tokenIndex]) && $this->tokenStack[$tokenIndex]['content'] === PHP_EOL;
    }

    /**
     * @param PHP_CodeSniffer_File $file
     * @return bool
     */
    private function addWarningMessage(PHP_CodeSniffer_File $file)
    {
        return $file->addFixableWarning($this->message, $this->tokenIndex, '', [], 0);
    }

    private function removeExpectsAnyTokensIfEnabled(PHP_CodeSniffer_File $file)
    {
        if (true === $file->fixer->enabled) {
            $file->fixer->beginChangeset();
            $this->removeExpectsAnyTokens($file);
            $file->fixer->endChangeset();
        }
    }

    private function removeExpectsAnyTokens(PHP_CodeSniffer_File $file)
    {
        $matchStartIndex = (int) $this->getStartIndexIfMatches($this->assertFramesBefore);
        $matchEndIndex = (int) $this->getEndIndexIfMatches($this->assertFramesAfter);
        for ($i = $matchStartIndex; $i <= $matchEndIndex; $i++) {
            $file->fixer->replaceToken($i, '');
        }
        $this->ifNewLineMoveNextLineUp($matchEndIndex+1, $file);
    }

    /**
     * @param int $index
     * @param PHP_CodeSniffer_File $file
     */
    private function ifNewLineMoveNextLineUp($index, PHP_CodeSniffer_File $file)
    {
        if ($this->isNewline($index)) {
            while ($this->isWhitespace($index)) {
                $file->fixer->replaceToken($index++, '');
            }
        }
    }
}
