<?php

class LizardsAndPumpkins_Sniffs_Complexity_TooManyTypedArgumentsSniff implements PHP_CodeSniffer_Sniff
{
    const MAXIMUM_ALLOWED_TYPED_ARGUMENTS = 3;

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
        $parameters = $file->getMethodParameters($functionTokenIndex);

        if (empty($parameters)) {
            return;
        }

        $typedArgumentsCount = 0;

        while ($typedArgumentsCount <= self::MAXIMUM_ALLOWED_TYPED_ARGUMENTS && list(, $parameter)= each($parameters)) {
            if (!empty($parameter['type_hint']) && 'array' !== $parameter['type_hint']) {
                ++$typedArgumentsCount;
            }
        }

        if ($typedArgumentsCount > self::MAXIMUM_ALLOWED_TYPED_ARGUMENTS) {
            $file->addWarning('Too many objects passed to function', $functionTokenIndex);
        }
    }
}
