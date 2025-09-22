<?php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rule;
use App\Core\Http\Requests\BaseRequest;
use App\Enums\TaskStatusEnum;

class UpdateTaskStatusRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::in(TaskStatusEnum::getValues()),
            ],
        ];
    }
}
