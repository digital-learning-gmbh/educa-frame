<?php namespace SoapBox\Formatter\Test\Parsers;

use SoapBox\Formatter\Parsers\CsvParser;
use SoapBox\Formatter\Parsers\Parser;
use SoapBox\Formatter\Test\TestCase;

class CsvParserTest extends TestCase
{

    private $simpleCsv = 'foo,boo
bar,far';

    public function testCsvParserIsInstanceOfParserInterface()
    {
        $parser = new CsvParser('');
        $this->assertTrue($parser instanceof Parser);
    }
    
    public function testConstructorThrowsInvalidExecptionWhenArrayDataIsProvided()
    {
        $this->expectException(\InvalidArgumentException::class);
        
        $parser = new CsvParser([0, 1, 3]);
    }

    public function testtoArrayReturnsCsvArrayRepresentation()
    {
        $expected = [['foo' => 'bar', 'boo' => 'far']];
        $parser   = new CsvParser($this->simpleCsv);
        $this->assertEquals($expected, $parser->toArray());
    }

    public function testtoJsonReturnsJsonRepresentationOfNamedArray()
    {
        $expected = '[{"foo":"bar","boo":"far"}]';
        $parser   = new CsvParser($this->simpleCsv);
        $this->assertEquals($expected, $parser->toJson());
    }

}
