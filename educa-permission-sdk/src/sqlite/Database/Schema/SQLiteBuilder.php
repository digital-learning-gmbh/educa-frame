<?php

declare(strict_types=1);


namespace StuPla\CloudSDK\sqlite\Database\Schema;

use Illuminate\Support\Str;

class SQLiteBuilder extends \Illuminate\Database\Schema\SQLiteBuilder
{
    public function dropAllTables()
    {
        if (Str::startsWith($this->connection->getDatabaseName(), ':named-memory:')) {
            $this->connection->select($this->grammar->compileEnableWriteableSchema());
            $this->connection->select($this->grammar->compileDropAllTables());
            $this->connection->select($this->grammar->compileDisableWriteableSchema());
            $this->connection->select($this->grammar->compileRebuild());
            return;
        }

        parent::dropAllTables();
    }
}