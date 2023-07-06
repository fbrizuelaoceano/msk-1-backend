<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GitPullAndCleanLaravelLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'git:pull-clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecuta un git pull y limpia los logs de laravel (storage/logs/laravel.logs)';

    /**
     * Execute the console command.
     */
    /* public function handle()
    {
         // Ejecutar git pull
         $output = shell_exec('git pull');
         $this->info($output);

         // Limpiar el archivo laravel.log
         file_put_contents(storage_path('logs/laravel.log'), '');

         $this->info('laravel.log se vacio.');
    } */

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {

            $output->writeln(" - Executing " . __CLASS__);
            // Ejecutar git pull
            $output->writeln(" - Executing - git pull");
            $shellResponse = shell_exec('git pull');
            $output->writeln($shellResponse);

            $output->writeln(" - Executing - Clear laravel.log");
            // Limpiar el archivo laravel.log
            file_put_contents(storage_path('logs/laravel.log'), '');
            $output->writeln(" - Executing " . __CLASS__ . " end");

        } catch (\Exception $e) {
            $msg = "ERROR: " . $e->getMessage();
            $output->writeln($msg);
            \Log::error($msg);
        }

        return 0;
    }
}