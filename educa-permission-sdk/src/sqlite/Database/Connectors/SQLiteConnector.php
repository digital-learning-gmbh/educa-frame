<?php

namespace StuPla\CloudSDK\sqlite\Database\Connectors;

use Illuminate\Support\Str;

class SQLiteConnector extends \Illuminate\Database\Connectors\SQLiteConnector
{
    /** @var \PDO[] named in-memory database connections */
    protected static $named_connections = [];

    /**
     * Establish a database connection.
     *
     *
     * @param  array  $config
     * @return \PDO
     *
     * @throws \InvalidArgumentException
     */
    public function connect(array $config): \PDO
    {
        $name = $config['database'] ?? '';
        if (Str::startsWith($name, ':named-memory:')) {
            if (!isset(self::$named_connections[$name])) {
                $options = $this->getOptions($config);
                self::$named_connections[$name] = $this->createConnection('sqlite::memory:', $config, $options);
            }

            return self::$named_connections[$name];
        }

        return parent::connect($config);
    }
}