<?php

class ReflectionSniffTest extends SniffTest
{
    final protected function getFileUnderTest() : string
    {
        return 'src/LizardsAndPumpkins/Sniffs/Tests/ReflectionSniff.php';
    }

    /**
     * @dataProvider reflectionCodeProvider
     */
    public function testWarningIsAddedIfCodeContainsReflection(string $codeWithReflection)
    {
        $phpCSFile = $this->processCode($codeWithReflection);

        $warning = $this->getFirstErrorMessage($phpCSFile->getWarnings());
        $expectedWarning = 'Reflection usage detected.';

        $this->assertEquals($expectedWarning, $warning);
    }

    /**
     * @return array[]
     */
    public function reflectionCodeProvider() : array
    {
        return [
            ['$property = new \ReflectionProperty($object, $propertyName);'],
            ['$property = new ReflectionProperty($object, $propertyName);'],
            ['$method = new \ReflectionMethod($className, $methodName);'],
            ['$method = new ReflectionMethod($className, $methodName);'],
            ['$class = new \ReflectionClass($className);'],
            ['$class = new ReflectionClass($className);'],
        ];
    }
    
    public function testNoWarningAddedIfCodeContainsNoReflection()
    {
        $phpCSFile = $this->processCode('$foo = new \stdClass();');
        $this->assertEquals(0, $phpCSFile->getWarningCount());
    }
}
