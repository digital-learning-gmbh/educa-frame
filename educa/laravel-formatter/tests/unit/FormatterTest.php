<?php namespace SoapBox\Formatter\Test;

use SoapBox\Formatter\Formatter;

class FormatterTest extends TestCase
{

    public function testFormatterProvidesCsvConstant()
    {
        $expected = 'csv';
        $actual   = Formatter::CSV;

        $this->assertEquals($expected, $actual);
    }

    public function testFormatterProvidesJsonConstant()
    {
        $expected = 'json';
        $actual   = Formatter::JSON;

        $this->assertEquals($expected, $actual);
    }

    public function testFormatterProvidesXmlConstant()
    {
        $expected = 'xml';
        $actual   = Formatter::XML;

        $this->assertEquals($expected, $actual);
    }

    public function testFormatterProvidesArrayConstant()
    {
        $expected = 'array';
        $actual   = Formatter::ARR;

        $this->assertEquals($expected, $actual);
    }

    public function testFormatterProvidesYamlConstant()
    {
        $expected = 'yaml';
        $actual   = Formatter::YAML;

        $this->assertEquals($expected, $actual);
    }

    public function testFormatterMakeThrowsInvalidTypeException()
    {
        $this->expectException(\InvalidArgumentException::class);

        $formatter = Formatter::make('', 'blue');
    }

    public function testFormatterMakeReturnsInstanceOfFormatter()
    {
        $formatter = Formatter::make('', Formatter::CSV);
        $this->assertTrue($formatter instanceof Formatter);
    }

}
