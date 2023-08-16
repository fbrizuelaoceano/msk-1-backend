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
            'secret' => '6LcIf-6Lf1FPomAAAAAOiTM3JObb8mwKPmu25MmNegoB3l',
            'response' => $value
        ])->object();

        Log::info("Rules-Recaptcha-response: " . print_r($response, true));

        // if(!$response->success && !$response->score >= 0.7)
        if(!$response->success)
            $fail('La verificacion de reCaptcha ha fallado. Message: ' . json_encode($response));
    }
}
