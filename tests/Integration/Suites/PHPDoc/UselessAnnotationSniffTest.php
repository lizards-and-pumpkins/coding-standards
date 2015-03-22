<?php

class UselessAnnotationSniffTest extends SniffTest
{
    /**
     * @return string
     */
    protected final function getFileUnderTest()
    {
        return '../../src/Brera/Sniffs/PHPDoc/UselessAnnotationSniff.php';
    }

    /**
     * @test
     */
    public function itShouldNotAddAnyErrorsIfOnlyAllowedAnnotationsAreDefined()
    {
        $code = '
        /**
         * @return string
         */
        public function getFoo()
        {
            return "foo";
        }
';

        $phpCSFile = $this->processCode($code);
        $errors = $phpCSFile->getErrors();

        $this->assertEmpty($errors);
    }

    /**
     * @test
     */
    public function itShouldAddAnErrorIfAnnotationContainsUselessLines()
    {
        $code = '
        /**
         * PHPDoc should not contain comments or blanks lines
         *
         * @return string
         */
        public function getFoo()
        {
            return "foo";
        }
';

        $phpCSFile = $this->processCode($code);
        $error = $this->getFirstErrorMessage($phpCSFile->getErrors());
        $expectedError = 'PHPDoc must not contain useless lines';

        $this->assertEquals($expectedError, $error);
    }
}
