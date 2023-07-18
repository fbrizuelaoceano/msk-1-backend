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
            // $result = [
            //     'data' => [
            //         [
            //             'Owner' => [
            //                 'name' => 'Integraciones Administrador',
            //                 'id' => '5344455000001853001',
            //                 'email' => 'integraciones@msklatam.com'
            //             ],
            //             'R_gimen_fiscal' => '',
            //             'Fecha_asignaci_n_retake' => '',
            //             'Email_del_usuario_que_toma_la_licencia' => '',
            //             '$field_states' => '',
            //             'Ad_Campaign' => '',
            //             'Mailing_State' => 'CHILE',
            //             'El_contacto_es_la_persona_que_va_a_tomar_el_curso' => '',
            //             'A_o_de_estudio' => '',
            //             'Motivo_del_estado' => [],
            //             '$state' => 'save',
            //             '$process_flow' => '',
            //             'Tel_fono_del_usuario_que_toma_la_licencia' => '',
            //             'id' => '5344455000006803001',
            //             'Estado_de_Posible_cliente' => '',
            //             'Fecha_contacto_futuro' => '',
            //             '$approval' => [
            //                 'delegate' => '',
            //                 'approve' => '',
            //                 'reject' => '',
            //                 'resubmit' => ''
            //             ],
            //             'First_Visited_URL' => '',
            //             'Created_Time' => '2023-07-12T13:30:15-03:00',
            //             'Horario_de_la_contactaci_n' => [],
            //             'Change_Log_Time__s' => '',
            //             'Caracter_stica_contacto' => 'Experiencia OM',
            //             'Last_Visited_Time' => '',
            //             'Created_By' => [
            //                 'name' => 'Integraciones Administrador',
            //                 'id' => '5344455000001853001',
            //                 'email' => 'integraciones@msklatam.com'
            //             ],
            //             'Nombre_de_la_base' => '',
            //             'Description' => '',
            //             'Usuario' => 'gabrielagarciamondaca@gmail.com',
            //             'Number_Of_Chats' => '',
            //             'Facebook_Page' => '',
            //             '$review_process' => [
            //                 'approve' => '',
            //                 'reject' => '',
            //                 'resubmit' => ''
            //             ],
            //             'Mailing_Street' => 'Latadía interior 4558, comuna Las Condes, RM',
            //             'Average_Time_Spent_Minutes' => '',
            //             'Salutation' => '',
            //             'Full_Name' => 'Gabriela Andrea García Mondaca',
            //             'Record_Image' => '',
            //             'Documento_CSF' => '',
            //             'Ad_ID' => '',
            //             'Account_Name' => '',
            //             'Generar_nueva_password' => '',
            //             'Cursos_consultados' => '',
            //             'Ad_Name' => '',
            //             'Temas_de_interes' => [],
            //             'correo_facturacion' => '',
            //             'ID_Personal' => '171204380',
            //             'Validador' => '',
            //             'Mobile' => '',
            //             'Territories' => '',
            //             'Carrera_de_estudio' => '',
            //             '$orchestration' => '',
            //             'RUT' => '17120438-0',
            //             'URL_DESCARGA' => '',
            //             'Tipo_de_certificacion' => '',
            //             'Locked__s' => '',
            //             'Lead_Source' => '',
            //             'Tag' => [],
            //             'Colegio_Sociedad_o_Federaci_n' => [],
            //             'Lead_Form_ID' => '',
            //             'Last_Enriched_Time__s' => '',
            //             'Ad_Account_ID' => '',
            //             'Email' => 'gabrielagarciamondaca@gmail.com',
            //             '$currency_symbol' => '$',
            //             'Visitor_Score' => '',
            //             'Ad_Campaign_ID' => '',
            //             'usuario_prueba' => '',
            //             'rea_donde_tabaja' => '',
            //             'RFC' => '',
            //             'Last_Activity_Time' => '2023-07-12T14:04:16-03:00',
            //             'Preferencia_de_contactaci_n' => [],
            //             'Unsubscribed_Mode' => '',
            //             'URL_ORIGEN' => '',
            //             'Ad_Set_ID' => '',
            //             '$locked_for_me' => '',
            //             'Otra_especialidad' => '',
            //             'CUIT_CUIL_o_DNI' => '',
            //             '$approved' => 1,
            //             'Profesi_n' => 'Personal de enfermería',
            //             'Lead_Form' => '',
            //             'Enrich_Status__s' => '',
            //             'Days_Visited' => '',
            //             'Campa_a_whatsapp' => '',
            //             'Plataforma' => '',
            //             'Estado_civil' => '',
            //             '$editable' => 1,
            //             'Momento_de_contactaci_n' => [],
            //             'Apellido_del_usuario' => '',
            //             'Facebook_Page_ID' => '',
            //             'Brand' => '',
            //             'Biblioteca_digital' => '',
            //             'Especialidad' => '',
            //             '$zia_owner_assignment' => 'owner_recommendation_unavailable',
            //             'Convertido_mediante' => '',
            //             'Pertenece_a_un_colegio' => '',
            //             'Raz_n_social' => 'Gabriela Andrea García Mondaca',
            //             'Mailing_Zip' => '7550000',
            //             'Lugar_de_trabajo' => '',
            //             'Nombre_del_usuario' => '',
            //             'Sexo' => 'Femenino',
            //             'First_Name' => 'Gabriela Andrea',
            //             'Modified_By' => [
            //                 'name' => 'Roberto Flores',
            //                 'id' => '5344455000000369001',
            //                 'email' => 'administrador@msklatam.com'
            //             ],
            //             '$review' => '',
            //             'Otra_profesi_n' => '',
            //             'Phone' => '56992347807',
            //             'googlemapstextsearch__Google_Address' => '',
            //             'Pais' => 'Chile',
            //             'Ad_Set' => '',
            //             'Password' => 'e7KutenbOg3P',
            //             'Modified_Time' => '2023-07-12T14:04:16-03:00',
            //             'Date_of_Birth' => '1989-06-03',
            //             'Mailing_City' => '',
            //             'Unsubscribed_Time' => '',
            //             'Requiere_factura' => '',
            //             'datos_completos_para_Facturar' => '',
            //             'Tiempo_en_gesti_n_comercial' => '',
            //             'Correo_verificado' => '',
            //             'Ad_Account' => '',
            //             'First_Visited_Time' => '',
            //             'Last_Name' => 'García Mondaca',
            //             '$in_merge' => '',
            //             'Referrer' => '',
            //             'Fecha_modificaci_n_de_estado' => '',
            //             '$approval_state' => 'approved'
            //         ]
            //     ],
            //     'info' => [
            //         'per_page' => 200,
            //         'count' => 200,
            //         'page' => 1,
            //         'sort_by' => 'id',
            //         'sort_order' => 'desc',
            //         'more_records' => 1
            //     ]
            // ];

            // Log::info("PopulateUsersWithContacts-execute-result: " . print_r($result, true));

            if (!(sizeof($result) > 0)) {
                $output->writeln(" - no entries result from CRM, aborting...");
                return 0;
            }

            if (isset($result['data'])) {
                $contacts = $result['data'];
                
                foreach ($contacts as $cntc) {
                    $newUser = User::UpdateOrCreate(
                        [
                            'email' => $cntc['Usuario']
                        ],
                        [
                        'name' => $cntc['Usuario'],
                        'email' => $cntc['Usuario'],
                        'password' => Hash::make($cntc['Password']),
                    ]);
                    $newContact = Contact::UpdateOrCreate(
                        [
                            'email' => $cntc['Usuario']
                        ],
                        [
                        'name' => $cntc['First_Name'],
                        'last_name' => $cntc['Last_Name'],
                        'email' => $cntc['Usuario'],
                        'entity_id_crm' => $cntc['id'],
                        'phone' => $cntc['Phone'],
                        'user_id' => $newUser->id,
                        'profession' => $cntc["Profesi_n"],
                        'speciality' => $cntc["Especialidad"],
                        'rfc' => $cntc["RFC"],
                        'country' => $cntc["Pais"],
                        'fiscal_regime' => $cntc["R_gimen_fiscal"],
                        'postal_code' => $cntc["Mailing_Zip"],
                        'address' => $cntc["Mailing_Street"],
                        'other_profession' => $cntc["Otra_profesi_n"],
                        'other_speciality' => $cntc["Otra_especialidad"],
                        'state' => $cntc["Mailing_City"],
                        'sex' => $cntc["Sexo"],
                        'validate' => $cntc["Validador"],
                        'date_of_birth' => $cntc["Date_of_Birth"]
                    ]);
                }
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
            $err = [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ];

            Log::error("Error en PopulateUsersWithContacts: " . $e->getMessage() . "\n" . json_encode($err, JSON_PRETTY_PRINT));
        }

        return 0;
    }
}