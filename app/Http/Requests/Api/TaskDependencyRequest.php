<?php

namespace App\Http\Requests\Api;

use App\Core\Http\Requests\BaseRequest;
class TaskDependencyRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'dependency_ids' => [
                'required',
                'array',
            ],
            'dependency_ids.*' => [
                'exists:tasks,id',
            ],
        ];
    }
}
