<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, $this->response($validator));
    }
    protected function response($validator)
    {
        $errors = $validator->errors();

        return response()->json([
            'message' => 'Error de validacion en los campos del perfil.',
            'errors' => $errors,
            'progress' => 2,
        ], 422);
    }

    public function messages()
    {
        return [
            'required' => 'El campo es obligatorio.',
            'email' => 'Debe ingresar una dirección de correo válida.',
            'min' => 'El campo debe tener :min o más caracteres.',
            'required_if' => 'El campo es obligatorio.',
        ];
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => "required",
            'last_name' => "required",
            'email' => "required|email|min:8|",
            'phone' => "required",
            
            // 'profession' => 'nullable|required_if:speciality,null',
            'profession' => "required",
            'other_profession' => 'required_if:profession,Otra profesión',
            
            'speciality' => "required",
            // 'speciality' => 'nullable|required_if:profession,null',
            'other_speciality' => 'required_if:speciality,Otra Especialidad',
            
            'address' => "required", 
            'country' => "required",
            'state' => "required",
            'postal_code' => "required",
            
            //identificacion
            'rfc' => 'required_if:country,México',
            'rut' => 'required_if:country,Chile',
            'mui' => 'required_if:country,Ecuador',
            'dni' => 'required_if:country,Argentina',

            'fiscal_regime' => "required"
        ];
    }

    public static $formAttributes = [
        'name',
        'last_name',
        'email',
        'phone',
        'profession',
        'other_profession',
        'speciality', 
        'other_speciality',
        'address', 
        'country',
        'state',
        'postal_code',

        'rfc',
        'rut',
        'mui',
        'dni',

        'fiscal_regime'
    ];
}
