<?php

class CloseLineSniffTest extends SniffTest
{
    /**
     * @return string
     */
    protected final function getFileUnderTest()
    {
        return '../../src/Brera/Sniffs/PHPDoc/CloseLineSniff.php';
    }

    /**
     * @test
     */
    public function itShouldNotAddAnyErrorsIfAnnotationClosingTagHasNothingInFrontOfIt()
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
    public function itShouldAddAnErrorIfAnnotationClosingTagHasContentInFrontOfIt()
    {
        $code = '
        /**
         * @return string
         bar */
        public function getFoo() { }';

        $phpCSFile = $this->processCode($code);
        $error = $this->getFirstErrorMessage($phpCSFile->getErrors());
        $expectedError = 'The close PHPDoc tag must be the only content on the line';

        $this->assertEquals($expectedError, $error);
    }
}