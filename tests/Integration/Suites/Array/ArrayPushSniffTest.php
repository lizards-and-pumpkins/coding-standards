<?php

class ArrayPushSniffTest extends SniffTest
{
    final protected function getFileUnderTest() : string
    {
        return 'src/LizardsAndPumpkins/Sniffs/Array/ArrayPushSniff.php';
    }

    public function testNoErrorsAddedIfArrayPushIsNotFound()
    {
        $code = '$foo = time()';

        $phpCSFile = $this->processCode($code);
        $errors = $phpCSFile->getErrors();

        $this->assertEmpty($errors);
    }

    public function testErrorIsAddedIfArrayPushIsUsed()
    {
        $code = 'array_push($foo, 1)';

        $phpCSFile = $this->processCode($code);
        $result = $this->getFirstErrorMessage($phpCSFile->getErrors());

        $this->assertEquals('array_push() is disallowed. Use assignment instead.', $result);
    }
}
