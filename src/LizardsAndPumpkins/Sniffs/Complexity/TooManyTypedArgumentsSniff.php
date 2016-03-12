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
     * @param int $tokenIndex
     */
    public function process(PHP_CodeSniffer_File $file, $tokenIndex)
    {
        $typedArgumentsCount = array_reduce($file->getMethodParameters($tokenIndex), function ($carry, $parameter) {
            if ('' !== $parameter['type_hint'] && 'array' !== $parameter['type_hint']) {
                return $carry + 1;
            }
            return $carry;
        }, 0);

        if ($typedArgumentsCount > self::MAXIMUM_ALLOWED_TYPED_ARGUMENTS) {
            $file->addWarning('Too many objects passed to function', $tokenIndex);
        }
    }
}
