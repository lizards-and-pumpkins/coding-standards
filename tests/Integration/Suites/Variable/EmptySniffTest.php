<?php

class EmptySniffTest extends SniffTest
{
    /**
     * @return string
     */
    protected function getFileUnderTest()
    {
        return '../../src/LizardsAndPumpkins/Sniffs/Variable/EmptySniff.php';
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

        $this->assertSame('empty() is disallowed.', $this->getFirstErrorMessage($phpCSFile->getErrors()));
    }
}
