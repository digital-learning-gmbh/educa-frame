<?php namespace SoapBox\Formatter\Test;

use SoapBox\Formatter\Formatter;

class ParserTest extends TestCase
{

    /**
     * A basic functional test for JSON to Array conversion
     *
     * @return void
     */
    public function testJsonToArray()
    {
        $data = '{"foo":"bar","bar":"foo"}';

        $actual   = Formatter::make($data, Formatter::JSON)->toArray();
        $expected = ['foo' => 'bar', 'bar' => 'foo'];

        $this->assertEquals($expected, $actual);
    }

    /**
     * A basic functional test for Array to JSON conversion
     *
     * @return void
     */
    public function testArrayToJson()
    {
        $data = ['foo' => 'bar', 'bar' => 'foo'];

        $actual   = Formatter::make($data, Formatter::ARR)->toJson();
        $expected = '{"foo":"bar","bar":"foo"}';

        $this->assertEquals($expected, $actual);
    }

    /**
     * A basic functional test for CSV data to array
     *
     * @return void
     */
    public function testCSVToArray()
    {
        $data = 'foo,bar,bing,bam,boom';

        $actual   = Formatter::make($data, Formatter::CSV)->toArray();
        $expected = ['foo', 'bar', 'bing', 'bam', 'boom'];

        $this->assertEquals($expected, $actual);
    }

    /**
     * A basic functional test for testJSONToXMLToArrayToJsonToArray data to array
     *
     * @return void
     */
    public function testJSONToXMLToArrayToJsonToArray()
    {
        $data = '{"foo":"bar","bar":"foo"}';

        $result = Formatter::make($data, Formatter::JSON)->toXml();
        $result = Formatter::make($result, Formatter::XML)->toArray();
        $result = Formatter::make($result, Formatter::ARR)->toJson();
        $actual = Formatter::make($result, Formatter::JSON)->toArray();

        $expected = ['foo' => 'bar', 'bar' => 'foo'];

        $this->assertEquals($expected, $actual);
    }

    public function testMultiDimensionalArrayFromJsonToCsv()
    {
        $expected = "\"simple\",\"date\",\"time\",\"duration_onset\",\"devicename\",\"calc_data.0.0\",\"calc_data.0.1\",\"calc_data.0.2\",\"calc_data.0.3\",\"calc_data.0.4\",\"calc_data.0.5\",\"calc_data.1.0\",\"calc_data.1.1\",\"calc_data.1.2\",\"calc_data.1.3\",\"calc_data.1.4\",\"calc_data.1.5\"\n\"118\",\"2014-05-20 21:03:59.333\",\"4067\",\"\",\"My Device\",\"1400609039\",\"0\",\"37\",\"0\",\"0\",\"1\",\"1400609039\",\"0\",\"37\",\"0\",\"0\",\"1\"";

        $json =
            '{
	"simple":"118",
	"date":"2014-05-20 21:03:59.333",
	"time":"4067",
	"duration_onset":null,
	"devicename":"My Device",
	"calc_data":[
		[
			1400609039,
			0,
			37,
			0,
			0,
			1
		],
		[
			1400609039,
			0,
			37,
			0,
			0,
			1
		]
	]
}';
        $jsonParser = Formatter::make($json, Formatter::JSON);

        $this->assertEquals($expected, $jsonParser->toCsv());
    }

}
