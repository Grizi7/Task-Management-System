<?php
namespace App\Enums;

enum TaskStatusEnum: int

{
    case pending = 0;
    case in_progress = 1;
    case completed = 2;

    public function label(): string
    {
        return match ($this) {
            TaskStatusEnum::pending => 'pending',
            TaskStatusEnum::in_progress => 'in-progress',
            TaskStatusEnum::completed => 'completed',
        };
    }
}