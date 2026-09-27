<?php

declare(strict_types=1);

namespace App\Enums\Resources;

enum FullTemplate: string
{
    case Default = 'default';

    public function label(): string
    {
        return match($this) {
            self::Default => 'Стандартный',
        };
    }

    public function view(string $resource): string
    {
        $specific = "pages.resourses.{$resource}.templates.full.{$this->value}";
        $common   = "pages.common.resourses.templates.full.{$this->value}";

        if (view()->exists($specific)) return $specific;

        return $common;
    }

    public static function toOptions(): array
    {
        return array_column(
            array_map(fn(self $case) => ['value' => $case->value, 'label' => $case->label()], self::cases()),
            'label',
            'value'
        );
    }
}
