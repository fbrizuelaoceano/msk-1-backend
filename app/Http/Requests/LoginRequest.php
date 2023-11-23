<?php

namespace App\Http\Requests;

use App\Rules\Recaptcha;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;

class LoginRequest extends FormRequest
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
            'email.required' => 'El Email es obligatorio.',
            'email.email' => 'El Email debe ser una direccion de correo electronico valida.',
            'password.required' => 'La Contraseña es obligatoria.',
            'password.string' => 'La Contraseña debe ser una cadena de caracteres.',
            'recaptcha_token' => 'El token de recaptcha es requerido.'
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
            'email' => 'required|string|email',
            'password' => 'required|string',
            'recaptcha_token' => App::environment('production') ? ['required', new Recaptcha] : [],
        ];
    }
}
