<?php

class IncompleteParamAnnotationSniffTest extends SniffTest
{
    /**
     * @return string
     */
    protected function getFileUnderTest()
    {
        return '../../src/LizardsAndPumpkins/Sniffs/PHPDoc/IncompleteParamAnnotationSniff.php';
    }

    public function testNoErrorsAddedIfParamAnnotationIsComplete()
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
     * @dataProvider incompleteParamAnnotationProvider
     * @param string $incompleteParamAnnotation
     */
    public function testErrorIsAddedIfVariableNameIsMissing($incompleteParamAnnotation)
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
