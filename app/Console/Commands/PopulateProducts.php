<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductCRM;
use App\Services\ZohoCRMService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateProducts extends Command
{
    protected $name = 'populate:products';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:products';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trae todos los productos de crm a msk db';
    private ZohoCRMService $zohoService;
    public function __construct(ZohoCRMService $service)
    {
        $this->setName($this->name);
        parent::__construct();
        $this->zohoService = $service;
    }
    /**
     * Execute the console command.
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try{
            $output->writeln("Ejecutando comando...");

            $result = $this->zohoService->Get('Products') ;
            // $result = [
            //     'data' => [
            //         [
            //             'Qty_in_Demand' => 0,
            //             'Temario' => '',
            //             'Owner' => [
            //                 'name' => 'Roberto Flores',
            //                 'id' => '5344455000000369001',
            //                 'email' => 'administrador@msklatam.com',
            //             ],
            //             '$currency_symbol' => '$',
            //             'Horas_lectivas' => '',
            //             '$field_states' => '',
            //             'Otros_paises' => '',
            //             'Tax' => [],
            //             'Product_Active' => 1,
            //             '$state' => 'save',
            //             'C_digo_de_Curso_Cedente' => '',
            //             '$process_flow' => '',
            //             'Inversi_n_marketing' => 10,
            //             'A_os_para_finalizar_licencia_cursada' => 1,
            //             'M_xico' => '',
            //             'id' => '5344455000002859257',
            //             'Chile' => '',
            //             'Curso_importado_desde_OM' => 1,
            //             '$approved' => 1,
            //             'Nivel' => 'No superior',
            //             'Ecuador' => '',
            //             'Colombia' => '',
            //             '$approval' => [
            //                 'delegate' => '',
            //                 'approve' => '',
            //                 'reject' => '',
            //                 'resubmit' => '',
            //             ],
            //             'Created_Time' => '2023-04-24T10:15:00-03:00',
            //             'Requisitos' => [
            //                 [
            //                     'Ser profesional médico',
            //                 ],
            //             ],
            //             'Product_Name' => 'Hipertensión resistente e hipertensión severa en servicios de emergencia',
            //             '$taxable' => 1,
            //             '$editable' => 1,
            //             'Plataforma_enrolamiento' => '',
            //             'Dias_para_enrolarse' => 30,
            //             'Created_By' => [
            //                 'name' => 'Roberto Flores',
            //                 'id' => '5344455000000369001',
            //                 'email' => 'administrador@msklatam.com',
            //             ],
            //             'Taxable' => 1,
            //             'Inversi_n_inicial' => 0,
            //             'Product_Category' => 'Ebook',
            //             'Description' => '',
            //             'Vendor_Name' => [
            //                 'name' => 'OCEANO ARGENTINA',
            //                 'id' => '5344455000002876388',
            //             ],
            //             '$review_process' => [
            //                 'approve' => '',
            //                 'reject' => '',
            //                 'resubmit' => '',
            //             ],
            //             'Inversi_n_extraordinaria' => 0,
            //             'Avance_completado' => '',
            //             'Record_Image' => '',
            //             'Modified_By' => [
            //                 'name' => 'Roberto Flores',
            //                 'id' => '5344455000000369001',
            //                 'email' => 'administrador@msklatam.com',
            //             ],
            //             '$review' => '',
            //             'Product_Code' => 1000011,
            //             'ISBN' => '',
            //             'Modalidad' => [
            //                 'Cursada 100% online',
            //             ],
            //             'Inversi_n_gestor_institucional' => 0,
            //             'Modified_Time' => '2023-04-24T11:37:02-03:00',
            //             'Pais_en_el_que_esta_disponible' => [],
            //             'Commission_Rate' => '',
            //             'valor' => '',
            //             '$orchestration' => '',
            //             '$in_merge' => '',
            //             'Fecha_de_lanzamiento' => '2021-05-01',
            //             'Tag' => [],
            //             '$approval_state' => 'approved',
            //             'Unit_Price' => 0,
            //         ],
            //     ],
            //     'info' => [
            //         'per_page' => 200,
            //         'count' => 200,
            //         'page' => 1,
            //         'sort_by' => 'id',
            //         'sort_order' => 'desc',
            //         'more_records' => 1,
            //     ],
            // ];
            if (!(sizeof($result) > 0)) {
                $output->writeln(" - no entries result from CRM, aborting...");
                return 0;
            }

            Log::info("PopulateProducts-execute-result: " . print_r($result, true));

            if (isset($result['data'])) {

                $products = $result['data'];
                // $requeridos = [
                   
                // ];
                foreach ($products as $prdct) {
                    $isNull = false;
                    // foreach ($requeridos as $campo) {
                    //     if (!isset($cntc[$campo]) || $cntc[$campo] === null) {//si el campo es null lo imprimo para que no rompa
                    //         // El campo es null o no está definido en $cntc
                    //         $output->writeln("Uno de los campos requeridos por la base de datos viene vacio desde la api zoho. Id de la entidad en crm: ".$cntc["id"]);
                    //         Log::info("PopulateUsersWithContacts-execute-contacto de zoho sin los datos requeridos: " . print_r($cntc, true));
                    //         $isNull=true;
                    //         break;
                    //     }
                    // }

                    if(!$isNull){
                        $newProduct = ProductCRM::UpdateOrCreate(
                            [
                                'entity_id' => $prdct['id']
                            ],
                            [
                                'product_code' => $prdct['Product_Code'],
                                'cedente_code' => $prdct['C_digo_de_Curso_Cedente'],
                                'platform' => $prdct['Plataforma_enrolamiento'], 
                                // 'platform_url' => $prdct['URL_plataforma'], //no esta en el producto
                                'entity_id' => $prdct['id'],
                            ]
                        );
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

            Log::error("Error en PopulateProducts: " . $e->getMessage() . "\n" . json_encode($err, JSON_PRETTY_PRINT));
        }

        return 0;
    }
}


// [
//     [data] => 
//         [
//             [0] => 
//             [
//                 [Qty_in_Demand] => 0
//                 [Temario] => 
//                 [Owner] => 
//                     [
//                         [name] => Roberto Flores
//                         [id] => 5344455000000369001
//                         [email] => administrador@msklatam.com
//                     ]

//                 [$currency_symbol] => $
//                 [Horas_lectivas] => 
//                 [$field_states] => 
//                 [Otros_paises] => 
//                 [Tax] => 
//                     [
//                     ]

//                 [Product_Active] => 1
//                 [$state] => save
//                 [C_digo_de_Curso_Cedente] => 
//                 [$process_flow] => 
//                 [Inversi_n_marketing] => 10
//                 [A_os_para_finalizar_licencia_cursada] => 1
//                 [M_xico] => 
//                 [id] => 5344455000002859257
//                 [Chile] => 
//                 [Curso_importado_desde_OM] => 1
//                 [$approved] => 1
//                 [Nivel] => No superior
//                 [Ecuador] => 
//                 [Colombia] => 
//                 [$approval] => 
//                     [
//                         [delegate] => 
//                         [approve] => 
//                         [reject] => 
//                         [resubmit] => 
//                     ]

//                 [Created_Time] => 2023-04-24T10:15:00-03:00
//                 [Requisitos] => 
//                     [
//                         [0] => Ser profesional médico
//                     ]

//                 [Product_Name] => Hipertensión resistente e hipertensión severa en servicios de emergencia
//                 [$taxable] => 1
//                 [$editable] => 1
//                 [Plataforma_enrolamiento] => 
//                 [Dias_para_enrolarse] => 30
//                 [Created_By] => 
//                     [
//                         [name] => Roberto Flores
//                         [id] => 5344455000000369001
//                         [email] => administrador@msklatam.com
//                     ]

//                 [Taxable] => 1
//                 [Inversi_n_inicial] => 0
//                 [Product_Category] => Ebook
//                 [Description] => 
//                 [Vendor_Name] => 
//                     [
//                         [name] => OCEANO ARGENTINA
//                         [id] => 5344455000002876388
//                     ]

//                 [$review_process] => 
//                     [
//                         [approve] => 
//                         [reject] => 
//                         [resubmit] => 
//                     ]

//                 [Inversi_n_extraordinaria] => 0
//                 [Avance_completado] => 
//                 [Record_Image] => 
//                 [Modified_By] => 
//                     [
//                         [name] => Roberto Flores
//                         [id] => 5344455000000369001
//                         [email] => administrador@msklatam.com
//                     ]

//                 [$review] => 
//                 [Product_Code] => 1000011
//                 [ISBN] => 
//                 [Modalidad] => 
//                     [
//                         [0] => Cursada 100% online
//                     ]

//                 [Inversi_n_gestor_institucional] => 0
//                 [Modified_Time] => 2023-04-24T11:37:02-03:00
//                 [Pais_en_el_que_esta_disponible] => 
//                     [
//                     ]

//                 [Commission_Rate] => 
//                 [valor] => 
//                 [$orchestration] => 
//                 [$in_merge] => 
//                 [Fecha_de_lanzamiento] => 2021-05-01
//                 [Tag] => 
//                     [
//                     ]

//                 [$approval_state] => approved
//                 [Unit_Price] => 0
//             ]

//             ]

//             [info] => 
//             [
//             [per_page] => 200
//             [count] => 200
//             [page] => 1
//             [sort_by] => id
//             [sort_order] => desc
//             [more_records] => 1
//             ]

// ]
