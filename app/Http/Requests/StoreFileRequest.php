<?php

namespace App\Http\Requests;

use App\Rules\validIssuer;
use App\Rules\validIssuerIdentityProofKey;
use App\Rules\validRecipient;
use App\Rules\validSignature;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFileRequest extends FormRequest
{
    public $validator = null;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $this->merge(json_decode($this->file()['file']->getContent(), true));
        $signature = $this->get('signature');

        return [
            'file' => [
                'required',
                'file',
                'mimes:json,csv',
                'max:2000',
            ],
            'data.recipient.name' => [
                'string',
                'required',
            ],
            'data.recipient.email' => [
                'email',
                'required',
            ],
            'data.recipient' => new validRecipient(),
            'data.issuer.name' => [
                'string',
                'required',
            ],
            'data.issuer' => new validIssuer(),
            'data.issuer.identityProof' => new validIssuerIdentityProofKey(),
            'data' => new validSignature($signature),
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        $this->validator = $validator;
    }
}
   