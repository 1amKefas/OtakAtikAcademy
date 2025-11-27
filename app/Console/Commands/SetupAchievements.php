<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupAchievements extends Command
{
    protected $signature = 'setup:achievements';
    protected $description = 'Run migrations and seed achievements table';

    public function handle()
    {
        $this->info('Running achievements migrations...');
        $this->call('migrate', [
            '--force' => true,
        ]);

        $this->info('Seeding achievements...');
        $this->call('db:seed', [
            '--class' => 'AchievementSeeder',
            '--force' => true,
        ]);

        $this->info('Achievements setup complete!');
    }
}
