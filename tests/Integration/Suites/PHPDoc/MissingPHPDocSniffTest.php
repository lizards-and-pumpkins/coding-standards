<?php

class MissingPHPDocSniffTest extends SniffTest
{
    /**
     * @return string
     */
    protected final function getFileUnderTest()
    {
        return '../../src/Brera/Sniffs/PHPDoc/MissingPHPDocSniff.php';
    }

    /**
     * @test
     */
    public function itShouldNotAddAnyErrorsIfFunctionHasAValidPHPDoc()
    {
        $code = '
        /**
         * @return string
         */
        public function getFoo() { }';

        $phpCSFile = $this->processCode($code);
        $errors = $phpCSFile->getErrors();

        $this->assertEmpty($errors);
    }

    /**
     * @test
     */
    public function itShouldAddAnErrorIfFunctionPHPDocBlockIsRequiredButMissing()
    {
        $code = 'public function prepareFoo($foo) { }';

        $phpCSFile = $this->processCode($code);
        $error = $this->getFirstErrorMessage($phpCSFile->getErrors());
        $expectedError = 'Missing PHPDoc';

        $this->assertEquals($expectedError, $error);
    }

    /**
     * @test
     */
    public function itShouldAddAnErrorIfPHPDocHasAtLeastOneUntypedArgument()
    {
        $code = 'public function prepareData(array $data) { }';

        $phpCSFile = $this->processCode($code);
        $error = $this->getFirstErrorMessage($phpCSFile->getErrors());
        $expectedError = 'Missing PHPDoc';

        $this->assertEquals($expectedError, $error);
    }

    /**
     * @test
     */
    public function itShouldNotAddAnyErrorsIfPHPDocContainsFromOnlyTypedArguments()
    {
        $code = 'public function prepareFoo(Foo $foo) { }';

        $phpCSFile = $this->processCode($code);
        $errors = $phpCSFile->getErrors();

        $this->assertEmpty($errors);
    }
}
