<?php

declare(strict_types=1);

class TooManyTypedArgumentsSniffTest extends SniffTest
{
    final protected function getFileUnderTest() : string
    {
        return 'src/LizardsAndPumpkins/Sniffs/Complexity/TooManyTypedArgumentsSniff.php';
    }

    public function testNoWarningsAddedIfNumberOfTypedArgumentsDoesNotExceedAllowed()
    {
        $code = <<<EOT
public function processData(Foo \$foo, Bar \$bar, array \$anArray, string \$aString, Baz \$baz, \$godKnowsWhatTypeIsIt)
{
}
EOT;

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
