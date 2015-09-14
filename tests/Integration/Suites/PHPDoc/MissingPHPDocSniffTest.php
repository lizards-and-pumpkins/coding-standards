<?php

class MissingPHPDocSniffTest extends SniffTest
{
    /**
     * @return string
     */
    protected final function getFileUnderTest()
    {
        return '../../src/LizardsAndPumpkins/Sniffs/PHPDoc/MissingPHPDocSniff.php';
    }

    public function testNoErrorsAddedIfFunctionHasAValidPHPDoc()
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

    public function testErrorIsAddedIfFunctionPHPDocBlockIsRequiredButMissing()
    {
        $code = 'public function prepareFoo($foo) { }';

        $phpCSFile = $this->processCode($code);
        $error = $this->getFirstErrorMessage($phpCSFile->getErrors());
        $expectedError = 'Missing PHPDoc';

        $this->assertEquals($expectedError, $error);
    }

    public function testErrorIsAddedIfPHPDocHasAtLeastOneUntypedArgument()
    {
        $code = 'public function prepareData(array $data) { }';

        $phpCSFile = $this->processCode($code);
        $error = $this->getFirstErrorMessage($phpCSFile->getErrors());
        $expectedError = 'Missing PHPDoc';

        $this->assertEquals($expectedError, $error);
    }

    public function testNoErrorsAddedIfPHPDocContainsFromOnlyTypedArguments()
    {
        $code = 'public function prepareFoo(Foo $foo) { }';

        $phpCSFile = $this->processCode($code);
        $errors = $phpCSFile->getErrors();

        $this->assertEmpty($errors);
    }

    public function testNoErrorsAreAddedIfThrowsAnnotationIsMissingInPHPDoc()
    {
        $code = 'public function foo() { throw new \Exception; }';
        $phpCSFile = $this->processCode($code);
        $errors = $phpCSFile->getErrors();

        $this->assertEmpty($errors);
    }
}
