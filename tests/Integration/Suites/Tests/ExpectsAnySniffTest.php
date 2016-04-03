<?php


class ExpectsAnySniffTest extends SniffTest
{
    /**
     * @return string
     */
    protected function getFileUnderTest()
    {
        return 'src/LizardsAndPumpkins/Sniffs/Tests/ExpectsAnySniff.php';
    }

    /**
     * @test
     * @dataProvider expectsAnyMethodCallDataProvider
     */
    public function itShouldAddAnErrorIfExpectsAnyIsUsedInATest($code)
    {
        $phpCSFile = $this->processCode($code);

        $warning = $this->getFirstErrorMessage($phpCSFile->getWarnings());
        $expectedWarning = 'Setting expects($this->any()) on mocks can (and should) be omitted since PHPUnit version 4';

        $this->assertEquals($expectedWarning, $warning);
    }

    /**
     * @return array[]
     */
    public function expectsAnyMethodCallDataProvider()
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
