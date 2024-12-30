<?php namespace SoapBox\Formatter\Test\Parsers;

use SoapBox\Formatter\Parsers\Parser;
use SoapBox\Formatter\Parsers\XmlParser;
use SoapBox\Formatter\Test\TestCase;

class XmlParserTest extends TestCase
{

    public function testXmlParserIsInstanceOfParserInterface()
    {
        $parser = new XmlParser('');
        $this->assertTrue($parser instanceof Parser);
    }

    public function testtoArrayReturnsArrayRepresenationOfXmlObject()
    {
        $expected = ['foo' => 'bar'];
        $parser   = new XmlParser('<xml><foo>bar</foo></xml>');
        $this->assertEquals($expected, $parser->toArray());
    }

    public function testtoJsonReturnsJsonRepresentationOfXmlObject()
    {
        $expected = '{"foo":"bar"}';
        $parser   = new XmlParser('<xml><foo>bar</foo></xml>');
        $this->assertEquals($expected, $parser->toJson());
    }
}
