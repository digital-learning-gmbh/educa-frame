<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdditionalInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('additional_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('personalnummer')->nullable()->index();

            $table->string('title')->nullable();
            $table->enum('anrede', ['herr', 'frau', 'na','divers'])->nullable();
            $table->string('displayname')->nullable();
            //address
            $table->string('street')->nullable();
            $table->string('plz')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('additional_address')->nullable();
            $table->string('street2')->nullable();
            $table->string('plz2')->nullable();
            $table->string('city2')->nullable();
            $table->string('additional_address2')->nullable();
            $table->string('country2')->nullable();
            //contact
            $table->string('tel_business')->nullable();
            $table->string('tel_private')->nullable();
            $table->string('tel_other')->nullable();
            $table->string('mobile')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();
            $table->string('email_private')->nullable();
            $table->string('email_other')->nullable();
            $table->string('homepage')->nullable();
            //personal data
            $table->date('birthdate')->nullable();
            $table->string('birthplace')->nullable();
            $table->string('birthname')->nullable();
            $table->string('birthland')->nullable();
            $table->enum('gender', ['male', 'female', 'na','divers'])->nullable();
            $table->string('religion')->nullable();
            $table->enum('familienstand', ['ledig', 'verheiratet', 'geschieden', 'verwitwet'])->nullable();
            $table->string('schulabschluss')->nullable();
            $table->string('bundesland')->nullable();
            $table->string('nationalitaet')->nullable();
            $table->string('nationalitaet2')->nullable();
            $table->string('position')->nullable();
            $table->string('academic_degree')->nullable();
            //bank
            $table->string('blz')->nullable();
            $table->string('bank')->nullable();
            $table->string('kontoinhaber')->nullable();
            $table->string('kontonummer')->nullable();
            $table->string('iban')->nullable();
            $table->string('bic')->nullable();


            //MISC
            $table->string('notes')->nullable();
            $table->string('remarks')->nullable();

            // StuPla Cloud
            $table->boolean('stupla_cloud')->default(false);
            $table->string('stupla_identifier')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('additional_infos');
    }
}
