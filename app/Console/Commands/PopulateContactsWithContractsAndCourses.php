<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateContactsWithContractsAndCourses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:contacts-contracts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Toma contratos del crm y mapea con los contactos que tiene la base de msk latam.';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $message = "Se ejecuto el comando";

        

        Contacts::all();
        
        Log::info("PopulateContactsWithContractsAndCourses-execute: " . print_r($message, true));
    }
}
