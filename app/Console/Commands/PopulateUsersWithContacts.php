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

            // $result = $this->service->Get('Contacts');
            $result = $this->service->GetForCommand('Contacts', $limit, $page);

            // Log::info("PopulateUsersWithContacts-execute-result: " . print_r($result, true));

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

                    $isNull = false;
                    foreach ($requeridos as $campo) {
                        if (!isset($cntc[$campo]) || $cntc[$campo] === null) { //si el campo es null lo imprimo para que no rompa
                            // El campo es null o no está definido en $cntc
                            $output->writeln("Uno de los campos requeridos por la base de datos viene vacio desde la api zoho.");
                            // Log::info("PopulateUsersWithContacts-execute-contacto de zoho sin los datos requeridos: " . print_r($cntc, true));
                            $isNull = true;
                            break;
                        }
                    }


                    if($cntc['Caracter_stica_contacto'] !== 'Experiencia MSK'){
                        $output->writeln("El contacto no cuenta con 'Experiencia MSK'." );
                        $isNull = true;
                    }
                    if($cntc["Validador"] === null){
                        $output->writeln("El usuario no esta validado.");
                        $isNull = true;
                    }

                    if (!$isNull) {
                        $output->writeln("Creando usuario " . $cntc['Usuario'] . " ...");

                        $newUser = User::UpdateOrCreate(
                            [
                                'email' => $cntc['Usuario']
                            ],
                            [
                                'name' => $cntc['Full_Name'],
                                'email' => $cntc['Usuario'],
                                'password' => Hash::make($cntc['Password']),
                            ]
                        );

                        Contact::UpdateOrCreate(
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
                // 'trace' => $e->getTraceAsString(),
            ];
            $output->writeln("Hubo un error al ejecutar el codigo del comando y no pudo terminar. Para mas detalles revice el archivo de log.");
            Log::error("Error en PopulateUsersWithContacts: " . $e->getMessage() . "\n" . json_encode($err, JSON_PRETTY_PRINT));
        }
        $output->writeln("Carga de contactos y usuarios completa.");
        return 0;
    }
}


