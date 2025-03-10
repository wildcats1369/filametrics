<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilametricsAccountsTable extends Migration
{
    public function up()
    {
        Schema::create('filametrics_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('site_id'); // Links to filametrics_sites
            $table->string('name'); // Account name
            $table->string('label')->nullable(); // Optional label
            $table->enum('type', ['text', 'numeric', 'upload']); // Type of account input
            $table->string('provider'); // Third-party provider (e.g., Google, Moz, etc.)
            $table->timestamps();

            $table->foreign('site_id')->references('id')->on('filametrics_sites')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('filametrics_accounts');
    }
}
