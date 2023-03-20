<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAPIConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('a_p_i_connections', function (Blueprint $table) {
            $table->id();
            $table->string('connection_name')->nullable();
            $table->string('type_auth')->nullable();
            $table->string('link_connect')->nullable();
            $table->string('client_id')->nullable();
            $table->string('token')->nullable();
            $table->string('scopes')->nullable();
            $table->string('retailer')->nullable();
            $table->boolean('active')->default(false);
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
        Schema::dropIfExists('a_p_i_connections');
    }
}
