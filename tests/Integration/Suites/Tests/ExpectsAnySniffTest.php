<?php


class ExpectsAnySniffTest extends SniffTest
{
    final protected function getFileUnderTest() : string
    {
        return 'src/LizardsAndPumpkins/Sniffs/Tests/ExpectsAnySniff.php';
    }

    /**
     * @test
     * @dataProvider expectsAnyMethodCallDataProvider
     */
    public function itShouldAddAnErrorIfExpectsAnyIsUsedInATest(string $code)
    {
        $phpCSFile = $this->processCode($code);

        $warning = $this->getFirstErrorMessage($phpCSFile->getWarnings());
        $expectedWarning = 'Setting expects($this->any()) on mocks can (and should) be omitted since PHPUnit version 4';

        $this->assertEquals($expectedWarning, $warning);
    }

    /**
     * @return array[]
     */
    public function expectsAnyMethodCallDataProvider() : array
    {
        return [
            'no-whitespace' => ['$mock->expects($this->any())->method(\'foo\')'],
            'property-mock' => ['$this->mock->expects($this->any())->method(\'foo\')'],
            'only-spaces' => [' $mock->expects( $this -> any() ) ->method( \'foo\' )  '],
            'only-newlines' => [
                '$mock->expects(' . PHP_EOL .
                '$this->any()' . PHP_EOL .
                ')' . PHP_EOL .
                '->method(' . PHP_EOL .
                '\'foo\'' . PHP_EOL .
                ')'
            ],
            'spaces-and-newlines' => [
                '$mock -> expects (' . PHP_EOL .
                '$this->any (   )' . PHP_EOL .
                ')' . PHP_EOL .
                '  -> method(' . PHP_EOL .
                '\'foo\'' . PHP_EOL .
                '  )'
            ],
        ];
    }
}
