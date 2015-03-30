<?php

class ArrayAnnotationSniffTest extends SniffTest
{
    /**
     * @return string
     */
    protected final function getFileUnderTest()
    {
        return '../../src/Brera/Sniffs/PHPDoc/ArrayAnnotationSniff.php';
    }

    /**
     * @test
     */
    public function itShouldNotAddAnyErrorsIfArraysAreExplicitlyAnnotated()
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

    /**
     * @test
     */
    public function itShouldAddAnErrorIfArrayIsAnnotatedImplicitly()
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
}
