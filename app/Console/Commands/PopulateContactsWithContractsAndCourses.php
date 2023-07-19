<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Contract;
use App\Models\Contact;
use App\Services\ZohoCRMService;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateContactsWithContractsAndCourses extends Command
{
    protected $name = 'populate:contacts-contracts';
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
    private ZohoCRMService $zohoService;
    public function __construct(ZohoCRMService $service)
    {
        $this->setName($this->name);
        parent::__construct();
        $this->zohoService = $service;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        try{
            $output->writeln("Ejecutando comando...");

            $salesOrders = $this->zohoService->Get('Sales_Orders') ;
            if (!(sizeof($salesOrders) > 0)) {
                $output->writeln(" - no entries result from CRM, aborting...");
                return 0;
            }

            // Log::info("PopulateContactsWithContractsAndCourses-execute-salesOrders: " . print_r($salesOrders, true));

            if (isset($salesOrders['data'])) {
                $salesOrdersArray = (array)$salesOrders["data"];
                // Log::info("PopulateContactsWithContractsAndCourses-execute-salesOrdersArray: " . print_r($salesOrdersArray, true));
                foreach ($salesOrdersArray as $saleOrder) {
                    $contact = Contact::where(["entity_id_crm" => $saleOrder["Contact_Name"]["id"]])->first();
                    if (isset($contact)){
                        // Log::info("PopulateContactsWithContractsAndCourses-execute-contactId: " . print_r($contact->id, true));
                        // Log::info("PopulateContactsWithContractsAndCourses-execute-saleOrder: " . print_r($saleOrder, true));
                        $contract = Contract::updateOrCreate(['entity_id_crm' => $saleOrder["id"]], [
                            'contact_id' => $contact->id,
                            'entity_id_crm' => $saleOrder["id"],
                            'so_crm' => $saleOrder["SO_Number"],
                            'status' => $saleOrder["Status"],
                            'status_payment' => $saleOrder["Estado_de_cobro"],
                            'country' => $saleOrder["Pais_de_facturaci_n"],
                            'currency' => $saleOrder["Currency"],
                        ]);
                    }
                }
            }

            $contacts = Contact::all();

        
        } catch (\Exception $e) {
            $err = [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ];

            Log::error("Error en PopulateContactsWithContractsAndCourses: " . $e->getMessage() . "\n" . json_encode($err, JSON_PRETTY_PRINT));
        }

        return 0;
    }
}
