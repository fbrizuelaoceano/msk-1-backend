<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{PurchasingProcess};

class Lead extends Model
{
    use HasFactory;
    protected $table = 'leads';
    protected $fillable = [
        'id',
        'entity_id_crm',
        'lead_status',
        'source_lead',
        'lead_source',
        'name',
        'last_name',
        'email',
        'telephone',
        'method_contact',
        'contact_id',
        'profession',
        'speciality',
        'phone',
        'country',
        'career',
        'year',
        'other_speciality',
        'other_profession',

    ];
    public $timestamps = true;
    public $hidden = ['created_at', 'updated_at', 'source_lead', 'lead_status', 'lead_source', 'id'];
    private static $formAttributes = [
        'name',
        'last_name',
        'email',
        'phone',
        'method_contact',
        'contact_id',
        'method_contact_id',
        'profession',
        'speciality'
    ];

    public static function getFormAttributes()
    {
        return self::$formAttributes;
    }

    public function profession()
    {
        $profession = Profession::where('id', $this->profession)->first()->name;
        return $profession;
    }

    public function contact()
    {
        // $contact = Contact::where('id', $this->contact_id_fk)->first();
        $contact = $this->belongsTo(Contact::class, 'contact_id_fk');
        return $contact;
        // return $contact;
    }

    public static function store($data){
        self::create($data);
    }
}
