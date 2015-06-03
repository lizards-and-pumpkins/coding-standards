<?php

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
        $code = '$mock = $this->getMockBuilder(Foo::class)->disableOriginalConstructor()->getMock();';

        $phpCSFile = $this->processCode($code);

        $error = $this->getFirstErrorMessage($phpCSFile->getErrors());
        $expectedError = 'getMock(Foo::class, [], [], \'\', false) must be used for disabling original constructor';

        $this->assertEquals($expectedError, $error);
    }

    /**
     * @test
     */
    public function itShouldNotAddAnyErrorsIfMockBuilderIsUsedNotOnlyToDisableOriginalConstructor()
    {
        $code = '$mock = $this->getMockBuilder()->disableOriginalConstructor()->setMethods(["foo"])->getMock();';

        $phpCSFile = $this->processCode($code);
        $errors = $phpCSFile->getErrors();

        $this->assertEmpty($errors);
    }
}
