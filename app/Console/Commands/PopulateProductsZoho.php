<?php

namespace App\Console\Commands;

use App\Services\ZohoCRMService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateProductsZoho extends Command
{
    protected $name = 'populate:products-zoho {limit=10&page=1}';
    protected $signature = 'populate:products-zoho {limit=10&page=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Suma productos del CRM en la base de datos';

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

            // $result = $this->service->Get('Contacts');
            $result = $this->service->GetForCommand('Contacts', $limit, $page);
        } catch (\Exception $e) {
            $output->writeln("Hubo un error al ejecutar el codigo del comando y no pudo terminar. Para mas detalles revice el archivo de log.");
        }

        return 0;
    }
}
