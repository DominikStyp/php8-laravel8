<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateProductsCategoriesPivot extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_product', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("product_id");
            $table->unsignedBigInteger("category_id");
            $table->timestamps();
            $table->foreign("product_id")->references("id")->on("products");
            $table->foreign("category_id")->references("id")->on("categories");
            // in newer version can be defined as
            // $table->foreignId('product_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("SET FOREIGN_KEY_CHECKS=0;");
        Schema::dropIfExists('category_product');
        DB::statement("SET FOREIGN_KEY_CHECKS=1;");

    }
}
