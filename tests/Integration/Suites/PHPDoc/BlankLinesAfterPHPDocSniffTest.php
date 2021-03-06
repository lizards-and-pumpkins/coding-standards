<?php

declare(strict_types=1);

class BlankLinesAfterPHPDocSniffTest extends SniffTest
{
    final protected function getFileUnderTest() : string
    {
        return 'src/LizardsAndPumpkins/Sniffs/PHPDoc/BlankLinesAfterPHPDocSniff.php';
    }

    public function testNoErrorsAddedIfFunctionAndItsAnnotationHaveNoBlankLinesInBetween()
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

    public function testErrorIsAddedIfFunctionAndItsAnnotationHaveHasBlankLinesInBetween()
    {
        $code = '
        /**
         * @return string
         */

        public function getFoo() { }';

        $phpCSFile = $this->processCode($code);
        $error = $this->getFirstErrorMessage($phpCSFile->getErrors());
        $expectedError = 'There must be no blank lines after PHPDoc';

        $this->assertEquals($expectedError, $error);
    }
}
