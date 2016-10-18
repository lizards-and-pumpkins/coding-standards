<?php

declare(strict_types=1);

class SuperfluousPHPDocSniffTest extends SniffTest
{
    final protected function getFileUnderTest() : string
    {
        return 'src/LizardsAndPumpkins/Sniffs/PHPDoc/SuperfluousPHPDocSniff.php';
    }

    public function testNoErrorsAddedIfAnnotationsAreRequiredAndSpecified()
    {
        $code = '
        /**
         * @param float $bar
         * @return string[]
         * @throws BarIsNotAnArrayException
         */
        public function getFoo($bar) : array
        {
            if (!is_float($bar)) {
                throw new BarIsNotAnArrayException();
            }

            return ["foo"];
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
     */
    public function testErrorIsAddedIfFunctionDoesNotRequireAnAnnotationButOneIsSpecified(string $superfluousAnnotation)
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
    public function getSuperfluousAnnotations() : array
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
     */
    public function testNoErrorsAddedIfPHPDocContainsAtLeastOneTestRelatedAnnotation(string $annotation)
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
    public function getTestRelatedAnnotations() : array
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
