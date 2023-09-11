<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactUsRequest extends FormRequest
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
            'message' => 'Error de validacion en los campos del formulario.',
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
            'First_Name' => "required",
            'Last_Name' => "required",
            'Email' => "required|email|min:8|",
            'Phone' => "required",
            'Profesion' => "required",
            'Especialidad' => "required",
            'Otra_profesion' => 'required_if:profession,Otra profesión',
            'Otra_especialidad' => 'required_if:speciality,Otra Especialidad',
            'Pais' => "required",
            'Preferencia_de_contactaci_n' => 'required'
        ];
    }
}
