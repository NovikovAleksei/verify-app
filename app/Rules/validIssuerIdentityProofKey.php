<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Session;
use Illuminate\Translation\PotentiallyTranslatedString;

class validIssuerIdentityProofKey implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $data = $this->fetchDns($value['location']);

        $validData = [];
        foreach ($data['Answer'] as $locations) {
            $validData[] = str_contains($locations['data'], $value['key']);
        }
        if (!in_array(true, $validData)) {
            Session::push('code', ['invalid_issuer']);
        }
    }

    /**
     * @param string $location
     * @return array
     */
    private function fetchDns(string $location): array
    {
        return json_decode(file_get_contents('https://dns.google/resolve?name='. $location .'&type=TXT'), true);
    }
}
