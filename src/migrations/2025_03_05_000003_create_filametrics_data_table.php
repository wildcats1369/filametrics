<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilametricsDataTable extends Migration
{
    public function up()
    {
        Schema::create('filametrics_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('site_id'); // Links to filametrics_sites
            $table->unsignedBigInteger('account_id'); // Links to filametrics_accounts
            $table->json('data'); // JSON column to store analytics data
            $table->timestamps();

            $table->foreign('site_id')->references('id')->on('filametrics_sites')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('filametrics_accounts')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('filametrics_data');
    }
}
