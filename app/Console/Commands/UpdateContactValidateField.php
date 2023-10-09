<?php

namespace App\Console\Commands;

use App\Models\Contact;
use App\Models\User;
use App\Services\ZohoCRMService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateContactValidateField extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'populate:update-contact {limit=10&page=1}';
    protected $signature = 'populate:update-contact {limit=10&page=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Toma contactos de ZohoCRM y actualiza el campo validate con para cada id de zoho de cada contacto';

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

            $result = $this->service->GetForCommand('Contacts', $limit, $page);


            if (!(sizeof($result) > 0)) {
                $output->writeln(" - no entries result from CRM, aborting...");
                return 0;
            }

            if (isset($result['data'])) {
                $contacts = $result['data'];
                $requeridos = [
                    'id',
                    'First_Name',
                    'Last_Name',
                    'Phone',
                    'Usuario',
                    'Sexo',
                    'Pais'
                ];
                foreach ($contacts as $index => $cntc) {
                    $output->writeln("-----------------------");
                    $output->writeln("Contacto " . $index + 1 . "/" . sizeof($contacts));
                    $output->writeln("ID de contacto en zoho: " . $cntc['id']);

                    $output->writeln("Actualizando validate de contacto " . " ...");
                    Contact::where('entity_id_crm', $cntc['id'])->update(['validate' => $cntc['Validador']]);
                }
            }

        } catch (\Exception $e) {
            $err = [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                // 'trace' => $e->getTraceAsString(),
            ];
            $output->writeln("Hubo un error al ejecutar el codigo del comando y no pudo terminar. Para mas detalles revice el archivo de log.");
            Log::error("Error en PopulateContactValidateField: " . $e->getMessage() . "\n" . json_encode($err, JSON_PRETTY_PRINT));
        }
        $output->writeln("Carga de campo validate del contacto completa.");
        return 0;
    }
}
