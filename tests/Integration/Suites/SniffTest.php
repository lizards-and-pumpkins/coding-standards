<?php

abstract class SniffTest extends \PHPUnit_Framework_TestCase
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
        $this->sniffer->registerSniffs([$this->getFileUnderTest()], '');
        $this->sniffer->populateTokenListeners();
    }

    protected function tearDown()
    {
        $_SERVER['argv'] = $this->originalArgs;
    }

    /**
     * @return string
     */
    abstract protected function getFileUnderTest();

    /**
     * @param string $code
     * @return PHP_CodeSniffer_File
     */
    final protected function processCode($code)
    {
        if ('<?php ' !== substr($code, 0, 6)) {
            $code = '<?php ' . $code;
        }

        return $this->sniffer->processFile('STDIN', $code);
    }

    /**
     * @param array[] $errors
     * @return string
     */
    final protected function getFirstErrorMessage(array $errors)
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
