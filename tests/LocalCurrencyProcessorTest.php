<?php

namespace Tests;

use App\LocalCurrencyProcessor;
use App\ProcessorException;
use PHPUnit\Framework\TestCase;

class LocalCurrencyProcessorTest extends TestCase
{
    private $processor;

    protected function setUp(): void
    {
        $this->processor = new LocalCurrencyProcessor('input.txt','config.ini');
    }


    public function testProcess()
    {
        $filename = 'test_input.txt';
        file_put_contents($filename, '{"bin":"45717360","amount":"100.00","currency":"USD"}' . PHP_EOL);

        $reflection = new \ReflectionClass($this->processor);
        $property = $reflection->getProperty('input');
        $property->setAccessible(true);
        $property->setValue($this->processor, $filename);

        $method = $reflection->getMethod('process');
        $method->setAccessible(true);
        $method->invoke($this->processor);

        $property = $reflection->getProperty('amntsFixed');
        $property->setAccessible(true);
        $amntsFixed = $property->getValue($this->processor);

        $this->assertCount(1, $amntsFixed);
        $this->assertEquals(0.9090909090909091, $amntsFixed[0]);

        unlink($filename);
    }

    // Тест для метода printResult с использованием рефлексии
    public function testPrintResult()
    {
        $reflection = new \ReflectionClass($this->processor);
        $property = $reflection->getProperty('amntsFixed');
        $property->setAccessible(true);
        $property->setValue($this->processor, [0.9090909090909091]);

        ob_start();
        $this->processor->printResult();
        $output = ob_get_clean();

        $this->assertEquals("0.9090909090909091\n", $output);
    }

    // Тест для fetchBinResults с выбросом исключения ProcessorException
    public function testFetchBinResultsThrowsProcessorException()
    {
        $reflection = new \ReflectionClass($this->processor);
        $method = $reflection->getMethod('fetchBinResults');
        $method->setAccessible(true);

        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('error fetching BIN results!');

        $method->invoke($this->processor, 'invalid');
    }

    // Тест для fetchExchangeRate с выбросом исключения ProcessorException
    public function testFetchExchangeRateThrowsProcessorException()
    {
        $reflection = new \ReflectionClass($this->processor);
        $method = $reflection->getMethod('fetchExchangeRate');
        $method->setAccessible(true);

        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('error fetching exchange rates!');

        $method->invoke($this->processor, 'invalid');
    }

    // Тест для process с выбросом исключения ProcessorException на недействительном файле
    public function testProcessThrowsProcessorExceptionOnInvalidFile()
    {
        $this->processor = new LocalCurrencyProcessor('invalid_input.txt','config.ini');

        $this->expectException(ProcessorException::class);
        $this->expectExceptionMessage('Invalid input File');

        $reflection = new \ReflectionClass($this->processor);
        $method = $reflection->getMethod('process');
        $method->setAccessible(true);

        $method->invoke($this->processor);
    }
}