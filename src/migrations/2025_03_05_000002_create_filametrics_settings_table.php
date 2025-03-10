<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilametricsSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('filametrics_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider'); // Name of the provider (e.g., Google)
            $table->string('view_id')->nullable(); // View ID for Google Analytics
            $table->text('service_account_credentials_json')->nullable(); // JSON credentials file
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('filametrics_settings');
    }
}
