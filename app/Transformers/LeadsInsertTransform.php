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
}