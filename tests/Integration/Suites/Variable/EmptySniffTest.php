<?php

declare(strict_types=1);

class EmptySniffTest extends SniffTest
{
    final protected function getFileUnderTest() : string
    {
        return 'src/LizardsAndPumpkins/Sniffs/Variable/EmptySniff.php';
    }

    public function testNoErrorsAreAddedIfEmptyFunctionIsNotFound()
    {
        $code = '$foo = "bar"';
        $phpCSFile = $this->processCode($code);

        $this->assertSame(0, $phpCSFile->getErrorCount());
    }

    public function testErrorIsAddedIfCodeContainsEmptyFunction()
    {
        $code = 'if (empty(0)) { }';

        $phpCSFile = $this->processCode($code);
        $firstErrorMessage = $this->getFirstErrorMessage($phpCSFile->getErrors());

        $this->assertSame('empty() is disallowed, please use explicit comparison instead.', $firstErrorMessage);
    }
}
