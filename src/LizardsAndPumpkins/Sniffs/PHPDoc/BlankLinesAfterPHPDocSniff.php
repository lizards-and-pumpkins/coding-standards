<?php

class LizardsAndPumpkins_Sniffs_PHPDoc_BlankLinesAfterPHPDocSniff implements PHP_CodeSniffer_Sniff
{
    use LizardsAndPumpkins_Traits_PHPDocSniffTrait;

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

        if (!$this->phpDocIsRequired($functionTokenIndex) && !$this->phpDocExists($functionTokenIndex)) {
            return;
        }

        if ($this->blankLinesBetweenFunctionAndItsDocBlock($functionTokenIndex)) {
            $this->file->addError('There must be no blank lines after PHPDoc', $functionTokenIndex);
        }
    }

    /**
     * @param int $functionTokenIndex
     * @return bool
     */
    private function blankLinesBetweenFunctionAndItsDocBlock($functionTokenIndex)
    {
        $commentEndIndex = $this->getPhpDocEndTokenIndex($functionTokenIndex);

        return 1 < $this->tokens[$functionTokenIndex]['line'] - $this->tokens[$commentEndIndex]['line'];
    }
}
