<?php

require_once 'SniffTest.php';

class GetMockBuilderSniffTest extends SniffTest
{
    /**
     * @return string
     */
    protected final function getFileUnderTest()
    {
        return '../../src/Brera/Sniffs/Tests/GetMockBuilderSniff.php';
    }

    /**
     * @test
     */
    public function itShouldAddAnErrorIfMockBuilderIsUsedToDisableOriginalConstructor()
    {
        $code = '<?php

    $mock = $this->getMockBuilder()
        ->disableOriginalConstructor()
        ->getMock();
';

        $phpCSFile = $this->sniffer->processFile('STDIN', $code);

        $error = $this->getFirstErrorMessage($phpCSFile->getErrors());
        $expectedError = 'getMock(Foo::class, [], [], \'\', false) must be used for disabling original constructor';

        $this->assertEquals($expectedError, $error);
    }

    /**
     * @test
     */
    public function itShouldNotAddAnyErrorsIfMockBuilderIsUsedNotOnlyToDisableOriginalConstructor()
    {
        $code = '<?php

    $mock = $this->getMockBuilder()
        ->disableOriginalConstructor()
        ->setMethods(["foo"])
        ->getMock();
';

        $phpCSFile = $this->sniffer->processFile('STDIN', $code);
        $errors = $phpCSFile->getErrors();

        $this->assertEmpty($errors);
    }
}
