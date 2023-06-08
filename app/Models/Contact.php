<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{Lead};

class Contact extends Model
{
    use HasFactory;
    protected $table = 'contacts';

    /*

*/
    protected $fillable = [
        'entity_id_crm',
        'name',
        'last_name',
        'profession',
        'speciality',
        'other_profession',
        'other_speciality',
        'user_id',
        'rfc',
        'dni',
        'fiscal_regime',
        'phone',
        'email',
        'sex',
        'date_of_birth',
        'country',
        'state',
        'postal_code',
        'address',
        'validate'
    ];

    private static $formAttributes = [
        'entity_id_crm',
        'name',
        'last_name',
        'profession',
        'speciality',
        'user_id',
        'rfc',
        'dni',
        'fiscal_regime',
        'phone',
        'email',
        'sex',
        'date_of_birth',
        'country',
        'postal_code',
        'address',
    ];

    public function lead()
    {
        $lead = $this->hasOne(Lead::class, 'contact_id', 'id');
        return $lead;
    }

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
}
