<?php

declare(strict_types=1);

class LizardsAndPumpkins_Sniffs_PHPDoc_SuperfluousPHPDocSniff implements PHP_CodeSniffer_Sniff
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

        if (!$this->phpDocIsRequired($functionTokenIndex) &&
            $this->phpDocExists($functionTokenIndex) &&
            !$this->phpDocContainsTestRelatedAnnotations($functionTokenIndex)
        ) {
            $this->file->addError('Superfluous PHPDoc', $functionTokenIndex);
        }
    }

    private function phpDocContainsTestRelatedAnnotations(int $functionTokenIndex) : bool
    {
        $annotationTokenIndices = $this->getPHPDocAnnotationsIndices($functionTokenIndex);

        foreach ($annotationTokenIndices as $annotationIndex) {
            $annotationToken = $this->tokens[$annotationIndex];
            if (in_array($annotationToken['content'], $this->getAnnotationsAllowedInTests())) {
                return true;
            }
        }

        return false;
    }
}
