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

                        $productDetails = $saleOrder["Product_Details"];
                        // Log::info("salesForCRM-productDetails: " . print_r($productDetails, true));
            
                        foreach ($productDetails as $pd) {
                            // Log::info("salesForCRM-pd: " . print_r($pd, true));
            
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
                    }
                }
            }
            
            $contacts = Contact::all();
            foreach ($contacts as $index => $contact) {
                if (isset($contact)){
                    $contactZoho = $this->zohoService->GetByIdAllDetails('Contacts',$contact->entity_id_crm);
                    // Log::info("PopulateContactsWithContractsAndCourses-execute-contact: " . print_r($contact, true));
                    // Log::info("PopulateContactsWithContractsAndCourses-execute-contactsZoho: " . print_r($contactZoho, true));
                    if(isset($contactZoho['data'][0])){
                        $coursesProgressZoho = $contactZoho['data'][0]["Formulario_de_cursada"];
                        foreach($coursesProgressZoho as $index => $cpZoho){
                            $dataProductZoho = $this->zohoService->GetByIdAllDetails('Products',$cpZoho["Nombre_de_curso"]["id"]);
                            // Log::info("PopulateContactsWithContractsAndCourses-execute-productsZoho: " . print_r($productsZoho, true));
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
                    }
                }
            }

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

