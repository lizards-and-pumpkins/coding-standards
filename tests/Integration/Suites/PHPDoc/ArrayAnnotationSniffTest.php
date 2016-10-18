<?php

class ArrayAnnotationSniffTest extends SniffTest
{
    final protected function getFileUnderTest() : string
    {
        return 'src/LizardsAndPumpkins/Sniffs/PHPDoc/ArrayAnnotationSniff.php';
    }

    public function testNoErrorsAddedIfArraysAreExplicitlyAnnotated()
    {
        $code = '
        /**
         * @return mixed[]
         */
        public function getArrayOfMixedTypes() { }';

        $phpCSFile = $this->processCode($code);
        $errors = $phpCSFile->getErrors();

        $this->assertEmpty($errors);
    }

    public function testErrorIsAddedIfArrayParameterIsAnnotatedImplicitly()
    {
        $code = '
        /**
         * @param array $fooArray
         */
        public function prepareData(array $fooArray) { }';

        $phpCSFile = $this->processCode($code);
        $error = $this->getFirstErrorMessage($phpCSFile->getErrors());
        $expectedError = 'Function PHPDoc must not contain "array" annotations';

        $this->assertEquals($expectedError, $error);
    }

    public function testErrorIsAddedIfArrayReturnIsAnnotatedImplicitly()
    {
        $code = '
        /**
         * @return array
         */
        public function getFooArray() { return [] }';

        $phpCSFile = $this->processCode($code);
        $error = $this->getFirstErrorMessage($phpCSFile->getErrors());
        $expectedError = 'Function PHPDoc must not contain "array" annotations';

        $this->assertEquals($expectedError, $error);
    }
}
