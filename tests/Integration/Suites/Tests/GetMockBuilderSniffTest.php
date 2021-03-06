<?php

declare(strict_types=1);

class GetMockBuilderSniffTest extends SniffTest
{
    final protected function getFileUnderTest() : string
    {
        return 'src/LizardsAndPumpkins/Sniffs/Tests/GetMockBuilderSniff.php';
    }

    public function testErrorIsAddedIfMockBuilderIsUsedToDisableOriginalConstructor()
    {
        $code = '$mock = $this->getMockBuilder(Foo::class)->disableOriginalConstructor()->getMock();';

        $phpCSFile = $this->processCode($code);

        $error = $this->getFirstErrorMessage($phpCSFile->getErrors());
        $expectedError = 'createMock() must be used for disabling original constructor';

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
