<?php

class LizardsAndPumpkins_Sniffs_PHPDoc_AnnotationGroupingSniff implements PHP_CodeSniffer_Sniff
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

        $groups = $this->getGroupedPHPDocAnnotations($functionTokenIndex);

        if ($this->groupsAreMixed($groups)) {
            $this->file->addError('PHPDoc annotations must be grouped by type', $functionTokenIndex);
        }
    }

    /**
     * @param int $functionTokenIndex
     * @return array[]
     */
    private function getGroupedPHPDocAnnotations($functionTokenIndex)
    {
        $groups = [];

        $functionPHPDocAnnotationsIndices = $this->getPHPDocAnnotationsIndices($functionTokenIndex);

        foreach ($functionPHPDocAnnotationsIndices as $annotationIndex) {
            if (!array_key_exists($this->tokens[$annotationIndex]['content'], $groups)) {
                $groups[$this->tokens[$annotationIndex]['content']] = [];
            }

            array_push($groups[$this->tokens[$annotationIndex]['content']], $this->tokens[$annotationIndex]['line']);
        }

        return $groups;
    }

    /**
     * @param array[] $groups
     * @return bool
     */
    private function groupsAreMixed(array $groups)
    {
        $groupsAreMixed = false;

        while (!$groupsAreMixed && list(, $group) = each($groups)) {
            $groupsAreMixed = !$this->arrayOnlyContainsConsecutiveNumbers($group);
        }

        return $groupsAreMixed;
    }

    /**
     * @param int[] $array
     * @return bool
     */
    private function arrayOnlyContainsConsecutiveNumbers(array $array)
    {
        $deductedArray = [];
        $arrayLength = count($array);

        for ($i = 0; $i < $arrayLength; $i++) {
            $deductedArray[$i] = $array[$i] - $array[0];
        }

        return $arrayLength - 1 === $deductedArray[$arrayLength - 1];
    }
}
