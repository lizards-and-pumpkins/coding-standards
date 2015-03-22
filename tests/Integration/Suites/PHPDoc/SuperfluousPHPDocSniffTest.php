<?php

class SuperfluousPHPDocSniffTest extends SniffTest
{
    /**
     * @return string
     */
    protected final function getFileUnderTest()
    {
        return '../../src/Brera/Sniffs/PHPDoc/SuperfluousPHPDocSniff.php';
    }

    /**
     * @test
     */
    public function itShouldNotAddAnyErrorsIfAnnotationsAreRequiredAndSpecified()
    {
        $code = '
        /**
         * @param float $bar
         * @return string
         * @throws BarIsNotAnArrayException
         */
        public function getFoo($bar)
        {
            if (!is_float($bar)) {
                throw new BarIsNotAnArrayException();
            }

            return "foo";
        }';

        $phpCSFile = $this->processCode($code);
        $errors = $phpCSFile->getErrors();

        $this->assertEmpty($errors);
    }

    /**
     * @test
     */
    public function itShouldNotAddAnyErrorsIfMethodIsAbstractAndReturnTypeIsSpecified()
    {
        $code = '
        /**
         * @return void
         */
        abstract protected function prepareFoo();
';

        $phpCSFile = $this->processCode($code);
        $errors = $phpCSFile->getErrors();

        $this->assertEmpty($errors);
    }

    /**
     * @test
     */
    public function itShouldNotAddAnyErrorsIfMethodIsAnInterfaceAndReturnTypeIsSpecified()
    {
        $code = '
        /**
         * @return void
         */
        public function prepareFoo();
';

        $phpCSFile = $this->processCode($code);
        $errors = $phpCSFile->getErrors();

        $this->assertEmpty($errors);
    }

    /**
     * @test
     */
    public function itShouldAddAnErrorIfFunctionDoesNotRequireAnAnnotationButOneIsSpecified()
    {
        $code = '
        /**
         * @return void
         */
        public function prepareFoo()
        {
            $this->foo = "foo";
        }
';

        $phpCSFile = $this->processCode($code);
        $error = $this->getFirstErrorMessage($phpCSFile->getErrors());
        $expectedError = 'Superfluous PHPDoc';

        $this->assertEquals($expectedError, $error);
    }
}
