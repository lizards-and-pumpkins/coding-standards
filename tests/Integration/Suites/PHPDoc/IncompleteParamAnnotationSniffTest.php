<?php

class IncompleteParamAnnotationSniffTest extends SniffTest
{
    /**
     * @return string
     */
    protected function getFileUnderTest()
    {
        return '../../src/Brera/Sniffs/PHPDoc/IncompleteParamAnnotationSniff.php';
    }

    /**
     * @test
     */
    public function itShouldNotAddAnyErrorsIfParamAnnotationIsComplete()
    {
        $code = '
        /**
         * @param int $foo
         */
        function prepareFoo($foo) {}';

        $phpCSFile = $this->processCode($code);
        $errors = $phpCSFile->getErrors();

        $this->assertEmpty($errors);
    }

    /**
     * @test
     * @param string $incompleteParamAnnotation
     * @dataProvider incompleteParamAnnotationProvider
     */
    public function itShouldAddAnErrorIfVariableNameIsMissing($incompleteParamAnnotation)
    {
        $phpCSFile = $this->processCode($incompleteParamAnnotation);
        $error = $this->getFirstErrorMessage($phpCSFile->getErrors());
        $expectedErrorMessage = 'Parameter annotation is incomplete';

        $this->assertEquals($expectedErrorMessage, $error);
    }

    /**
     * @return array
     */
    public function incompleteParamAnnotationProvider()
    {
        return [
            ['
             /**
              * @param int
              */
              function prepareFoo($foo) {}
            '],
            ['
             /**
              * @param $foo
              */
              function prepareFoo($foo) {}
            '],
            ['
             /**
              * @param
              */
              function prepareFoo($foo) {}
            '],
        ];
    }
}
