<?php

declare(strict_types=1);

class UselessAnnotationSniffTest extends SniffTest
{
    final protected function getFileUnderTest() : string
    {
        return 'src/LizardsAndPumpkins/Sniffs/PHPDoc/UselessAnnotationSniff.php';
    }

    public function testNoErrorsAddedIfOnlyAllowedAnnotationsArePresentInPHPDoc()
    {
        $code = '
        /**
         * @return string[]
         */
        public function getFoo() : array
        {
            return ["foo"];
        }';

        $phpCSFile = $this->processCode($code);
        $errors = $phpCSFile->getErrors();

        $this->assertEmpty($errors);
    }

    public function testErrorIsAddedIfAnnotationContainsUselessLines()
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
        }';

        $phpCSFile = $this->processCode($code);
        $error = $this->getFirstErrorMessage($phpCSFile->getErrors());
        $expectedError = 'PHPDoc must not contain useless lines';

        $this->assertEquals($expectedError, $error);
    }
}
