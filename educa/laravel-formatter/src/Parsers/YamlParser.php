<?php namespace SoapBox\Formatter\Parsers;

use InvalidArgumentException;
use Spyc;

class YamlParser extends Parser {

	private $array;

	public function __construct($data) {
		if (is_string($data)) {
			$this->array = Spyc::YAMLLoadString($data);
		} else {
			throw new InvalidArgumentException(
				'YamlParser only accepts (string) [yaml] for $data.'
			);
		}
	}

	public function toArray() {
		return $this->array;
	}

}
