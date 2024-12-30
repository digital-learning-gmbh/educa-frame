<?php namespace SoapBox\Formatter\Parsers;

use Illuminate\Support\Str;
use SoapBox\Formatter\ArrayHelpers;
use Spyc;

/**
 * Parser Interface
 *
 * This interface describes the abilities of a parser which is able to transform
 * inputs to the object type.
 */
abstract class Parser
{

    /**
     * Constructor is used to initialize the parser
     *
     * @param mixed $data The input sharing a type with the parser
     */
    abstract public function __construct($data);

    /**
     * Used to retrieve a (php) array representation of the data encapsulated within our Parser.
     *
     * @return array
     */
    abstract public function toArray();

    /**
     * Return a json representation of the data stored in the parser
     *
     * @return string A json string representing the encapsulated data
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Return a yaml representation of the data stored in the parser
     *
     * @return string A yaml string representing the encapsulated data
     */
    public function toYaml()
    {
        return Spyc::YAMLDump($this->toArray());
    }

    /**
     * To XML conversion
     *
     * @param  mixed       $data
     * @param  null        $structure
     * @param  null|string $basenode
     * @param  null|string $encoding
     * @return string
     */
    private function xmlify($data, $structure = null, $basenode = 'xml', $encoding = 'utf-8', $formatted = false)
    {
        // turn off compatibility mode as simple xml throws a wobbly if you don't.
        if (ini_get('zend.ze1_compatibility_mode') == 1) {
            ini_set('zend.ze1_compatibility_mode', 0);
        }

        if ($structure == null) {
            $structure = simplexml_load_string("<?xml version='1.0' encoding='$encoding'?><$basenode />");
        }

        // Force it to be something useful
        if (!is_array($data) && !is_object($data)) {
            $data = (array) $data;
        }

        foreach ($data as $key => $value) {
            // checking for xml tag having attributes
            if ($key === '@attributes') {
                //STRICT IS NECESSARY because if key is numeric @attributes will be cast to integer and 0 == 0!
                foreach ($data[$key] as $attrName => $attrValue) {
                    $structure->addAttribute($attrName, $attrValue);
                }
            } else {
                // convert our booleans to 0/1 integer values so they are
                // not converted to blanks.
                if (is_bool($value)) {
                    $value = (int) $value;
                }

                // no numeric keys in our xml please!
                if (is_numeric($key)) {
                    // make string key...
                    if (isset($value['@name']) && is_string($value['@name'])) {
                        $key = $value['@name'];
                    } else {
                        $key = (Str::singular($basenode) != $basenode) ? Str::singular($basenode) : 'item';
                    }

                    unset($value['@name']);
                }

                // replace anything not alpha numeric AND '@' because of '@attributes'
                $key = preg_replace('/[^a-z_@\-0-9]/i', '', $key);

                // if there is another array found recursively call this function
                if (is_array($value) or is_object($value)) {
                    $node = $structure->addChild($key);

                    // recursive call if value is not empty
                    if (!empty($value)) {
                        $this->xmlify($value, $node, $key);
                    }
                } else {
                    // add single node.
                    $value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, "UTF-8");
                    $structure->addChild($key, $value);
                }
            }
        }

        // return formatted xml
        if ($formatted) {
            $dom               = dom_import_simplexml($structure)->ownerDocument;
            $dom->formatOutput = true;
            return $dom->saveXML();
        }

        // pass back as string. or simple xml object if you want!
        return $structure->asXML();
    }

    /**
     * Return an xml representation of the data stored in the parser
     *
     * @param  string $baseNode
     * @param  string $encoding
     * @param  bool   $formatted
     * @return string An xml string representing the encapsulated data
     */
    public function toXml($baseNode = 'xml', $encoding = 'utf-8', $formatted = false)
    {
        return $this->xmlify($this->toArray(), null, $baseNode, $encoding, $formatted);
    }

    private function csvify($data)
    {
        $results = [];
        foreach ($data as $row) {
            $results[] = array_values(ArrayHelpers::dot($row));
        }
        return $results;
    }

    /**
     * Ported from laravel-formatter
     * https://github.com/SoapBox/laravel-formatter
     *
     * Return a csv representation of the data stored in the parser
     * @author  Daniel Berry <daniel@danielberry.me>
     * @license MIT License (see LICENSE.readme included in the bundle)
     *
     * @return string An csv string representing the encapsulated data
     */
    public function toCsv($newline = "\n", $delimiter = ",", $enclosure = '"', $escape = "\\")
    {
        $data = $this->toArray();

        if (ArrayHelpers::isAssociative($data) || !is_array($data[0])) {
            $data = [$data];
        }

        $escaper = function ($items) use ($enclosure, $escape) {
            return array_map(function ($item) use ($enclosure, $escape) {
                return str_replace($enclosure, $escape . $enclosure, $item);
            }, $items);
        };

        $headings = ArrayHelpers::dotKeys($data[0]);
        $result   = [];

        foreach ($data as $row) {
            $result[] = array_values(ArrayHelpers::dot($row));
        }

        $data = $result;

        $output = $enclosure . implode($enclosure . $delimiter . $enclosure, $escaper($headings)) . $enclosure . $newline;

        foreach ($data as $row) {
            $output .= $enclosure . implode($enclosure . $delimiter . $enclosure, $escaper((array) $row)) . $enclosure . $newline;
        }

        return rtrim($output, $newline);
    }
}
