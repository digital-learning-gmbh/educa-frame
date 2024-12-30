<?php

namespace StuPla\CloudSDK\sqlite;

use StuPla\CloudSDK\sqlite\Database\Connectors\SQLiteConnector;
use PHPUnit\Framework\TestCase;

class SQLiteConnectorTest extends TestCase
{

    public function testConnect()
    {
        $connector = new SQLiteConnector();

        $names = ['', 'foo', 'bar'];
        $connections = [];

        foreach ($names as $name) {
            $connection = $connector->connect(['database' => ':named-memory:'.$name]);
            $this->assertInstanceOf(\PDO::class, $connection);
            $connections[$name] = $connection;
        }

        foreach ($names as $name) {
            $connection = $connector->connect(['database' => ':named-memory:'.$name]);
            $this->assertSame($connections[$name], $connection);
        }

        $this->assertDistinct($connections);
    }

    /**
     * Assert that elements of the array are different from each other
     *
     * @param array $x
     */
    public function assertDistinct(array $x)
    {
        foreach ($x as $k1 => $v1) {
            foreach ($x as $k2 => $v2) {
                if ($k1 < $k2) {
                    $this->assertNotSame($v1, $v2);
                }
            }
        }
    }
}
