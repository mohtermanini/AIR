<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_term', function (Blueprint $table) {
            $table->string("term");
            $table->foreignId("document_id");
            $table->integer("frequency")->unsigned()->default(1);

            $table->foreign("term")->references("term")->on("terms")->onUpdate("cascade")->onDelete("cascade");
            $table->foreign("document_id")->references("id")->on("documents")->onUpdate("cascade")->onDelete("cascade");

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
        Schema::dropIfExists('document_term');
    }
};
