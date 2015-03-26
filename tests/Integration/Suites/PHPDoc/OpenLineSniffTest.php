<?php

class OpenLineSniffTest extends SniffTest
{
    /**
     * @return string
     */
    protected final function getFileUnderTest()
    {
        return '../../src/Brera/Sniffs/PHPDoc/OpenLineSniff.php';
    }

    /**
     * @test
     */
    public function itShouldNotAddAnyErrorsIfAnnotationOpeningTagIsTheOnlyContentOnTheLine()
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
    public function itShouldAddAnErrorIfAnnotationOpeningTagIfFollowedBySomeContentOnTheSameLine()
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