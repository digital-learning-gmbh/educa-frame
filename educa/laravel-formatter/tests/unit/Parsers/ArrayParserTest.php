<?php namespace SoapBox\Formatter\Test\Parsers;

use SoapBox\Formatter\Parsers\ArrayParser;
use SoapBox\Formatter\Parsers\Parser;
use SoapBox\Formatter\Test\TestCase;
use stdClass;

class ArrayParserTest extends TestCase
{

    public function testArrayParserIsInstanceOfParserInterface()
    {
        $parser = new ArrayParser(new \stdClass);
        $this->assertTrue($parser instanceof Parser);
    }

    public function testConstructorAcceptsSerializedArray()
    {
        $expected = [0, 1, 2];
        $parser   = new ArrayParser(serialize($expected));
        $this->assertEquals($expected, $parser->toArray());
    }

    public function testConstructorAcceptsObject()
    {
        $expected   = ['foo' => 'bar'];
        $input      = new stdClass;
        $input->foo = 'bar';
        $parser     = new ArrayParser($input);
        $this->assertEquals($expected, $parser->toArray());
    }

    public function testArrayParserThrowsExceptionWithInvalidInputOfEmptyString()
    {
        $this->expectException(\InvalidArgumentException::class);

        $parser = new ArrayParser('');
    }

    public function testtoArrayReturnsArray()
    {
        $parser = new ArrayParser(serialize([0, 1, 2]));
        $this->assertTrue(is_array($parser->toArray()));
    }

    public function testtoJsonReturnsJsonRepresentationOfArray()
    {
        $expected = '[0,1,2]';
        $parser   = new ArrayParser([0, 1, 2]);
        $this->assertEquals($expected, $parser->toJson());
    }

    public function testtoJsonReturnsJsonRepresentationOfNamedArray()
    {
        $expected = '{"foo":"bar"}';
        $parser   = new ArrayParser(['foo' => 'bar']);
        $this->assertEquals($expected, $parser->toJson());
    }

    public function testtoCSVFromArrayContainingContentWithCommasWorks()
    {
        $expected = "\"0\",\"1\",\"2\",\"3\"\n\"a\",\"b\",\"c,e\",\"d\"";
        $parser   = new ArrayParser(['a', 'b', 'c,e', 'd']);
        $this->assertEquals($expected, $parser->toCsv());
    }
}
