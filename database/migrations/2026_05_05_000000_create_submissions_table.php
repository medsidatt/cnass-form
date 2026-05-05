<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();

            // Employé
            $table->string('nom_complet');
            $table->string('situation_familiale');
            $table->string('ci_employe')->nullable();
            $table->string('photo_employe')->nullable();

            // Père
            $table->string('nom_pere')->nullable();
            $table->string('ci_pere')->nullable();
            $table->string('photo_pere')->nullable();

            // Mère
            $table->string('nom_mere')->nullable();
            $table->string('ci_mere')->nullable();
            $table->string('photo_mere')->nullable();

            // Fratrie (JSON arrays)
            $table->json('freres')->nullable();   // [{ci, photo}, ...]
            $table->json('soeurs')->nullable();

            // Conjoint(e)
            $table->string('nom_conjoint')->nullable();
            $table->string('ci_conjoint')->nullable();
            $table->string('photo_conjoint')->nullable();

            // Descendants (JSON array)
            $table->json('descendants')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
