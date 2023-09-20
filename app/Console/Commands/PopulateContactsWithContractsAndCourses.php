<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Contract;
use App\Models\Contact;
use App\Models\Product;
use App\Models\CourseProgress;
use App\Services\ZohoCRMService;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateContactsWithContractsAndCourses extends Command
{
    // protected $name = 'populate:contracts-products-courses-progress';
    protected $name = 'populate:contacts-contracts {limit=10&page=1}';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:contacts-contracts {limit=10&page=1}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Toma los contactos traidos del comando que populariza msk db y busca con querys all de contratos y sus productos asociados. Tambien trae las cursadas.';
    private ZohoCRMService $zohoService;
    public function __construct(ZohoCRMService $service)
    {
        $this->setName($this->name);
        parent::__construct();
        $this->zohoService = $service;
    }

    protected function configure()
    {
        $this->addArgument('limit');
        $this->addArgument('page');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {

            $limit = $input->getArgument('limit') ?? 1;
            $page = $input->getArgument('page') ?? 1;
            $contactsIds = [];
            $output->writeln(" - Executing " . __CLASS__ . " " . $page . " " . $limit);


            $salesOrders = $this->zohoService->GetForCommand('Sales_Orders', $limit, $page);
            // Log::debug("GetForCommand: " . print_r($salesOrders, true));
            $output->writeln("Se encontraton " . sizeof($salesOrders) . " contratos");

            if (!(sizeof($salesOrders) > 0)) {
                $output->writeln(" - no entries result from CRM, aborting...");
                return 0;
            }

            if (isset($salesOrders['data'])) {
                $salesOrdersArray = (array) $salesOrders["data"];

                foreach ($salesOrdersArray as $index => $saleOrder) {
                    $output->writeln("Contrato " . $index + 1 . "/" . sizeof($salesOrders));
                    $output->writeln("-----------------------");
                    $output->writeln("Recuperando contacto con id " . $saleOrder["Contact_Name"]["id"]);

                    $contact = Contact::where(["entity_id_crm" => $saleOrder["Contact_Name"]["id"]])->first();

                    if (isset($contact)) {
                        $output->writeln("El contacto esta en la base de datos");
                        $output->writeln("Actualizando contacto y tomando productos ...");

                        $contract = Contract::updateOrCreate(['entity_id_crm' => $saleOrder["id"]], [
                            'contact_id' => $contact->id,
                            'entity_id_crm' => $saleOrder["id"],
                            'so_crm' => $saleOrder["SO_Number"],
                            'status' => $saleOrder["Status"],
                            'status_payment' => $saleOrder["Estado_de_cobro"],
                            'country' => $saleOrder["Pais_de_facturaci_n"],
                            'currency' => $saleOrder["Currency"],
                        ]);

                        $productDetails = $saleOrder["Product_Details"];

                        foreach ($productDetails as $pd) {
                            Product::updateOrCreate([
                                'entity_id_crm' => $pd["product"]["id"],
                                'contract_entity_id' => $saleOrder["id"]
                            ], [
                                'entity_id_crm' => $pd["product"]["id"],
                                'contract_id' => $contract->id,
                                'contract_entity_id' => $saleOrder["id"],
                                'quantity' => $pd["quantity"],
                                'discount' => $pd["Discount"],
                                'price' => $pd["total"],
                                'product_code' => (int) $pd["product"]["Product_Code"]
                            ]);
                        }
                        $output->writeln("Se completo el procesamiento");
                        $contactsIds[] = $saleOrder["Contact_Name"]["id"];

                    } else {
                        $output->writeln("El contacto " . $saleOrder["Contact_Name"]["id"] . " no esta en la base de datos");
                    }


                }
            }

            $output->writeln("Buscando contactos " . count($contactsIds) . " ...");
            $contacts = Contact::whereIn('entity_id_crm', $contactsIds)->get();
            $output->writeln("Se encontraron " . sizeof($contacts));

            foreach ($contacts as $index => $contact) {
                if (isset($contact)) {
                    $output->writeln("Contacto " . $index + 1 . "/" . sizeof($contacts));
                    $output->writeln("-----------------------");
                    $output->writeln("Recuperando contacto con id " . $contact->entity_id_crm . " desde ZohoCRM");

                    $contactZoho = $this->zohoService->GetByIdAllDetails('Contacts', $contact->entity_id_crm);

                    if (isset($contactZoho['data'][0])) {
                        $output->writeln("Se encontro el contacto " . $contact->entity_id_crm . " en ZohoCRM");
                        $output->writeln("Tomando y actualizando cursadas ....");
                        Log::debug("Actualizando usuario: " . $contactZoho['data'][0]['Usuario'] . " - ID: " . $contactZoho['data'][0]['id']);

                        $coursesProgressZoho = $contactZoho['data'][0]["Formulario_de_cursada"];
                        foreach ($coursesProgressZoho as $index => $cpZoho) {
                            $dataProductZoho = $this->zohoService->GetByIdAllDetails('Products', $cpZoho["Nombre_de_curso"]["id"]);
                            $productZoho = $dataProductZoho["data"][0];
                            CourseProgress::updateOrCreate([
                                'entity_id_crm' => $cpZoho['id'],
                                'contact_id' => $contact->id
                            ], [
                                'entity_id_crm' => $cpZoho['id'],
                                'Fecha_finalizaci_n' => $cpZoho['Fecha_finalizaci_n'],
                                // 'Nombre_de_curso' => $formCP['Nombre_de_curso']['name'].' id:'.$formCP['Nombre_de_curso']['id'],
                                'Nombre_de_curso' => $cpZoho['Nombre_de_curso']["name"],
                                'Estado_de_OV' => $cpZoho['Estado_de_OV'],
                                'field_states' => $cpZoho['$field_states'],
                                'Created_Time' => $cpZoho['Created_Time'],
                                // 'Parent_Id' => $formCP['Parent_Id']['name'].' id:'.$formCP['Parent_Id']['id'],
                                'Parent_Id' => $cpZoho['Parent_Id']["id"],
                                'Nota' => $cpZoho['Nota'],
                                'Estado_cursada' => $cpZoho['Estado_cursada'],
                                'Avance' => $cpZoho['Avance'],
                                'Fecha_de_expiraci_n' => $cpZoho['Fecha_de_expiraci_n'],
                                'in_merge' => $cpZoho['$in_merge'],
                                'Fecha_de_compra' => $cpZoho['Fecha_de_compra'],
                                'Enrollamiento' => $cpZoho['Enrollamiento'],
                                'Fecha_de_ltima_sesi_n' => $cpZoho['Fecha_de_ltima_sesi_n'],
                                'contact_id' => $contact->id,
                                'Product_Code' => $productZoho['Product_Code'],
                                'C_digo_de_Curso_Cedente' => $productZoho['C_digo_de_Curso_Cedente'],
                                'Plataforma_enrolamiento' => $productZoho['Plataforma_enrolamiento'],
                            ]);
                        }
                    } else {
                        $output->writeln("No se encontro el contacto " . $contact->entity_id_crm . " en ZohoCRM");
                        Log::warning("Contacto no esta en ZohoCRM: [ID]" . $contact->entity_id_crm);

                    }
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

            Log::error("Error en PopulateContactsWithContractsAndCourses: " . $e->getMessage() . "\n" . json_encode($err, JSON_PRETTY_PRINT));
        }

        return 0;
    }
}