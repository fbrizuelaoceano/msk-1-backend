<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{Lead};

class Contact extends Model
{
    use HasFactory;
    protected $table = 'contacts';

    protected $fillable = [
        'entity_id_crm',
        'name',
        "last_name",
        "profession",
        "speciality",
        "user_id",
        "rfc",
        "dni",
        "fiscal_regime",
        "phone",
        "email",
        "sex",
        "date_of_birth",
        "country",
        "postal_code",
        "address",
        "other_speciality",
        "other_profession",
        "state",
        "career",
        "year"
    ];

    private static $formAttributes = [
        'entity_id_crm',
    ];
    /***Relationships */
    public function user()
    {
        $user = $this->belongsTo(User::class, 'user_id');
        return $user;
    }
    public static function getFormAttributes()
    {
        return self::$formAttributes;
    }
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
    public function courses_progress()
    {
        return $this->hasMany(CourseProgress::class);
    }
    /***End Relationships */
    private static $formAttributesUpdateProfileRequest = [
        'name',
        'last_name',
        'email',
        'phone',
        'fiscal_regime',
        'address',
        'country',
        'state',
        'postal_code',

        'rfc',
        'rut',
        'mui',
        'dni',

        'profession',
        'speciality',
        'other_profession',
        'other_speciality',

        'career',
        'year'
    ];
    public static function getFormAttributesUpdateProfileRequest()
    {
        return self::$formAttributesUpdateProfileRequest;
    }

    private static $formAttributesPutProfile = [
        'name',
        "last_name",
        "rfc",
        "dni",
        "fiscal_regime",
        "phone",
        "email",
        "sex",
        "date_of_birth",
        "country",
        "postal_code",
        "address",
        "profession",
        "speciality",
        "other_profession",
        "other_speciality",
        "state",
        "career",
        "year"
    ];
    public static function getFormAttributesPutProfile()
    {
        return self::$formAttributesPutProfile;
    }

    public static function mappingData(array $contactInformation)
    {

        return [
            'name' => $contactInformation["First_Name"],
            'last_name' => $contactInformation["Last_Name"],
            'email' => $contactInformation["Email"],
            'phone' => $contactInformation["Phone"],
            'entity_id_crm' => $contactInformation["id"],
            'profession' => $contactInformation["Profesi_n"],
            'other_profession' => $contactInformation["Otra_profesi_n"],
            'speciality' => $contactInformation["Especialidad"],
            'other_speciality' => $contactInformation["Otra_especialidad"],
            'rfc' => $contactInformation["RFC"],
            'country' => $contactInformation["Pais"],
            'fiscal_regime' => $contactInformation["R_gimen_fiscal"],
            'postal_code' => $contactInformation["Mailing_Zip"],
            'address' => $contactInformation["Mailing_Street"],
            'state' => $contactInformation["Mailing_State"], // 'date_of_birth' => $contactInformation["Date_of_Birth"],//no esta en el form de Datos personales
            // 'sex' => $contactInformation["Sexo"],//no esta en el form de Datos personales
            // 'validate' => $contactInformation["Validador"],//no esta en el form de Datos personales
        ];

    }

    public static function updateOrCreateContact(array $data)
    {
        return self::updateOrCreate(['entity_id_crm' => $data["id"]], $data);
    }



}