<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        Schema::create('cluster_results', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id')->nullable();
            $table->integer('cluster')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('address')->nullable();
            $table->enum('customer_type', ['baru', 'lama'])->default('baru');
            $table->timestamps();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index('jobs_queue_index');
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->date('tanggal_transaksi');
            $table->double('length');
            $table->double('height');
            $table->double('luas');
            $table->integer('initial_price_per_m');
            $table->integer('final_price_per_m')->nullable();
            $table->integer('total_price')->nullable();
            $table->enum('statusjob', ['pending','survey','price_fixation','material_buy','in_production','progress_reported','installation','completed'])->default('pending');
            $table->timestamps();
            $table->integer('product_id')->default(0);
            // Sesuai dump: index saja, tidak ada FK pada tabel orders.
            $table->index('customer_id', 'orders_customer_id_foreign');
        });

        Schema::create('orders_reals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->date('tanggal_transaksi');
            $table->double('length');
            $table->double('height');
            $table->double('luas');
            $table->integer('initial_price_per_m');
            $table->integer('final_price_per_m')->nullable();
            $table->integer('total_price')->nullable();
            $table->enum('statusjob', ['pending','survey','price_fixation','material_buy','in_production','progress_reported','installation','completed'])->default('pending');
            $table->timestamps();
            $table->integer('product_id')->default(0);
            $table->foreign('customer_id', 'orders_reals_customer_id_foreign')
                ->references('id')->on('customers')->cascadeOnDelete();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->text('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('pesans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->nullable();
            $table->string('pesan')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });

        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->integer('cluster');
            $table->enum('type', ['discount','cross']);
            $table->unsignedBigInteger('product_id');
            $table->decimal('discount', 5, 2)->nullable();
            $table->unsignedBigInteger('cross_product_id')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->boolean('is_active')->default(true);
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->unsignedBigInteger('user_id')->nullable()->index('sessions_user_id_index');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index('sessions_last_activity_index');
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role_type', ['admin','user','pembeli'])->default('pembeli');
            $table->rememberToken();
            $table->timestamps();
            $table->integer('id_pelanggan')->nullable();
        });

        // Hapus view lama bila ada, lalu buat ulang berdasarkan definisi dari dump.
        DB::statement('DROP VIEW IF EXISTS customer_transaction_with_cluster');
        DB::statement('DROP VIEW IF EXISTS customer_transaction_summary');

        DB::statement(<<<'SQL'
            CREATE VIEW customer_transaction_summary AS
            SELECT c.id AS customer_id, c.name AS customer_name,
                   COUNT(o.id) AS total_transaksi, SUM(o.luas) AS total_luas,
                   SUM(o.total_price) AS total_spending,
                   AVG(o.final_price_per_m) AS avg_price
            FROM customers c
            INNER JOIN orders o ON o.customer_id = c.id
            GROUP BY c.id, c.name
        SQL);

        DB::statement(<<<'SQL'
            CREATE VIEW customer_transaction_with_cluster AS
            SELECT v.customer_id, v.customer_name, v.total_transaksi,
                   v.total_luas, v.total_spending, v.avg_price,
                   cr.cluster AS cluster
            FROM (
                SELECT c.id AS customer_id, c.name AS customer_name,
                       COUNT(o.id) AS total_transaksi, SUM(o.luas) AS total_luas,
                       SUM(o.total_price) AS total_spending,
                       AVG(o.final_price_per_m) AS avg_price
                FROM customers c
                INNER JOIN orders o ON o.customer_id = c.id
                GROUP BY c.id, c.name
            ) v
            LEFT JOIN cluster_results cr ON cr.customer_id = v.customer_id
        SQL);
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS customer_transaction_with_cluster');
        DB::statement('DROP VIEW IF EXISTS customer_transaction_summary');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('products');
        Schema::dropIfExists('pesans');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('orders_reals');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('cluster_results');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('users');
    }
};
