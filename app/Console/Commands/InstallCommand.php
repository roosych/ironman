<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install 
                            {--fresh : Drop all tables and re-run all migrations}
                            {--seed : Seed the database with data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the application (run migrations and seeders)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Начало установки приложения...');
        $this->newLine();

        // Проверка подключения к БД
        if (!$this->checkDatabaseConnection()) {
            $this->error('❌ Не удалось подключиться к базе данных!');
            return Command::FAILURE;
        }

        $this->info('✅ Подключение к базе данных установлено');
        $this->newLine();

        // Запуск миграций
        $fresh = $this->option('fresh');
        if ($fresh) {
            $this->info('🔄 Запуск миграций с очисткой базы данных...');
            $this->call('migrate:fresh', [
                '--force' => true,
            ]);
        } else {
            $this->info('🔄 Запуск миграций...');
            $this->call('migrate', [
                '--force' => true,
            ]);
        }

        $this->newLine();

        // Запуск seeder'ов
        $shouldSeed = $this->option('seed') || $fresh;
        
        if (!$shouldSeed && !$this->option('no-interaction')) {
            $shouldSeed = $this->confirm('Запустить seeder\'ы для создания начальных данных?', true);
        }

        if ($shouldSeed) {
            $this->runSeeders();
        }

        $this->newLine();

        // Создание символической ссылки для storage
        $this->info('🔗 Создание символической ссылки для storage...');
        try {
            $this->call('storage:link');
            $this->info('✅ Символическая ссылка создана');
        } catch (\Exception $e) {
            $this->warn('⚠️  Не удалось создать символическую ссылку: ' . $e->getMessage());
        }

        $this->newLine();
        $this->info('✅ Установка приложения завершена успешно!');
        $this->newLine();

        $this->displaySummary();

        return Command::SUCCESS;
    }

    /**
     * Check database connection.
     */
    private function checkDatabaseConnection(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Run all necessary seeders.
     * Порядок важен: AthleteProfileSeeder должен быть перед ReviewerSeeder,
     * так как ReviewerSeeder может ссылаться на профили.
     */
    private function runSeeders(): void
    {
        $this->info('🌱 Запуск seeder\'ов...');
        $this->newLine();

        $seeders = [
            'AdminSeeder' => 'Создание администратора',
            'AthleteProfileSeeder' => 'Создание профилей атлетов',
            'RaceResultsSeeder' => 'Создание результатов гонок',
            'ReviewerSeeder' => 'Создание reviewer пользователя',
        ];

        foreach ($seeders as $seeder => $description) {
            $this->info("  → {$description}...");
            $this->call('db:seed', [
                '--class' => $seeder,
                '--force' => true,
            ]);
        }

        $this->newLine();
        $this->info('✅ Все seeder\'ы выполнены');
    }

    /**
     * Display installation summary.
     */
    private function displaySummary(): void
    {
        $this->info('📋 Сводка установки:');
        $this->table(
            ['Компонент', 'Статус'],
            [
                ['База данных', '✅ Настроена'],
                ['Миграции', '✅ Выполнены'],
                ['Администратор', '✅ Создан'],
                ['Профили атлетов', '✅ Созданы'],
                ['Результаты гонок', '✅ Созданы'],
                ['Reviewer пользователь', '✅ Создан'],
                ['Storage ссылка', '✅ Создана'],
            ]
        );

        $this->newLine();
        $this->comment('💡 Полезные команды:');
        $this->line('  • php artisan serve - запустить сервер разработки');
        $this->line('  • php artisan queue:work - запустить обработку очередей');
        $this->line('  • php artisan app:install --fresh - переустановить приложение');
    }
}
