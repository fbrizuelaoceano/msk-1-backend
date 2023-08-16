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
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify',[
            'secret' => '6Lf1FPomAAAAAOiTM3JObb8mwKPmu25MmNegoB3l',
            'response' => $value
        ])->object();

        // Log::info("Rules-Recaptcha-value: " . print_r($value, true));
        // Log::info("Rules-Recaptcha-response: " . print_r($response, true));

        $errorMessages = [
            'missing-input-secret' => 'Falta el parámetro de clave secreta.',
            'invalid-input-secret' => 'El parámetro de clave secreta es inválido o está malformado.',
            'missing-input-response' => 'Falta el parámetro de respuesta.',
            'invalid-input-response' => 'El parámetro de respuesta es inválido o está malformado.',
            'bad-request' => 'La solicitud es inválida o está malformada.',
            'timeout-or-duplicate' => 'La respuesta ya no es válida: o es demasiado antigua o se ha utilizado previamente.'
        ];

        // if(!$response->success && !$response->score >= 0.7)
        if (!$response->success && isset($errorMessages[$response->{'error-codes'}[0]])) {
            $fail('La verificación de reCaptcha ha fallado. Mensaje: ' . $errorMessages[$response->{'error-codes'}[0]]);
        }
    }
}
