<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\CourseProgress;
use App\Models\ProductCRM;
use App\Services\ZohoCRMService;
use Illuminate\Http\{Request, Response};
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SSOController extends Controller
{

    // colocar el servicio de zohomsk
    private $zohoService;

    public function __construct(ZohoCRMService $service)
    {
        $this->zohoService = $service;
    }

    private function makeSSOTropos($data)
    {
        $secret = env("SSO_TROPOS_SECRET");
        //$secret = "contraseña sso";

        $timestamp = time();
        //$timestamp = 1686668709;

        $identifier = urlencode($data['email']);
        //$identifier = "correo%40dominio.com";

        $cadena = "sso_identifier=$identifier&sso_timestamp=$timestamp&secret=$secret";
        //$cadena = "sso_identifier=correo%40dominio.com&sso_timestamp=1686668709&secret=contraseña ss";

        $hash_cadena = md5($cadena);
        //$hash_cadena = "3beea8cb022146a06c399245c2001dfd";

        $cadenaGET = "?sso_identifier=$identifier&sso_timestamp=$timestamp&sso_hash=$hash_cadena";
        //$cadenaGET="?sso_identifier=correo%40dominio.com&sso_timestamp=1686668709&sso_hash=3beea8cb022146a06c399245c2001dfd";

        if (isset($data['cod_curso'])) {
            $cadenaGET .= "&codcurso=" . $data['cod_curso'];
        }

        // $cadenaGET.="&codcurso=C23796";
        return env('SSO_TROPOS_URL') . $cadenaGET;
    }

    private function makeSSOMoodle($data, $isMundoSanitario = false)
    {
        $secret = env("SSO_MOODLE_SECRET");
        $identifier = $data['email'];

        $hash = password_hash($identifier . $secret, PASSWORD_BCRYPT, array("cost" => 12));
        $identifier = urlencode($identifier);

        $cadenaGET = "?mail=" . $identifier . "&secret=" . $hash . "&curso=" . $data['cod_curso'];
        // $cadenaGET.="&codcurso=C23796";

        if ($isMundoSanitario) {
            return env('SSO_MOODLE_SANITARIO_URL') . $cadenaGET;
        }

        return env('SSO_MOODLE_URL') . $cadenaGET;
    }

    private function makeSSOLink($data)
    {

        $course = ProductCRM::where('product_code', $data['product_code'])->first();
        $coursePlatform = $course->platform;

        switch ($coursePlatform) {
            case 'Moodle MSK':
                return $this->makeSSOMoodle($data);
            case 'Moodle Mundo Sanitario':
                return $this->makeSSOMoodle($data, true);
            default: //Tropos
                return $this->makeSSOTropos($data);
        }

    }

    public function getLMSLink(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'cod_curso' => 'required',
                'product_code' => 'required'
            ]);

            if ($validator->fails()) {
                // Si la validación falla, puedes retornar una respuesta con los errores
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $data = $request->only(['email', 'cod_curso', 'product_code']);
            $sso = $this->makeSSOLink($data);

            // Contact::where('email', 'emarmolejo@msklatam.com')->get();
            $contacto = Contact::where('email', $data['email'])->get()->first();
            $formCourseProgressMSKDB = CourseProgress::where([
                'contact_id' => $contacto->id,
                'Product_Code' => $data['product_code']
            ])->get()->first();
            // $formCourseProgressMSKDB = CourseProgress::all();
            if ($formCourseProgressMSKDB) {
                if ($formCourseProgressMSKDB["Product_Code"] == $data["product_code"]) {
                    $contactZohoCRM = $this->zohoService->GetByIdAllDetails("Contacts", $contacto->entity_id_crm);

                    $formCourseProgress = (array) $contactZohoCRM["data"][0]["Formulario_de_cursada"];
                    if ($formCourseProgress) {
                        foreach ($formCourseProgress as $index => $formCPstdClass) {
                            $formCP = (array) $formCPstdClass;
                            $idDB = $formCourseProgressMSKDB["entity_id_crm"];
                            $idZoho = $formCP['id'];
                            if ($idZoho == $idDB) {
                                $fechaActual = Carbon::now();
                                $horaActual = $fechaActual->format('H:i:s');
                                // Combina la fecha actual, la hora actual y el desplazamiento horario
                                $fechaExpiracion = $fechaActual->toDateString() . "T" . $horaActual; //. "-03:00";
                                $formCourseProgress[$index]['Fecha_de_ltima_sesi_n'] = $fechaExpiracion;
                            }
                            // $formCP['Fecha_de_ltima_sesi_n'] = ;
                            // $formCP['Product_Code'] = ;
                            // $formCP['C_digo_de_Curso_Cedente'] = ;

                        }
                    }
                    $dataZoho = [
                        "data" => [
                            [
                                "Formulario_de_cursada" => $formCourseProgress,
                            ]
                        ]
                    ];
                    $contactZohoCRM = $this->zohoService->update("Contacts", $dataZoho, $contacto->entity_id_crm);
                }
            }


            return response()->json([
                "done" => true,
                "sso" => $sso,
                // $contacto->id,
                // $data['cod_curso'],
                // $formCourseProgress,
                // $formCourseProgressMSKDB,
                // $contactZohoCRM
            ]);
        } catch (\Exception $e) {
            $err = [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                // 'trace' => $e->getTraceAsString(),
            ];

            Log::error("Error en getLMSLink: " . $e->getMessage() . "\n" . json_encode($err, JSON_PRETTY_PRINT));

            return response()->json([
                'error' => 'Ocurrió un error en el servidor',
                $err,
            ], 500);
        }
    }
}