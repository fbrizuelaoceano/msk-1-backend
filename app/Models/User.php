<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'test',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function contact()
    {
        return $this->hasOne(Contact::class);
    }
    public function likes()
    {
        // return $this->hasMany(Like::class);
        return Like::where(["user_id" => $this->id]);
    }

    public static function updateNameByEmail(string $email, string $newName)
    {
        self::where('email', $email)->update(['name' => $newName]);
    }

    public static function updateOrCreateByContact($contactArrayObj)
    {
        $contact = Contact::where(['entity_id_crm' => $contactArrayObj['id']])->get()->first();

        if($contact->user??null !== null){
            $contact->user->update([
                'name' => $contactArrayObj['Full_Name'],
                'email' => $contactArrayObj['Email'],
                'password' => Hash::make($contactArrayObj['Password']),
                'test' => $contactArrayObj['usuario_prueba'],
            ]);
        }

    }
}
