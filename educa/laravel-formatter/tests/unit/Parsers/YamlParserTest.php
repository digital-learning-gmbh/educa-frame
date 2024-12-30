<?php namespace SoapBox\Formatter\Test\Parsers;

use SoapBox\Formatter\Parsers\Parser;
use SoapBox\Formatter\Parsers\XmlParser;
use SoapBox\Formatter\Parsers\YamlParser;
use SoapBox\Formatter\Test\TestCase;

class YamlParserTest extends TestCase
{

    public function testYamlParserIsInstanceOfParserInterface()
    {
        $parser = new YamlParser('');
        $this->assertTrue($parser instanceof Parser);
    }

    public function testtoArrayReturnsArrayRepresenationOfYamlObject()
    {
        $expected = ['foo' => 'bar'];
        $parser   = new XmlParser('<xml><foo>bar</foo></xml>');
        $x        = new YamlParser($parser->toYaml());
        $this->assertEquals($expected, $x->toArray());
    }

}
