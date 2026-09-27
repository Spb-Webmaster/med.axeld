<?php

namespace App\View\Components\Files;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Download extends Component
{
    public array $files;

    private array $iconMap = [
        'pdf'  => 'pdf.svg',
        'doc'  => 'doc.svg',
        'docx' => 'doc.svg',
        'xls'  => 'excel.svg',
        'xlsx' => 'excel.svg',
        'jpg'  => 'jpg.svg',
        'jpeg' => 'jpg.svg',
        'png'  => 'png.svg',
    ];

    public function __construct(array $files = [])
    {
        $this->files = array_filter(
            array_map(fn($file) => !empty($file['file']) ? array_merge($file, [
                'icon' => $this->iconMap[strtolower(pathinfo($file['file'], PATHINFO_EXTENSION))] ?? 'none.svg',
                'name' => $file['label'] ?: basename($file['file']),
            ]) : null, $files)
        );
    }

    public function render(): View|Closure|string
    {
        return view('components.files.download');
    }
}
