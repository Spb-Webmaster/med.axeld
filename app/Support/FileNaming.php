<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileNaming
{
    /**
     * Возвращает уникальное имя файла, сохраняя оригинальное название.
     * Если файл с таким именем уже существует — добавляет числовой суффикс: name_1.ext, name_2.ext ...
     */
    public static function deduplicate(UploadedFile $file, string $dir, string $disk = 'public'): string
    {
        $storage  = Storage::disk($disk);
        $name     = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $ext      = $file->getClientOriginalExtension();
        $filename = "{$name}.{$ext}";

        if (!$storage->exists("{$dir}/{$filename}")) {
            return $filename;
        }

        $counter = 1;
        do {
            $filename = "{$name}_{$counter}.{$ext}";
            $counter++;
        } while ($storage->exists("{$dir}/{$filename}"));

        return $filename;
    }
}
