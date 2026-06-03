<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Profile (1:1 with user) ──
        Schema::create('user_profiles', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $t->string('display_name')->nullable();
            $t->string('religion')->nullable();
            $t->string('custom_sport_name')->nullable();
            $t->boolean('setup_done')->default(false);
            $t->string('plan')->default('freemium');
            $t->string('referral_code')->nullable()->unique();
            $t->unsignedBigInteger('ref_invited')->default(0);
            $t->unsignedBigInteger('ref_converted')->default(0);
            $t->unsignedBigInteger('ref_earnings')->default(0);
            $t->unsignedBigInteger('ref_pending')->default(0);
            $t->timestamps();
        });

        // ── Feature toggles ──
        Schema::create('user_features', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('feature_key');
            $t->boolean('enabled')->default(true);
            $t->timestamps();
            $t->unique(['user_id', 'feature_key']);
        });

        // ── Spiritual: Sholat ──
        Schema::create('sholat_prayers', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->date('date');
            $t->string('name');                       // Subuh, Dzuhur, ...
            $t->boolean('done')->default(false);
            $t->boolean('takbir_pertama')->default(false);
            $t->boolean('rawatib')->default(false);
            $t->timestamps();
            $t->unique(['user_id', 'date', 'name']);
            $t->index(['user_id', 'date']);
        });
        Schema::create('sholat_sunnah', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->date('date');
            $t->string('name');                       // Tahajud, Dhuha, ...
            $t->timestamps();
            $t->unique(['user_id', 'date', 'name']);
            $t->index(['user_id', 'date']);
        });

        // ── Spiritual: non-Islam ──
        Schema::create('spiritual_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->date('date');
            $t->string('type');                       // morning_prayer, meditation, ...
            $t->boolean('done')->default(false);
            $t->timestamps();
            $t->unique(['user_id', 'date', 'type']);
            $t->index(['user_id', 'date']);
        });

        // ── Sports ──
        Schema::create('gym_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->date('date');
            $t->boolean('done')->default(false);
            $t->unsignedInteger('calories')->default(0);
            $t->timestamps();
            $t->unique(['user_id', 'date']);
            $t->index(['user_id', 'date']);
        });
        Schema::create('run_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->date('date');
            $t->boolean('done')->default(false);
            $t->decimal('distance', 6, 2)->default(0);   // km
            $t->unsignedInteger('duration')->default(0); // minutes
            $t->string('type')->nullable();              // easy, tempo, ...
            $t->unsignedInteger('calories')->default(0);
            $t->text('notes')->nullable();
            $t->timestamps();
            $t->unique(['user_id', 'date']);
            $t->index(['user_id', 'date']);
        });
        Schema::create('cycling_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->date('date');
            $t->boolean('done')->default(false);
            $t->decimal('km', 6, 2)->default(0);
            $t->unsignedInteger('duration')->default(0);
            $t->timestamps();
            $t->unique(['user_id', 'date']);
            $t->index(['user_id', 'date']);
        });
        Schema::create('swimming_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->date('date');
            $t->boolean('done')->default(false);
            $t->unsignedInteger('laps')->default(0);
            $t->unsignedInteger('duration')->default(0);
            $t->timestamps();
            $t->unique(['user_id', 'date']);
            $t->index(['user_id', 'date']);
        });
        Schema::create('racket_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->date('date');
            $t->boolean('done')->default(false);
            $t->unsignedInteger('sets')->default(0);
            $t->timestamps();
            $t->unique(['user_id', 'date']);
            $t->index(['user_id', 'date']);
        });
        Schema::create('custom_sport_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->date('date');
            $t->boolean('done')->default(false);
            $t->unsignedInteger('duration')->default(0);
            $t->timestamps();
            $t->unique(['user_id', 'date']);
            $t->index(['user_id', 'date']);
        });

        // ── Intimacy ──
        Schema::create('intimacy_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->date('date');
            $t->unsignedInteger('count')->default(0);
            $t->timestamps();
            $t->unique(['user_id', 'date']);
            $t->index(['user_id', 'date']);
        });

        // ── Mental: mood + reflections + notes ──
        Schema::create('mood_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->date('date');
            $t->unsignedTinyInteger('score')->default(0);   // 0-5
            $t->unsignedTinyInteger('energy')->default(0);  // 0-5
            $t->text('note')->nullable();
            $t->timestamps();
            $t->unique(['user_id', 'date']);
            $t->index(['user_id', 'date']);
        });
        Schema::create('reflections', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->date('date');
            $t->text('good')->nullable();
            $t->text('improve')->nullable();
            $t->timestamps();
            $t->unique(['user_id', 'date']);
            $t->index(['user_id', 'date']);
        });
        Schema::create('notes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->date('date');
            $t->text('content')->nullable();
            $t->timestamps();
            $t->unique(['user_id', 'date']);
        });

        // ── Tasks ──
        Schema::create('todos', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('scope');                  // daily | weekly
            $t->string('period_key');             // date (daily) or week key (weekly)
            $t->string('text');
            $t->string('priority')->default('medium');
            $t->boolean('done')->default(false);
            $t->timestamps();
            $t->index(['user_id', 'scope', 'period_key']);
        });

        // ── Goals & Reminders ──
        Schema::create('goals', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('month_key');              // Y-m
            $t->string('field');
            $t->integer('value')->default(0);
            $t->timestamps();
            $t->unique(['user_id', 'month_key', 'field']);
        });
        Schema::create('reminders', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('key');
            $t->string('time')->nullable();
            $t->timestamps();
            $t->unique(['user_id', 'key']);
        });

        // ── Career ──
        Schema::create('job_applications', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('company');
            $t->string('position');
            $t->string('location')->nullable();
            $t->string('salary')->nullable();
            $t->date('applied_date')->nullable();
            $t->string('status')->default('applied');
            $t->string('job_type')->nullable();
            $t->string('channel')->nullable();
            $t->string('job_url', 1000)->nullable();
            $t->text('notes')->nullable();
            $t->timestamps();
            $t->index(['user_id', 'status']);
        });
        Schema::create('interviews', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('application_id')->nullable()->constrained('job_applications')->nullOnDelete();
            $t->string('company')->nullable();
            $t->string('position')->nullable();
            $t->date('date')->nullable();
            $t->string('time')->nullable();
            $t->string('type')->default('video');
            $t->string('round')->nullable();
            $t->string('location')->nullable();
            $t->string('interviewer')->nullable();
            $t->text('notes')->nullable();
            $t->boolean('completed')->default(false);
            $t->timestamps();
            $t->index(['user_id', 'date']);
        });
        Schema::create('career_goals', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $t->string('target_role')->nullable();
            $t->string('target_company')->nullable();
            $t->string('target_salary')->nullable();
            $t->string('target_date')->nullable();
            $t->text('notes')->nullable();
            $t->timestamps();
        });

        // ── Persiapan Melamar ──
        Schema::create('prep_links', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->string('url', 1000);
            $t->string('type')->default('other');
            $t->text('notes')->nullable();
            $t->timestamps();
        });
        Schema::create('prep_files', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->string('original_name');
            $t->string('path');
            $t->unsignedBigInteger('size')->default(0);
            $t->string('mime')->nullable();
            $t->string('type')->default('other');
            $t->text('notes')->nullable();
            $t->timestamps();
        });
        Schema::create('prep_templates', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('title')->default('Template Baru');
            $t->string('category')->default('email');
            $t->longText('content')->nullable();
            $t->timestamps();
        });
        Schema::create('prep_qa', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->text('question');
            $t->text('answer')->nullable();
            $t->string('category')->default('general');
            $t->unsignedTinyInteger('confidence')->default(3);
            $t->text('star_situation')->nullable();
            $t->text('star_task')->nullable();
            $t->text('star_action')->nullable();
            $t->text('star_result')->nullable();
            $t->timestamps();
        });
        Schema::create('contacts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->string('company')->nullable();
            $t->string('role')->nullable();
            $t->string('channel')->default('linkedin');
            $t->text('notes')->nullable();
            $t->date('connected_at')->nullable();
            $t->timestamps();
        });

        // ── Finance ──
        Schema::create('finance_transactions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('type');                   // income | expense
            $t->date('date');
            $t->string('category');
            $t->unsignedBigInteger('amount')->default(0);
            $t->string('note')->nullable();
            $t->timestamps();
            $t->index(['user_id', 'date']);
        });
        Schema::create('finance_budgets', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('month_key');              // Y-m
            $t->string('category');
            $t->unsignedBigInteger('amount')->default(0);
            $t->timestamps();
            $t->unique(['user_id', 'month_key', 'category']);
        });
        Schema::create('finance_savings_goals', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->unsignedBigInteger('target')->default(0);
            $t->unsignedBigInteger('current')->default(0);
            $t->date('deadline')->nullable();
            $t->string('color')->default('emerald');
            $t->timestamps();
        });

        // ── Referral payouts ──
        Schema::create('referral_payouts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->unsignedBigInteger('amount')->default(0);
            $t->string('method');                 // bank | ewallet
            $t->string('account');
            $t->string('name');
            $t->string('status')->default('pending');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach ([
            'referral_payouts', 'finance_savings_goals', 'finance_budgets', 'finance_transactions',
            'contacts', 'prep_qa', 'prep_templates', 'prep_files', 'prep_links',
            'career_goals', 'interviews', 'job_applications',
            'reminders', 'goals', 'todos', 'notes', 'reflections', 'mood_logs',
            'intimacy_logs', 'custom_sport_logs', 'racket_logs', 'swimming_logs',
            'cycling_logs', 'run_logs', 'gym_logs', 'spiritual_logs',
            'sholat_sunnah', 'sholat_prayers', 'user_features', 'user_profiles',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
