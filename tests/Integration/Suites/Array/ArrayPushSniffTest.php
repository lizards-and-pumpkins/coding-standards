<?php

class ArrayPushSniffTest extends SniffTest
{
    /**
     * @return string
     */
    protected function getFileUnderTest()
    {
        return '../../src/Brera/Sniffs/Array/ArrayPushSniff.php';
    }

    /**
     * @test
     */
    public function itShouldNotAddAnErrorIfArrayPushIsNotFound()
    {
        $code = '$foo = time()';

        $phpCSFile = $this->processCode($code);
        $errors = $phpCSFile->getErrors();

        $this->assertEmpty($errors);
    }

    /**
     * @test
     */
    public function itShouldAddAnErrorIfArrayPushIsUsed()
    {
        $code = 'array_push($foo, 1)';

        $phpCSFile = $this->processCode($code);
        $result = $this->getFirstErrorMessage($phpCSFile->getErrors());

        $this->assertEquals('array_push() is disallowed.', $result);
    }
}
