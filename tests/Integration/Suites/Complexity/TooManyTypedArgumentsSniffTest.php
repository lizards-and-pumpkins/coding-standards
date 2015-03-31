<?php

class TooManyTypedArgumentsSniffTest extends SniffTest
{
    /**
     * @return string
     */
    protected function getFileUnderTest()
    {
        return '../../src/Brera/Sniffs/Complexity/TooManyTypedArgumentsSniff.php';
    }

    /**
     * @test
     */
    public function itShouldNotAddAnyWarningsIfNumberOfTypedArgumentsDoesNotExceedAllowed()
    {
        $code = 'public function processData(Foo $foo, Bar $bar, array $anArray, Baz $baz, $godKnowsWhatTypeIsIt) { }';

        $phpCSFile = $this->processCode($code);
        $errors = $phpCSFile->getWarnings();

        $this->assertEmpty($errors);
    }

    /**
     * @test
     */
    public function itShouldAddAWarningIfNumberOfTypedArgumentsExceedsAllowed()
    {
        $code = 'public function processData(Foo $foo, Bar $bar, array $anArray, Baz $baz, Qux $qux) { }';

        $phpCSFile = $this->processCode($code);
        $warning = $this->getFirstErrorMessage($phpCSFile->getWarnings());
        $expectedWarning = 'Too many objects passed to function';

        $this->assertEquals($expectedWarning, $warning);
    }
}
