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