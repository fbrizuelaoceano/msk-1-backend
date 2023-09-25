<?php

namespace App\Http\Requests;

use App\Rules\Recaptcha;
use Illuminate\Foundation\Http\FormRequest;

class LeadHomeNewsletterRequest extends FormRequest
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

        $status = 422;
        return response()->json([
            'message' => 'Error de validacion en los campos del registro.',
            'errors' => $errors,
            'status' => $status
        ], $status);
    }
    public function messages()
    {
        return [
            'Last_Name.required' => 'El Apellido es obligatorio.',
            'Last_Name.string' => 'El Apellido debe ser una cadena de caracteres.',
            'Email.required' => 'El Email es obligatorio.',
            'Email.email' => 'El Email debe ser una direccion de correo electronico valida.',
            'First_Name.required' => 'El Nombre es obligatorio.',
            'First_Name.string' => 'El Nombre debe ser una cadena de caracteres.',
            'Preferencia_de_contactaci_n.required' => 'La Preferencia de contacto es obligatoria.',
            'Preferencia_de_contactaci_n.string' => 'La Preferencia de contacto debe ser una cadena de caracteres.',
            'Profesion.required' => 'La Profesion es obligatoria.',
            'Especialidad.required_unless' => 'La Especialidad es obligatoria.',
            'Otra_profesion.required_if' => 'La Otra Profesion es obligatoria.',
            'Otra_profesion.string' => 'La Otra Profesion debe ser una cadena de caracteres.',
            'Otra_especialidad.required_if' => 'La Otra Especialidad es obligatoria.',
            'Otra_especialidad.string' => 'La Otra Especialidad debe ser una cadena de caracteres.',
            'Career.required_if' => 'La Carrera es obligatoria.',
            'Career.not_in' => 'Seleccione carrera.',
            'Year.required_if' => 'El Año es obligatorio.',
            'Year.not_in' => 'Seleccione año de carrera.',
            'utm_campaign.nullable' => 'Campaña UTM debe ser una cadena de caracteres.',
            'utm_content.nullable' => 'Contenido UTM debe ser una cadena de caracteres.',
            'utm_medium.nullable' => 'Medio UTM debe ser una cadena de caracteres.',
            'utm_source.nullable' => 'Fuente UTM debe ser una cadena de caracteres.',
            'Terms_And_Conditions2.required' => 'Debes aceptar los términos y condiciones para continuar.',
            'Terms_And_Conditions2.accepted' => 'Debes aceptar los términos y condiciones para continuar.',
            'Temas_de_interes.required' => 'Selecciona al menos un tema de interés.',
            'Temas_de_interes.array_min' => 'Selecciona al menos un tema de interés.',
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
            // 'Description' => 'required|string',
            'Last_Name' => 'required|string',
            'Email' => 'required|string|email',
            // 'recaptcha_token' => ['required', new Recaptcha],

            'First_Name' => 'required|string',

            //    'Preferencia_de_contactaci_n' => 'required|string',
            'Profesion' => 'required',
            'Especialidad' => 'required_unless:Profesion,Estudiante',
            'Otra_profesion' => 'required_if:Profesion,Otra profesión',
            'Otra_especialidad' => 'required_if:Especialidad,Otra Especialidad',
            'Career' => 'required_if:Profesion,Estudiante|not_in:Seleccionar carrera',
            'Year' => 'required_if:Profesion,Estudiante|not_in:Seleccionar año',

            'utm_campaign' => 'nullable|string',
            'utm_content' => 'nullable|string',
            'utm_medium' => 'nullable|string',
            'utm_source' => 'nullable|string',

            'Terms_And_Conditions2' => 'required|accepted',
            'Temas_de_interes' => 'required|array_min:1',
        ];
    }
}