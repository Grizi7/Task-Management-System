<?php

namespace App\Http\Requests\Api;

use App\Core\Http\Requests\BaseRequest;
class TaskRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => [
                request()->method() === 'POST' ? 'required' : 'nullable',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'due_date' => [
                'nullable',
                'date',
            ],
            'assignee_id' => [
                request()->method() === 'POST' ? 'required' : 'nullable',
                'exists:users,id',
            ],
        ];
    }
}
