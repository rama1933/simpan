<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SetupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:setup {--fresh : Drop all tables and re-run all migrations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup database dengan menjalankan migration dan seeder sekaligus';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Memulai setup database...');
        
        // Cek apakah menggunakan option --fresh
        if ($this->option('fresh')) {
            $this->info('🔄 Menjalankan migration:fresh...');
            Artisan::call('migrate:fresh', [], $this->getOutput());
        } else {
            $this->info('🔄 Menjalankan migration...');
            Artisan::call('migrate', [], $this->getOutput());
        }
        
        $this->info('🌱 Menjalankan seeder...');
        Artisan::call('db:seed', [], $this->getOutput());
        
        $this->info('✅ Setup database berhasil!');
        $this->info('📊 Database siap digunakan dengan data lengkap.');
        
        return Command::SUCCESS;
    }
}