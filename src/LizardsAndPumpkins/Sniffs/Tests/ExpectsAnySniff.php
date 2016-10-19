<?php

declare(strict_types=1);

class LizardsAndPumpkins_Sniffs_Tests_ExpectsAnySniff implements PHP_CodeSniffer_Sniff
{
    /**
     * @var array[]
     */
    private $assertFramesBefore = [
        ['content' => '->'],
    ];

    /**
     * @var array[]
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

        $canFix = $file->addFixableWarning($this->message, $tokenStackIndex);

        if (true === $canFix && true === $file->fixer->enabled) {
            $this->removeExpectsAnyTokens($file);
        }
    }

    private function isMatchesExpectsAnyMethodCall() : bool
    {
        if ('expects' !== $this->tokenStack[$this->tokenIndex]['content']) {
            return false;
        }

        if (false === $this->getStartIndexIfMatches() || false === $this->getEndIndexIfMatches()) {
            return false;
        }

        return true;
    }

    /**
     * @return int|bool
     */
    private function getStartIndexIfMatches()
    {
        $finder = $this->getMatchingIndexFinder('backward');
        return $finder(array_reverse($this->assertFramesBefore));
    }

    /**
     * @return int|bool
     */
    private function getEndIndexIfMatches()
    {
        $finder = $this->getMatchingIndexFinder('forward');
        return $finder($this->assertFramesAfter);
    }

    private function getMatchingIndexFinder(string $direction = 'forward') : callable
    {
        $op = 'backward' === $direction ? -1 : 1;
        
        $finder = function (array $frameSpecList, $stackIndex = null) use ($op, &$finder) {
            if ([] === $frameSpecList) {
                return null === $stackIndex ? $this->tokenIndex : $stackIndex - $op;
            }
            $index = null === $stackIndex ? $this->tokenIndex + $op : $stackIndex;
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
    private function isMatchingFrame(array $tokenSpec, array $tokenToCheck) : bool
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

    private function isWhitespace(int $tokenIndex) : bool
    {
        return isset($this->tokenStack[$tokenIndex]) && $this->tokenStack[$tokenIndex]['type'] === 'T_WHITESPACE';
    }

    private function isNewline(int $tokenIndex) : bool
    {
        return isset($this->tokenStack[$tokenIndex]) && $this->tokenStack[$tokenIndex]['content'] === PHP_EOL;
    }

    private function removeExpectsAnyTokens(PHP_CodeSniffer_File $file)
    {
        $file->fixer->beginChangeset();
        $matchStartIndex = (int) $this->getStartIndexIfMatches();
        $matchEndIndex = (int) $this->getEndIndexIfMatches();
        for ($i = $matchStartIndex; $i <= $matchEndIndex; $i++) {
            $file->fixer->replaceToken($i, '');
        }
        $this->ifNewLineMoveNextLineUp($matchEndIndex+1, $file);
        $file->fixer->endChangeset();
    }

    private function ifNewLineMoveNextLineUp(int $index, PHP_CodeSniffer_File $file)
    {
        if ($this->isNewline($index)) {
            while ($this->isWhitespace($index)) {
                $file->fixer->replaceToken($index++, '');
            }
        }
    }
}
