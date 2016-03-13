<?php

class GetMockBuilderSniffTest extends SniffTest
{
    /**
     * @return string
     */
    protected final function getFileUnderTest()
    {
        return 'src/LizardsAndPumpkins/Sniffs/Tests/GetMockBuilderSniff.php';
    }

    public function testErrorIsAddedIfMockBuilderIsUsedToDisableOriginalConstructor()
    {
        $code = '$mock = $this->getMockBuilder(Foo::class)->disableOriginalConstructor()->getMock();';

        $phpCSFile = $this->processCode($code);

        $error = $this->getFirstErrorMessage($phpCSFile->getErrors());
        $expectedError = 'getMock(Foo::class, [], [], \'\', false) must be used for disabling original constructor';

        $this->assertEquals($expectedError, $error);
    }

    public function testNoErrorsAddedIfMockBuilderIsUsedNotOnlyToDisableOriginalConstructor()
    {
        $code = '$mock = $this->getMockBuilder()->disableOriginalConstructor()->setMethods(["foo"])->getMock();';

        $phpCSFile = $this->processCode($code);
        $errors = $phpCSFile->getErrors();

        $this->assertEmpty($errors);
    }
}
