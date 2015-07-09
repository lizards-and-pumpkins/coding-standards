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

    public function testNoWarningsAddedIfNumberOfTypedArgumentsDoesNotExceedAllowed()
    {
        $code = 'public function processData(Foo $foo, Bar $bar, array $anArray, Baz $baz, $godKnowsWhatTypeIsIt) { }';

        $phpCSFile = $this->processCode($code);
        $errors = $phpCSFile->getWarnings();

        $this->assertEmpty($errors);
    }

    public function testWarningIsAddedIfNumberOfTypedArgumentsExceedsAllowed()
    {
        $code = 'public function processData(Foo $foo, Bar $bar, array $anArray, Baz $baz, Qux $qux) { }';

        $phpCSFile = $this->processCode($code);
        $warning = $this->getFirstErrorMessage($phpCSFile->getWarnings());
        $expectedWarning = 'Too many objects passed to function';

        $this->assertEquals($expectedWarning, $warning);
    }
}
