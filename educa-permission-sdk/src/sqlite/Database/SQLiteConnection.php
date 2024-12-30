<?php

declare(strict_types=1);

namespace StuPla\CloudSDK\sqlite\Database;

use Illuminate\Database\SQLiteConnection as ParentSQLiteConnection;

class SQLiteConnection extends ParentSQLiteConnection
{
    public function getSchemaBuilder()
    {
        if (is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }

        return new Schema\SQLiteBuilder($this);
    }
}