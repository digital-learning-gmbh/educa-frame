<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDokumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dokuments', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->nullable();
            $table->string('file_type')->nullable();
            $table->unsignedInteger('parent_id')->nullable();
            $table->enum('type', ['folder', 'file'])->default('file');
            $table->unsignedInteger('owner_id');
            $table->enum('owner_type', ['lehrer', 'user', 'cloudid'])->default('cloudid');
            $table->unsignedInteger('size')->nullable();
            $table->string('disk_name')->nullable(); // this is the filename is the document folder, null if document
            $table->string('checksum')->nullable();

            $table->string("access_hash")->default("");
            if ($this->isSqlite()) {
                $table->json("metadata")->nullable();
            } else {
                $table->json("metadata")->nullable();
            }
            $table->timestamps();
        });

        //Intermediate for referencing
        Schema::create('model_dokument', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedInteger('model_id')->nullable(); // id of the model
            $table->string('model_type')->nullable(); // 'schuler', 'curriculum', 'klasse'

            $table->unsignedInteger('dokument_id')->nullable();
            $table->foreign('dokument_id')
                ->references('id')->on('dokuments');


            $table->timestamps();
        });
    }

    private function isSqlite(): bool
    {
        return 'sqlite' === Schema::connection($this->getConnection())
                ->getConnection()
                ->getPdo()
                ->getAttribute(PDO::ATTR_DRIVER_NAME);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('model_dokument');
        Schema::dropIfExists('klasse_dokument');
        Schema::dropIfExists('dokuments');
    }
}
