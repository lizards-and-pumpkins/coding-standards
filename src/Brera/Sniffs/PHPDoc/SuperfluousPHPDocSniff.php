<?php

class Brera_Sniffs_PHPDoc_SuperfluousPHPDocSniff implements PHP_CodeSniffer_Sniff
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

        if ($this->isTest($functionTokenIndex)) {
            return;
        }

        if (!$this->phpDocIsRequired($functionTokenIndex) && $this->phpDocExists($functionTokenIndex)) {
            $this->file->addError('Superfluous PHPDoc', $functionTokenIndex);
        }
    }
}
