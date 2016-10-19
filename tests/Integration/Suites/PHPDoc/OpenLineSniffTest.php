<?php

declare(strict_types=1);

class OpenLineSniffTest extends SniffTest
{
    final protected function getFileUnderTest() : string
    {
        return 'src/LizardsAndPumpkins/Sniffs/PHPDoc/OpenLineSniff.php';
    }

    public function testNoErrorsAddedIfAnnotationOpeningTagIsTheOnlyContentOnTheLine()
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

    public function testErrorIsAddedIfAnnotationOpeningTagIfFollowedBySomeContentOnTheSameLine()
    {
        $code = '
        /** bar
         * @return string
         */
        public function getFoo() { }';

        $phpCSFile = $this->processCode($code);
        $error = $this->getFirstErrorMessage($phpCSFile->getErrors());
        $expectedError = 'The open PHPDoc tag must be the only content on the line';

        $this->assertEquals($expectedError, $error);
    }
}
