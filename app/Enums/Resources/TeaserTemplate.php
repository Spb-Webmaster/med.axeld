<?php

declare(strict_types=1);

namespace App\Enums\Resources;

enum TeaserTemplate: string
{
    case Default = 'default';
    case Colomns = 'colomns';
    case Training = 'training';

    public function label(): string
    {
        return match($this) {
            self::Default => 'Стандартный',
            self::Colomns => 'Колонки',
            self::Training => 'Обучение',
        };
    }

    public function view(string $section): string
    {
        $specific = "pages.{$section}.resourses.templates.teaser.{$this->value}";
        $common   = "pages.common.resourses.templates.teaser.{$this->value}";

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
