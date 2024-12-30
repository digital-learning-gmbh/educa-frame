<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdressbookEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adressbook_entries', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("role")->nullable();
            $table->string("telephone")->nullable();
            $table->string("location")->nullable();
            $table->string("email")->nullable();
            $table->string("mobil")->nullable();
            $table->longText("description")->nullable();

            $table->unsignedBigInteger('cloudid')->nullable(); // nullable
            $table->foreign('cloudid')
                ->references('id')->on('cloud_i_d_s');

            $table->boolean("isMailAllowed")->default(true);
            $table->boolean("isMailAnonymized")->default(false);
            $table->boolean("isChatAllowed")->default(true);

            $table->timestamps();
        });

        Schema::create('adressbook_entry_role', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->bigInteger("role_id");
            $table->bigInteger("adressbook_entry_id");
        });

        Schema::create('adressbook_group', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->bigInteger("group_id");
            $table->bigInteger("adressbook_entry_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adressbook_group');
        Schema::dropIfExists('adressbook_entry_role');
        Schema::dropIfExists('adressbook_entries');
    }
}
