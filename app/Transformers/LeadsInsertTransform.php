<?php

namespace App\Transformers;

class LeadsInsertTransform
{
    public static function handle($array): array
    {
        return [
            "data" => [
                [
                    "Owner" => [
                        "id" => $array["user_id"] ?? null,
                    ],
                    "Last_Name" => $array["last_name"]?? null,
                    "Email" => $array["email"] ?? null,
                    "Description" => $array["description"] ?? null,
                    "Rating" => $array["rating"] ?? null,
                    "Website" => $array["website"] ?? null,
                    "Twitter" => $array["twitter"] ?? null,
                    "Salutation" => $array["salutation"] ?? null,
                    "First_Name" => $array["first_name"] ?? null,
                    "Lead_Status" => $array["lead_status"] ?? null,
                    "Industry" => $array["industry"] ?? null,
                    "Skype_ID" => $array["skype_id"] ?? null,
                    "Phone" => $array["phone"] ?? null,
                    "Street" => $array["street"] ?? null,
                    "Zip_Code" => $array["zipcode"] ?? null,
                    "Email_Opt_Out" => false ?? null,
                    "Designation" => $array["designation"] ?? null,
                    "City" => $array["city"] ?? null,
                    "No_of_Employees" => $array["number_employees"] ?? null,
                    "Mobile" => $array["mobile"] ?? null,
                    "State" => $array["state"] ?? null,
                    "Lead_Source" => $array["lead_source"] ?? null,
                    "Country" => $array["country"] ?? null,
                    "Fax" => $array["fax"] ?? null,
                    "Annual_Revenue" => $array["annul_revenue"] ?? null,
                    "Secondary_Email" => $array["secondary_email"] ?? null,
                ]
            ]
        ];
    }

    public static function handleContactUs($array): array
    {
        return [
            "data" => [
                [
                    "Phone" => $array['Phone'],
                    "Description" => $array['Description'],
                    "Preferencia_de_contactaci_n" => [$array['Preferencia_de_contactaci_n']] ?? null,
                    "Lead_Status" =>($array['leadSource'] === 'Descarga ebook') ? 'No habilitado' : 'Contacto urgente',
                    "Lead_Source" => $array['leadSource'] ?? null,
                    "First_Name" => $array['First_Name'],
                    "Last_Name" => $array['Last_Name'],
                    "Email" => $array['Email'],
                    "Profesion" => $array['Profesion'],
                    "Especialidad" => $array['Especialidad'],
                    "Otra_profesion" => $array['Otra_profesion'],
                    "Otra_especialidad" => $array['Otra_especialidad'],
                    "Ad_Account" => $array['utm_source'] ?? null,
                    "Ad_Set" =>  $array['utm_medium'] ?? null,
                    "Ad_Campaign" =>  $array['utm_campaign'] ?? null,
                    "Ad_Name" =>  $array['utm_content'] ?? null,
                    "Pais" => $array['Pais'],
                    "Cursos_consultados" => $array['Cursos_consultados'] ?? null,
                    "Carrera_de_estudio" => $array['career'] ?? null,
                    "A_o_de_estudio" => $array['year'] ?? null,
                    "URL_ORIGEN" => $array['URL_ORIGEN'] ?? null
                ]
            ]
        ];
    }

    public static function handleNewsletter($array): array
    {
        return [
            "data" => [
                [
                    "First_Name" => $array['First_Name'],
                    "Last_Name" => $array['Last_Name'],
                    "Email" => $array['Email'],
                    "Profesion" => $array['Profesion'],
                    "Especialidad" => $array['Especialidad'],
                    "Otra_profesion" => $array['Otra_profesion'],
                    "Otra_especialidad" => $array['Otra_especialidad'],
                    "Temas_de_interes" => $array['Temas_de_interes'],
                    "Lead_Source" => "Suscriptor newsletter",
                    "Ad_Account" => $array['utm_source'] ?? null,
                    "Ad_Set" => $array['utm_medium'] ?? null,
                    "Ad_Campaign" => $array['utm_campaign'] ?? null,
                    "Ad_Name" => $array['utm_content'] ?? null,
                    "Pais" => $array['country'] ?? null,
                    "Country" => $array['country'] ?? null,
                    "URL_ORIGEN" => $array['URL_ORIGEN'] ?? null
                ]
            ]
        ];
    }
}
