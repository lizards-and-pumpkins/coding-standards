<?php

class SuperfluousPHPDocSniffTest extends SniffTest
{
    /**
     * @return string
     */
    protected final function getFileUnderTest()
    {
        return '../../src/LizardsAndPumpkins/Sniffs/PHPDoc/SuperfluousPHPDocSniff.php';
    }

    public function testNoErrorsAddedIfAnnotationsAreRequiredAndSpecified()
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

    public function testNoErrorsAddedIfMethodIsAbstractAndReturnTypeIsSpecified()
    {
        $code = '
        /**
         * @return void
         */
        abstract protected function prepareFoo();';

        $phpCSFile = $this->processCode($code);
        $errors = $phpCSFile->getErrors();

        $this->assertEmpty($errors);
    }

    public function testNoErrorsAddedIfMethodIsAnInterfaceAndReturnTypeIsSpecified()
    {
        $code = '
        /**
         * @return void
         */
        public function prepareFoo();';

        $phpCSFile = $this->processCode($code);
        $errors = $phpCSFile->getErrors();

        $this->assertEmpty($errors);
    }

    /**
     * @dataProvider getSuperfluousAnnotations
     * @param string $superfluousAnnotation
     */
    public function testErrorIsAddedIfFunctionDoesNotRequireAnAnnotationButOneIsSpecified($superfluousAnnotation)
    {
        $code = '
        /**
         * ' . $superfluousAnnotation . '
         */
        public function prepareFoo() { }';

        $phpCSFile = $this->processCode($code);
        $error = $this->getFirstErrorMessage($phpCSFile->getErrors());
        $expectedError = 'Superfluous PHPDoc';

        $this->assertEquals($expectedError, $error);
    }

    /**
     * @return array[]
     */
    public function getSuperfluousAnnotations()
    {
        return [
            ['@return void'],
            ['@throws \Exception']
        ];
    }

    public function testNoErrorsAddedIfPHPDocHasAtLeastOneUntypedArgument()
    {
        $code = '
        /**
         * @param mixed[] $data
         */
        public function prepareData(array $data) { }';

        $phpCSFile = $this->processCode($code);
        $errors = $phpCSFile->getErrors();

        $this->assertEmpty($errors);
    }

    public function testErrorIsAddedIfPHPDocContainsFromOnlyTypedArguments()
    {
        $code = '
        /**
         * @param Foo $foo
         */
        public function prepareFoo(Foo $foo) { }';

        $phpCSFile = $this->processCode($code);
        $error = $this->getFirstErrorMessage($phpCSFile->getErrors());
        $expectedError = 'Superfluous PHPDoc';

        $this->assertEquals($expectedError, $error);
    }

    /**
     * @dataProvider getTestRelatedAnnotations
     * @param string $annotation
     */
    public function testNoErrorsAddedIfPHPDocContainsAtLeastOneTestRelatedAnnotation($annotation)
    {
        $code = '
        /**
         * ' . $annotation . '
         */
        public function foo() { }';

        $phpCSFile = $this->processCode($code);
        $errors = $phpCSFile->getErrors();

        $this->assertEmpty($errors);
    }

    /**
     * @return array[]
     */
    public function getTestRelatedAnnotations()
    {
        return [
            ['@depends'],
            ['@dataProvider'],
            ['@runInSeparateProcess'],
            ['@before'],
            ['@after'],
            ['@requires'],
        ];
    }
}
