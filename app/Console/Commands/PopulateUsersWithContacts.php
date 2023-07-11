<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\ZohoCRMService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateUsersWithContacts extends Command
{

    protected $name = 'populate:users-contacts {limit=10&page=1}';
    protected $signature = 'populate:users-contacts {limit=10&page=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Toma contactos de ZohoCRM y crea Usuarios';

    private ZohoCRMService $service;

    public function __construct(ZohoCRMService $service)
    {
        $this->setName($this->name);
        parent::__construct();
        $this->service = $service;
    }
    protected function configure()
    {
        $this->addArgument('limit');
        $this->addArgument('page');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $limit = $input->getArgument('limit');
            $page = $input->getArgument('page');
            $output->writeln(" - Executing " . __CLASS__ . " " . $page . " " . $limit);

            $result = $this->service->Get('Contacts');



            if (!(sizeof($result) > 0)) {
                $output->writeln(" - no entries result from CRM, aborting...");
                return 0;
            }

            dd(collect($result));


            collect($result)->each(function ($contact) {
                User::updateOrCreate(['email' => $contact['Usuario']], [
                    'name' => $contact['Full_Name'],
                    'email' => $contact['Usuario'],
                    'password' => Hash::make($contact['Password']),
                    'updated_at' => now()
                ]);

            });

        } catch (\Exception $e) {
            $msg = "ERROR: " . $e->getMessage();
            $output->writeln($msg);
            \Log::error($msg);
        }

        return 0;
    }
}