<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignUpRequest extends FormRequest
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
            'last_name.required' => 'El Apellido es obligatorio.',
            'last_name.string' => 'El Apellido debe ser una cadena de caracteres.',
            'email.required' => 'El Email es obligatorio.',
            'email.email' => 'El Email debe ser una direccion de correo electronico valida.',
            'email.unique' => 'El Email ya ha sido registrado.',
            'first_name.required' => 'El Nombre es obligatorio.',
            'first_name.string' => 'El Nombre debe ser una cadena de caracteres.',
            'phone.required' => 'El Telefono es obligatorio.',
            'phone.string' => 'El Telefono debe ser una cadena de caracteres.',
            'country.required' => 'El Pais es obligatorio.',
            'country.string' => 'El Pais debe ser una cadena de caracteres.',
            'profession.required' => 'La Profesion es obligatoria.',
            'profession.string' => 'La Profesion debe ser una cadena de caracteres.',
            'speciality.required' => 'La Especialidad es obligatoria.',
            'speciality.string' => 'La Especialidad debe ser una cadena de caracteres.',
            'Otra_profesion.required_if' => 'La Otra Profesion es obligatoria.',
            'Otra_profesion.string' => 'La Otra Profesion debe ser una cadena de caracteres.',
            'Otra_especialidad.required_if' => 'La Otra Especialidad es obligatorio.',
            'Otra_especialidad.string' => 'La Otra Especialidad debe ser una cadena de caracteres.',

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
            'last_name' => 'required|string',//Necesario para crear el contacto de crm
            'email' => 'required|string|email|unique:users',//Necesario para crear el contacto de crm
            'first_name' => 'required|string:',
            'phone' => 'required|string',
            'country' => 'required|string',
            'profession' => "required|string",
            'speciality' => "required|string",
            // 'speciality' => 'required_unless:profession,Estudiante|string',
            'Otra_profesion' => 'required_if:profession,Otra profesión',
            'Otra_especialidad' => 'required_if:speciality,Otra Especialidad',
            // 'career' => 'required_if:profession,Estudiante',
            // 'year' => 'required_if:profession,Estudiante',
        ];
    }
}
