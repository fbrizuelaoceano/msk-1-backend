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
    ];

    private static $formAttributes = [
        'entity_id_crm',
    ];

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

}
