<?php

namespace App\Core\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator): void
    {
        if (request()->wantsJson()) {
            $errors = $validator->errors();
            throw new HttpResponseException(response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $errors,
            ], ResponseAlias::HTTP_UNPROCESSABLE_ENTITY));
        } else {
            parent::failedValidation($validator);
        }
    }
}
