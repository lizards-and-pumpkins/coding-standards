<?php

require_once __DIR__ . '/../../../src/Brera/Sniffs/Tests/GetMockBuilderSniff.php';

/**
 * @covers Brera_Sniffs_Tests_GetMockBuilderSniff
 */
class Brera_Sniffs_Tests_GetMockBuilderSniffTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Brera_Sniffs_Tests_GetMockBuilderSniff
     */
    private $sniff;

    protected function setUp()
    {
        $this->sniff = new Brera_Sniffs_Tests_GetMockBuilderSniff();
    }

    /**
     * @test
     */
    public function itShouldReturnArrayContainingStringTokensCode()
    {
        $result = $this->sniff->register();
        $expectation = [T_STRING];

        $this->assertSame($expectation, $result);
    }

    /**
     * @test
     */
    public function itShouldAddAnErrorIfMockBuilderIsUsedToDisableOriginalConstructor()
    {
        $stubTokens = [
            ['content' => 'getMockBuilder'],
            ['content' => 'disableOriginalConstructor'],
            ['content' => 'getMock']
        ];

        $mockFile = $this->getMock(PHP_CodeSniffer_File::class, [], [], '', false);
        $mockFile->expects($this->once())
            ->method('getTokens')
            ->willReturn($stubTokens);
        $mockFile->expects($this->exactly(2))
            ->method('findNext')
            ->willReturnMap([[T_STRING, 1, null, false, null, false, 1], [T_STRING, 2, null, false, null, false, 2]]);
        $mockFile->expects($this->once())
            ->method('addError')
            ->with('getMock(Foo::class, [], [], \'\', false) must be used for disabling original constructor', 0);

        $this->sniff->process($mockFile, 0);
    }
}
