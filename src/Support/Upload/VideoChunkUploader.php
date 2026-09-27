<?php

namespace Support\Upload;

use Illuminate\Http\UploadedFile;
use Support\Traits\Makeable;

/**
 * Сервис для чанковой (пофрагментной) загрузки видеофайлов.
 *
 * Зачем нужна чанковая загрузка?
 *   PHP и веб-серверы имеют ограничение на размер загружаемого файла
 *   (upload_max_filesize, post_max_size). Большие видео (300–500 МБ)
 *   нельзя загрузить одним запросом. Решение — разбить файл на маленькие
 *   части (чанки) на стороне браузера и отправлять их по одному.
 *   Этот класс принимает чанки, записывает их в один временный файл,
 *   а после получения последнего — перемещает готовый файл в нужную папку.
 *
 * Использование:
 *   $uploader = VideoChunkUploader::make(session()->getId());
 *   $uploader->writeChunk($request->file('chunk'), $filename);
 *   $path = $uploader->assemble($filename, 'users/1');
 */
class VideoChunkUploader
{
    use Makeable;

    /**
     * Абсолютный путь к временной папке для хранения чанков.
     * Например: /var/www/storage/app/temp/video_abc123def456/
     */
    private string $tmpDir;

    /**
     * @param string $sessionId  Идентификатор сессии текущего пользователя.
     *                           Используется как уникальный суффикс временной папки,
     *                           чтобы одновременные загрузки разных пользователей
     *                           не перемешивались между собой.
     */
    public function __construct(private readonly string $sessionId)
    {
        $this->tmpDir = storage_path('app/temp/video_' . $this->sessionId);
    }

    /**
     * Принимает один чанк и дописывает его содержимое в конец временного файла.
     *
     * Как это работает:
     *   Браузер отправляет чанки строго по порядку (0, 1, 2, ...).
     *   Каждый чанк дописывается в конец одного и того же временного файла
     *   с помощью флага FILE_APPEND. Таким образом файл собирается по кусочкам.
     *
     * @param UploadedFile $chunk     Загруженный фрагмент файла (из $request->file('chunk'))
     * @param string       $filename  Очищенное имя файла (используется как имя временного файла)
     */
    public function writeChunk(UploadedFile $chunk, string $filename): void
    {
        // Создаём временную папку, если она ещё не существует.
        // 0755 — права доступа: владелец может читать/писать/выполнять,
        // остальные — только читать и выполнять.
        // true — создать все промежуточные папки (аналог mkdir -p).
        if (!is_dir($this->tmpDir)) {
            mkdir($this->tmpDir, 0755, true);
        }

        // Читаем бинарное содержимое чанка и дописываем в конец временного файла.
        // FILE_APPEND — ключевой флаг: без него каждый чанк перезаписывал бы файл,
        // а не дополнял его.
        file_put_contents(
            $this->tmpDir . '/' . $filename,
            file_get_contents($chunk->getRealPath()),
            FILE_APPEND
        );
    }

    /**
     * Собирает финальный файл: перемещает временный файл в постоянную папку.
     *
     * Вызывается только после получения последнего чанка.
     * После перемещения временная папка удаляется.
     *
     * @param string $filename    Очищенное оригинальное имя файла (нужно для определения расширения)
     * @param string $destFolder  Папка назначения относительно storage/app/public/
     *                            Например: 'users/1'
     * @return string             Относительный путь к файлу от storage/app/public/
     *                            Например: 'users/1/video_abc123.mp4'
     */
    public function assemble(string $filename, string $destFolder): string
    {
        // Извлекаем расширение из оригинального имени файла (mp4, mov, avi, webm)
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        // Генерируем уникальное имя для конечного файла.
        // uniqid('video_', true) даёт что-то вроде: video_6629f3a1b2c4d5.678901
        // Это исключает коллизии, если два пользователя загружают файл с одинаковым именем.
        $newFilename = uniqid('video_', true) . '.' . $ext;

        // Формируем абсолютный путь к папке назначения.
        // ltrim убирает лишний слэш в начале, если он есть.
        // Итог: /var/www/storage/app/public/users/1
        $destDir = storage_path('app/public/' . ltrim($destFolder, '/'));

        // Создаём папку назначения, если её нет
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        // Перемещаем собранный временный файл в постоянную папку.
        // rename() работает атомарно в пределах одной файловой системы —
        // файл либо переместится целиком, либо останется на месте (не будет "обрезан").
        rename($this->tmpDir . '/' . $filename, $destDir . '/' . $newFilename);

        // Удаляем временную папку — она больше не нужна
        $this->cleanup();

        // Возвращаем относительный путь, который сохраняется в базе данных.
        // Например: 'users/1/video_6629f3a1b2c4d5.mp4'
        return ltrim($destFolder, '/') . '/' . $newFilename;
    }

    /**
     * Удаляет временную папку после успешной сборки файла.
     *
     * rmdir() работает только с пустыми папками, поэтому вызывается
     * после rename() — когда единственный файл уже перемещён.
     * Символ @ подавляет предупреждение на случай,
     * если папка по какой-то причине уже удалена.
     */
    private function cleanup(): void
    {
        if (is_dir($this->tmpDir)) {
            @rmdir($this->tmpDir);
        }
    }
}
