<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->integer("bingo_venue_id");
            $table->string("title");
            $table->string("first_name");
            $table->string("last_name");
            $table->dateTime("dob");
            $table->integer("country");
            $table->integer("state");
            $table->integer("city");
            $table->string("zip");
            $table->text("address");
            $table->string("pagcor_pts_number")->nullable();
            $table->string("gov_photo_proof")->nullable();
            $table->string("selfie_gov_proof")->nullable();
            $table->json("other_proof_docs")->nullable();
            $table->integer("activated")->default(0);
            $table->softDeletes();
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
        Schema::dropIfExists('customers');
    }
}
