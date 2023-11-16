<?php

namespace App\Transformers;

class ContactsInsertTransform
{
    public static function handle($array): array
    {
        return [
            "data" => [
                [
                    "Owner" => [
                        "id" => $array["owner_id"] ?? null,
                    ],
                    "Account_Name" => [
                        "id" => $array["account_id"] ?? null,
                    ],
                    "Vendor_Name" => [
                        "id" => $array["vendor_id"] ?? null,
                    ],
                    "Salutation" => $array["salutation"] ?? null,
                    "First_Name" => $array["first_name"] ?? null,
                    "Last_Name" => $array["last_name"] ?? null,
                    "Email" => $array["email"] ?? null,
                    "Secondary_Email" => $array["secondary_email"] ?? null,
                    "Mobile" => $array["mobile"] ?? null,
                    "Home_Phone" => $array["home_phone"] ?? null,
                    "Phone" => $array["phone"] ?? null,
                    "Other_Phone" => $array["other_phone"] ?? null,
                    "Description" => $array["description"] ?? null,
                    "Mailing_Zip" => $array["mailing_zip"] ?? null,
                    "Reports_To" => $array["reports_to"] ?? null,
                    "Mailing_State" => $array["mailing_state"] ?? null,
                    "Twitter" => $array["twitter"] ?? null,
                    "Other_Zip" => $array["other_zip"] ?? null,
                    "Mailing_Street" => $array["mailing_street"] ?? null,
                    "Other_State" => $array["other_state"] ?? null,
                    "Other_Country" => $array["other_country"] ?? null,
                    "Asst_Phone" => $array["asst_phone"] ?? null,
                    "Department" => $array["department"] ?? null,
                    "Skype_ID" => $array["skype_id"] ?? null,
                    "Assistant" => $array["assistant"] ?? null,
                    "Mailing_Country" => $array["mailing_country"] ?? null,
                    "Email_Opt_Out" => true,
                    "Date_of_Birth" => $array["date_of_birth"] ?? null,
                    "Mailing_City" => $array["mailing_city"] ?? null,
                    "Other_City" => $array["other_city"] ?? null,
                    "Title" => $array["title"] ?? null,
                    "Other_Street" => $array["other_street"] ?? null,
                    "Lead_Source" => $array["lead_source"] ?? null,
                    "Fax" => $array["fax"] ?? null,
                ]
            ]
        ];
    }

    public static function handleUpdateProfile($array): array
    {
        return  [
            'First_Name' => $array['name'],
            'Last_Name' => $array['last_name'],
            'Email' => $array['email'],
            'Phone' => $array['phone'],
            'Profesi_n' => $array['profession'],
            'Otra_profesi_n' => $array['other_profession'],
            'Especialidad' => $array['speciality'],
            'Otra_especialidad' => $array['other_speciality'],
            'Pais' => $array['country'],
            'Mailing_State' => $array['state'],
            'Mailing_Zip' => $array['postal_code'],
            'RFC' => $array['rfc'] ?? null,// Mexico
            'RUT' => $array['rut'] ?? null, // Chile
            'CUIT_CUIL_o_DNI' => $array['dni'] ?? null, // Argentina
            'R_gimen_fiscal' => $array['fiscal_regime'],
            'Mailing_Street' => $array['address'],
            'Carrera_de_estudio' => ($array['profession'] === "Estudiante") ? $array['career'] : null,
            'A_o_de_estudio' => ($array['profession'] === "Estudiante") ? $array['year'] : null,
        ];
    }
}
