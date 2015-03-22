<?php

class GetMockBuilderSniffTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PHP_CodeSniffer
     */
    private $sniffer;

    /**
     * @var string[]
     */
    private $originalArgs;

    protected function setUp()
    {
        if (!defined('PHP_CODESNIFFER_IN_TESTS')) {
            define('PHP_CODESNIFFER_IN_TESTS', true);
        }

        $this->originalArgs = $_SERVER['argv'];
        $_SERVER['argv'] = [];

        $this->sniffer = new PHP_CodeSniffer();
        $this->sniffer->registerSniffs(['../../src/Brera/Sniffs/Tests/GetMockBuilderSniff.php'], '');
        $this->sniffer->populateTokenListeners();
    }

    protected function tearDown()
    {
        $_SERVER['argv'] = $this->originalArgs;
    }

    /**
     * @test
     */
    public function itShouldAddAnErrorIfMockBuilderIsUsedToDisableOriginalConstructor()
    {
        $code = '<?php

    $mock = $this->getMockBuilder()
        ->disableOriginalConstructor()
        ->getMock();
';

        $phpCSFile = $this->sniffer->processFile('STDIN', $code);

        $error = $this->getFirstErrorMessage($phpCSFile->getErrors());
        $expectedError = 'getMock(Foo::class, [], [], \'\', false) must be used for disabling original constructor';

        $this->assertEquals($expectedError, $error);
    }

    /**
     * @test
     */
    public function itShouldNotAddAnyErrorsIfMockBuilderIsUsedNotOnlyToDisableOriginalConstructor()
    {
        $code = '<?php

    $mock = $this->getMockBuilder()
        ->disableOriginalConstructor()
        ->setMethods(["foo"])
        ->getMock();
';

        $phpCSFile = $this->sniffer->processFile('STDIN', $code);
        $errors = $phpCSFile->getErrors();

        $this->assertEmpty($errors);
    }

    /**
     * @param array[] $errors
     * @return string
     */
    private function getFirstErrorMessage(array $errors)
    {
        $error = array_shift($errors);
        if (!is_array($error)) {
            return '';
        }

        $error = array_shift($error);
        if (!is_array($error)) {
            return '';
        }

        $error = array_shift($error);
        if (!is_array($error) || !isset($error['message'])) {
            return '';
        }

        return $error['message'];
    }
}
