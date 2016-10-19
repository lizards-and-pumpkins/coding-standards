<?php

declare(strict_types=1);

class CloseLineSniffTest extends SniffTest
{
    final protected function getFileUnderTest() : string
    {
        return 'src/LizardsAndPumpkins/Sniffs/PHPDoc/CloseLineSniff.php';
    }

    public function testNoErrorsAddedIfAnnotationClosingTagHasNothingInFrontOfIt()
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

    public function testErrorIsAddedIfAnnotationClosingTagHasContentInFrontOfIt()
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
