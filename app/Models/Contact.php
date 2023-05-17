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
        'name',
        'last_name',
        'email',
        'id',
        'entity_id_crm',
        'dni',
        'sex',
        'date_of_birth',
        'registration_number',
        'area_of_work',
        'training_interest',
        'type_of_address',
        'country',
        'postal_code',
        'street',
        'locality',
        'province_state',
        'lead_id',
        'user_id'
    ];

    private static $formAttributes = [
        'name',
        'last_name',
        'email',
        'dni',
        'sex',
        'date_of_birth',
        'registration_number',
        'area_of_work',
        'training_interest',
        'type_of_address',
        'country',
        'postal_code',
        'street',
        'locality',
        'province_state',
        'entity_id_crm'
    ];

    public function lead()
    {
        $lead = $this->hasOne(Lead::class, 'contact_id', 'id');
        return $lead;
    }
    // public function user()
    // {
    //     $user = $this->hasOne(User::class, 'contact_id','id');
    //     return $user;
    // }
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