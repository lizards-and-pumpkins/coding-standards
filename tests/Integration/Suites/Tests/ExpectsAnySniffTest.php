<?php


class ExpectsAnySniffTest extends SniffTest
{
    /**
     * @return string
     */
    protected function getFileUnderTest()
    {
        return '../../src/Brera/Sniffs/Tests/ExpectsAnySniff.php';
    }

    /**
     * @test
     */
    public function itShouldAddAnErrorIfExpectsAnyIsUsedInATest()
    {
        $code = '$mock->expects($this->any())->method(\'foo\');';

        $phpCSFile = $this->processCode($code);

        $error = $this->getFirstErrorMessage($phpCSFile->getWarnings());
        $expectedError = 'Setting expects{$this->any()) on mocks can (and should) be omitted since PHPUnit version 4';

        $this->assertEquals($expectedError, $error);
    }
}
