<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Recaptcha implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // https://www.youtube.com/watch?v=HK_146nJSWU&t=488s
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'response' => $value
        ])->object();

        if (!$response->success && isset($response->{'error-codes'}[0])) {
            $errorCode = $response->{'error-codes'}[0];
            $errorMessage = $this->getErrorMessage($errorCode);
            $fail('reCaptcha: ' . $errorMessage);
        }
    }

    private function getErrorMessage(string $errorCode): string
    {
        $errorMessages = [
            'missing-input-secret' => 'Falta el parametro de clave secreta.',
            'invalid-input-secret' => 'El parametro de clave secreta es invalido o esta malformado.',
            'missing-input-response' => 'Falta el parametro de respuesta.',
            'invalid-input-response' => 'El parametro de respuesta es invalido o esta malformado.',
            'bad-request' => 'La solicitud es invalida o esta malformada.',
            'timeout-or-duplicate' => 'Token reCaptcha no valido: o es demasiado antiguo o se ha utilizado previamente.',
        ];

        return $errorMessages[$errorCode] ?? 'Error de reCaptcha desconocido.';
    }
}