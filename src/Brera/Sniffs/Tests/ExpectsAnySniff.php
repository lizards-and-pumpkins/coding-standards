<?php


class Brera_Sniffs_Tests_ExpectsAnySniff implements PHP_CodeSniffer_Sniff
{
    /**
     * @var int
     */
    private $tokenIndex;

    /**
     * @var array[]
     */
    private $tokens;

    /**
     * @var mixed[]
     */
    private $assertFramesBefore = [
        ['code' => T_VARIABLE],
        ['code' => T_OBJECT_OPERATOR],
    ];

    /**
     * @var mixed[]
     */
    private $assertFramesAfter = [
        ['code' => T_OPEN_PARENTHESIS],
        ['code' => T_VARIABLE, 'content' => '$this'],
        ['code' => T_OBJECT_OPERATOR],
        ['code' => T_STRING, 'content' => 'any'],
        ['code' => T_OPEN_PARENTHESIS],
        ['code' => T_CLOSE_PARENTHESIS],
        ['code' => T_CLOSE_PARENTHESIS],
    ];
    
    /**
     * @var string
     */
    private $message = 'Setting expects{$this->any()) on mocks can (and should) be omitted since PHPUnit version 4';

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
        $this->tokenIndex = $tokenIndex;
        $this->tokens = $file->getTokens();
        
        if (! $this->isMatchesExpectsAnyMethodCall()) {
            return;
        }
        
        $canFix = $this->addWarningMessage($file);

        if ($canFix === true) {
            $this->removeExpectsAnyIfFixerEnabled($file);
        }
    }

    /**
     * @return bool
     */
    private function isMatchesExpectsAnyMethodCall()
    {
        if ('expects' !== $this->tokens[$this->tokenIndex]['content']) {
            return false;
        }
        if (!$this->matchesStackBefore($this->assertFramesBefore)) {
            return false;
        }

        if (!$this->matchesStackAfter($this->assertFramesAfter)) {
            return false;
        }
        return true;
    }

    /**
     * @param array[] $assertBefore
     * @param int|null $stackIndex
     * @return bool
     */
    private function matchesStackBefore(array $assertBefore, $stackIndex = null)
    {
        if (empty($assertBefore)) {
            return true;
        }
        $index = is_null($stackIndex) ? $this->tokenIndex - 1 : $stackIndex;
        $frameSpec = array_slice($assertBefore, -1)[0];
        if ($index < 0 || !$this->matchesFrame($frameSpec, $this->tokens[$index])) {
            return false;
        }
        return $this->matchesStackBefore(array_slice($assertBefore, 0, -1), $index - 1);
    }

    /**
     * @param array[] $assertAfter
     * @param int|null $stackIndex
     * @return bool
     */
    private function matchesStackAfter(array $assertAfter, $stackIndex = null)
    {
        if (empty($assertAfter)) {
            return true;
        }
        $index = is_null($stackIndex) ? $this->tokenIndex + 1 : $stackIndex;
        if (!isset($this->tokens[$index]) || !$this->matchesFrame($assertAfter[0], $this->tokens[$index])) {
            return false;
        }
        return $this->matchesStackAfter(array_slice($assertAfter, 1), $index + 1);
    }

    /**
     * @param mixed[] $frameSpec
     * @param mixed[] $frameToCheck
     * @return bool
     */
    private function matchesFrame(array $frameSpec, array $frameToCheck)
    {
        foreach ($frameSpec as $key => $value) {
            if (!isset($frameToCheck[$key])) {
                return false;
            }
            if ($frameToCheck[$key] !== $value) {
                return false;
            }
        }
        return true;
    }

    private function removeExpectsAnyIfFixerEnabled(PHP_CodeSniffer_File $file)
    {
        if ($this->autoFixWouldProduceValidPHP()) {
            return;
        }
        if ($file->fixer->enabled === true) {
            $file->fixer->beginChangeset();
            $this->applyAutoFixChangeSet($file);
            $file->fixer->endChangeset();
        }
    }

    /**
     * @return bool
     */
    private function autoFixWouldProduceValidPHP()
    {
        $indexOfTokenAfterExpectsAny = count($this->assertFramesAfter) +1;
        $allowedNextTokens = [';', '->'];
        return in_array($this->tokens[$indexOfTokenAfterExpectsAny]['content'], $allowedNextTokens);
    }

    /**
     * @param PHP_CodeSniffer_File $file
     * @return bool
     */
    private function addWarningMessage(PHP_CodeSniffer_File $file)
    {
        return $file->addFixableWarning($this->message, $this->tokenIndex, '', [], 0);
    }

    private function applyAutoFixChangeSet(PHP_CodeSniffer_File $file)
    {
        $firstTokenOfChangeSet = $this->tokenIndex - 1;
        $lastTokenOfChangeSet = $this->tokenIndex + 7;
        for ($i = $firstTokenOfChangeSet; $i <= $lastTokenOfChangeSet; $i++) {
            $file->fixer->replaceToken($i, '');
        }
    }
}
