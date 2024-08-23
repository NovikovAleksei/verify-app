<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Session;
use Illuminate\Translation\PotentiallyTranslatedString;

class validSignature implements ValidationRule
{
    public function __construct(
        protected array $signature
    ) {}

    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        foreach ($this->preparedData($value) as $key => $data) {
            $hashes[] = hash('sha256', json_encode([$key => $data]));
        }

        sort($hashes);

        $hash = hash('sha256', json_encode($hashes));

        if (!hash_equals($hash, $this->signature['targetHash'])) {
            Session::push('code', ['invalid_signature']);
        }

    }

    private function preparedData($request): array
    {
        return [
            'id' => $request['id'],
            'name' => $request['name'],
            'recipient.name' => $request['recipient']['name'],
            'recipient.email' => $request['recipient']['email'],
            'issuer.name' => $request['issuer']['name'],
            'issuer.identityProof.type' => $request['issuer']['identityProof']['type'],
            'issuer.identityProof.key' => $request['issuer']['identityProof']['key'],
            'issuer.identityProof.location' => $request['issuer']['identityProof']['location'],
            'issued' => $request['issued'],
        ];

    }
}
