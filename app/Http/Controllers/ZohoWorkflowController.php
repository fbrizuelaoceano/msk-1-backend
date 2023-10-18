<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Product;
use App\Models\Profession;
use App\Models\Speciality;
use App\Models\Quote;
use App\Models\User;
use App\Models\Contact;
use App\Models\CourseProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;



class ZohoWorkflowController extends Controller
{
    public function setNewPasswordFromMSK(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required',
        ]);

        $user = User::where(["email" => $request->email])->first();
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'Successfully find user!',
        ], 201);
    }

    public function salesForCRM(Request $request)
    {
        try {
            $contactObj = json_decode($_POST['contact']);
            $saleObj = json_decode($_POST['sale']);

            $user = User::updateOrCreate(['email' => $contactObj->Usuario], [
                'name' => $contactObj->Full_Name,
                'email' => $contactObj->Usuario,
                'password' => Hash::make($contactObj->Password),
            ]);
            // Log::info("salesForCRM-user: " . print_r($user, true));


            $contact = Contact::updateOrCreate(['entity_id_crm' => $contactObj->id], [
                'name' => $contactObj->First_Name,
                'last_name' => $contactObj->Last_Name,
                'email' => $contactObj->Usuario,
                'profession' => $contactObj->Profesi_n,
                'specialty' => $contactObj->Especialidad,
                'user_id' => $user->id,
                'entity_id_crm' => $contactObj->id,
                'rfc' => $contactObj->RFC ?? '',
                'sex' => $contactObj->Sexo,
                'country' => $contactObj->Pais,
                'phone' => $contactObj->Phone,
                'validate' => $contactObj->Validador,
                'fiscal_regime' => $contactObj->R_gimen_fiscal,
                'postal_code' => $contactObj->Mailing_Zip,
                'address' => $contactObj->Mailing_Street,
                'date_of_birth' => $contactObj->Date_of_Birth
            ]);
            // Log::info("salesForCRM-contact: " . print_r($contact, true));

            $contactArrayObj = (array) $contactObj;
            $formCourseProgress = (array) $contactArrayObj["Formulario_de_cursada"];
            Log::info("salesForCRM-formCourseProgress: " . print_r($formCourseProgress, true));

            if ($formCourseProgress) {

                foreach ($formCourseProgress as $formCPstdClass) {
                    $formCP = (array) $formCPstdClass;
                    // Log::info("salesForCRM-foreach: " . print_r($formCP, true));

                    $mskObjDBCourseProgress = null;
                    $mskObjDBCourseProgress = [
                        'entity_id_crm' => $formCP['id'],
                        'Fecha_finalizaci_n' => $formCP['Fecha_finalizaci_n'],
                        // 'Nombre_de_curso' => $formCP['Nombre_de_curso']['name'].' id:'.$formCP['Nombre_de_curso']['id'],
                        'Nombre_de_curso' => $formCP['Nombre_de_curso']->name,
                        'Estado_de_OV' => $formCP['Estado_de_OV'],
                        'field_states' => $formCP['$field_states'],
                        'Created_Time' => $formCP['Created_Time'],
                        // 'Parent_Id' => $formCP['Parent_Id']['name'].' id:'.$formCP['Parent_Id']['id'],
                        'Parent_Id' => $formCP['Parent_Id']->id,
                        'Nota' => $formCP['Nota'],
                        'Estado_cursada' => $formCP['Estado_cursada'],
                        'Avance' => $formCP['Avance'],
                        'Fecha_de_expiraci_n' => $formCP['Fecha_de_expiraci_n'],
                        'in_merge' => $formCP['$in_merge'],
                        'Fecha_de_compra' => $formCP['Fecha_de_compra'],
                        'Enrollamiento' => $formCP['Enrollamiento'],
                        'Fecha_de_ltima_sesi_n' => $formCP['Fecha_de_ltima_sesi_n'],
                        'contact_id' => $contact->id,
                        'Product_Code' => $formCP['Product_Code'],
                        'C_digo_de_Curso_Cedente' => $formCP['C_digo_de_Curso_Cedente'],
                        'Plataforma_enrolamiento' => $formCP['Plataforma_enrolamiento'],
                    ];

                    // Log::info("salesForCRM-mskObjDBCourseProgress: " . print_r($mskObjDBCourseProgress, true));

                    CourseProgress::updateOrCreate([
                        'entity_id_crm' => $formCP['id'],
                        'contact_id' => $contact->id
                    ], $mskObjDBCourseProgress);

                }
            }

            $contract = Contract::updateOrCreate(['entity_id_crm' => $saleObj->id], [
                'contact_id' => $contact->id,
                'entity_id_crm' => $saleObj->id,
                'so_crm' => $saleObj->SO_Number,
                'status' => $saleObj->Status,
                'status_payment' => $saleObj->Estado_de_cobro,
                'country' => $saleObj->Pais_de_facturaci_n,
                'currency' => $saleObj->Currency,
            ]);

            $productDetails = $saleObj->Product_Details;
            // Log::info("salesForCRM-productDetails: " . print_r($productDetails, true));

            foreach ($productDetails as $pd) {
                // Log::info("salesForCRM-pd: " . print_r($pd, true));

                Product::updateOrCreate([
                    'entity_id_crm' => $pd->product->id,
                    'contract_entity_id' => $saleObj->id
                ], [
                    'entity_id_crm' => $pd->product->id,
                    'contract_id' => $contract->id,
                    'contract_entity_id' => $saleObj->id,
                    'quantity' => $pd->quantity,
                    'discount' => $pd->Discount,
                    'price' => $pd->total,
                    'product_code' => (int) $pd->product->Product_Code
                ]);

            }

            return response()->json([
                'user' => $user,
                'contact' => $contact
            ]);
        } catch (\Exception $e) {
            $err = [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                // 'trace' => $e->getTraceAsString(),
            ];
            Log::channel('zohoWorkFlow')->error("Error en salesForCRM: " . $e->getMessage() . "\n" . json_encode($err, JSON_PRETTY_PRINT));

            return response()->json([
                'error' => 'Ocurrió un error en el servidor',
                $err,
            ], 500);
        }
    }

    public function ValidatedUser(Request $request)
    {
        try {
            $contactObj = json_decode($_POST['contact']);
            $contact = Contact::where('email', $contactObj->Usuario)->update([
                'validate' => $contactObj->Validador,
            ]);

            return response()->json(['contact' => $contact]);
        } catch (\Exception $e) {
            $err = [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                // 'trace' => $e->getTraceAsString(),
            ];
            Log::channel('zohoWorkFlow')->error("Error en ValidatedUser: " . $e->getMessage() . "\n" . json_encode($err, JSON_PRETTY_PRINT));

            $status = 500;
            return response()->json([
                'error' => 'Ocurrió un error en el servidor',
                $err,
                $status
            ], $status);
        }
    }

    function UpdateQuotes(Request $request)
    {
        try {
            $quoteObjstdClass = json_decode($_POST['quote']);
            // Log::info("OnDev-quoteObjstdClass: " . print_r($quoteObjstdClass, true));
            $quoteObj = (array) $quoteObjstdClass;
            // Log::info("OnDev-quoteObj: " . print_r($quoteObj, true));

            //prueba desde postman
            // $quoteObj = $request->quote["context"];

            $mskObjDBQuote = Quote::mappingData($quoteObj);

            // Log::info("OnDev-mskObjDBQuote: " . print_r($mskObjDBQuote, true));
            // Log::info("OnDev-quoteObj: " . print_r($quoteObj, true));
            // Log::info("OnDev-quoteObj->id: " . print_r($quoteObj["Contact_Name"]->id, true));

            $contact = Contact::where("entity_id_crm", $quoteObj["Contact_Name"]["id"])->first();

            if ($contact) { //rober
                $mskObjDBQuote["contact_id"] = $contact->id;
            }

            $quote = Quote::updateOrCreate(
                [
                    'entity_id_crm' => $mskObjDBQuote['entity_id_crm']
                ],
                $mskObjDBQuote
            );
            // Log::info("Quote::updateOrCreate: " . print_r($quote, true));

            return response()->json([
                $mskObjDBQuote,
                $quote,
            ]);

        } catch (\Exception $e) {

            $err = [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                // 'trace' => $e->getTraceAsString(),
            ];

            Log::channel('zohoWorkFlow')->error("Error en UpdateQuotes: " . $e->getMessage() . "\n" . json_encode($err, JSON_PRETTY_PRINT));

            return response()->json([
                'error' => 'Ocurrió un error en el servidor',
                $err,
            ], 500);
        }
    }

    /**
     *    Datos desde postman                        Datos desde api con api-names.
     *   {
     *       "entity_id_crm": "5344455000004398022",
     *       "name": "Eva2",                         [First_Name] => Eva
     *       "last_name": "Marmolejo2",              [Last_Name] => Marmolejo
     *       "email": "emarmolejo2@msklatam.com",    [Email] => emarmolejo@msklatam.com
     *       "phone": "+34 222 22 22 22",            [Phone] => +34 619 96 13 17
     *       "profession": "Abogado2",               [Profesi_n] =>
     *       "other_profession": null,               [Otra_profesi_n] =>
     *       "speciality": "Derecho penal2",         [Especialidad] => Bioquímica
     *       "other_speciality": null,               [Otra_especialidad] =>
     *       "address": "Calle 2222",                [Mailing_Street] => Calle 1123
     *       "country": "Chile2",                    [Pais] => México
     *       "state": "Ciudad de México2",           [Mailing_City] => Ciudad de México
     *       "postal_code": "22222",                 [Mailing_Zip] => 03100
     *       "rfc": "XAXX020202000",                 [RFC] => XAXX010101000
     *       "fiscal_regime": "General2"             [R_gimen_fiscal] => 616 Sin obligaciones fiscales
     *   }
     */
    // Esto recibe lo de la regla


    public function UpdateContact(Request $request)
    {
        try {
            $contactObjstdClass = json_decode($_POST['contact']);
            $contactArrayObj = (array) $contactObjstdClass;
            Log::channel('zohoWorkFlow')->info("zohoWorkflow->contactArrayObj: " . print_r($contactArrayObj, true));

            $user = User::where('email', $contactArrayObj['Email'])->first();

            $mskObjDBContact = Contact::mappingData($contactArrayObj);

            $mskObjDBContact['user_id'] = $user->id;

            $updatedContact = Contact::updateOrCreateContact($contactArrayObj['id'], $mskObjDBContact);
            $contactCourses = $updatedContact->courses_progress()->get();


            User::updateOrCreateByContact($contactArrayObj);
            // User::updateNameByEmail($contactArrayObj["Usuario"], $contactArrayObj["Full_Name"]);

            //traer contact con buscar courses_progress
            //actualizar los datos de cursadas

            $formCourseProgress = (array) $contactArrayObj["Formulario_de_cursada"];

            if (isset($formCourseProgress)) {
                $mskObjDBCourseProgress = [];

                foreach ($formCourseProgress as $formCPstdClass) {
                    $arrayFormCP = (array) $formCPstdClass;
                    $mskObjDBCourseProgress = CourseProgress::mappingData($arrayFormCP, $updatedContact->id);

                    CourseProgress::updateOrCreate([
                        'entity_id_crm' => $arrayFormCP['id'],
                        'contact_id' => $updatedContact->id
                    ], $mskObjDBCourseProgress);
                }
            }

            $contactCourses->each(function ($contactCourse) use ($formCourseProgress) {
                $entityIdCrm = $contactCourse->entity_id_crm;
                $formCourseIds = array_column($formCourseProgress, 'id');
                $conditionToDelete = !in_array($entityIdCrm, $formCourseIds);

                if ($conditionToDelete) {
                    $contactCourse->delete();
                }
            });

            return response()->json([
                //$mskObjDBContact,
                $updatedContact,
            ]);

        } catch (\Exception $e) {

            $err = [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                // 'trace' => $e->getTraceAsString(),
            ];

            Log::channel('zohoWorkFlow')->error("Error en UpdateContact: " . $e->getMessage() . "\n" . json_encode($err, JSON_PRETTY_PRINT));

            return response()->json([
                'error' => 'Ocurrió un error en el servidor',
                $err,
            ], 500);
        }
    }

}
