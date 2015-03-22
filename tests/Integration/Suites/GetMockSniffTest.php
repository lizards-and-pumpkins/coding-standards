<?php

require_once 'SniffTest.php';

class GetMockSniffTest extends SniffTest
{
    /**
     * @return string
     */
    protected final function getFileUnderTest()
    {
        return '../../src/Brera/Sniffs/Tests/GetMockSniff.php';
    }

    /**
     * @test
     */
    public function itShouldAddAnErrorIfGetMockIsUsedNotOnlyToDisableOriginalConstructor()
    {
        $code = '$mock = $this->getMock(Foo::class, ["getFoo"]);';

        $phpCSFile = $this->processCode($code);

        $error = $this->getFirstErrorMessage($phpCSFile->getErrors());
        $expectedError = 'Optional arguments of getMock() can be only used to disable original constructor';

        $this->assertEquals($expectedError, $error);
    }

    /**
     * @test
     */
    public function itShouldNotAddAnyErrorsIfGetMockIsUsedWithNoOptionalArguments()
    {
        $code = '$mock = $this->getMock(Foo::class);';

        $phpCSFile = $this->processCode($code);
        $errors = $phpCSFile->getErrors();

        $this->assertEmpty($errors);
    }

    /**
     * @test
     */
    public function itShouldNotAddAnyErrorsIfGetMockIsUsedOnlyToDisableOriginalConstructor()
    {
        $code = '$mock = $this->getMock(Foo::class, [], [], "", false);';

        $phpCSFile = $this->processCode($code);
        $errors = $phpCSFile->getErrors();

        $this->assertEmpty($errors);
    }
}
