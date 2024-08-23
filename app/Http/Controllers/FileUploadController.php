<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFileRequest;
use App\Models\Verification;
use Illuminate\Support\Facades\Session;

class FileUploadController extends Controller
{
    public function upload(StoreFileRequest $request): string
    {
        $validated = $request->validated();

        $validated['code'] = 'verified';
        if (Session::has('code')) {
            $validated['code'] = '';
            $errors = Session::get('code');
            if (is_array($errors)) {
                foreach ($errors as $error) {
                    $validated['code'] .= $error[0] . ':';
                }
            }
        }

        Verification::query()->create($this->prepare($validated));

        Session::forget('code');

        return response()->json([
            'data' => [
                'issuer' => $validated['data']['issuer']['name'],
                'result' => $validated['code'],
            ]
        ]);
    }

    protected function prepare($data): array
    {
        // user_id Auth::user() instead of 1
        return [
            'user_id' => 1,
            'file_type' => 'json',
            'verification_result' => $data['code'],
        ];
    }
}
