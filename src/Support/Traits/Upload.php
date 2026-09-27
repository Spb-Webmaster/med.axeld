<?php

namespace Support\Traits;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
trait Upload
{
    public function UploadFile(UploadedFile $file, $folder = null, $disk = 'public', $filename = null)
    {
        $FileName = !is_null($filename) ? $filename : Str::random(10);
        return $file->storeAs(
            $folder,
            $FileName . "." . $file->getClientOriginalExtension(),
            $disk
        );
    }

    public function CopyFile($file, $folder = null, $disk = 'public', $filename = null):string | bool
    {
        $FileName = !is_null($filename) ? $filename : Str::random(10);

        $ext = pathinfo($file,  PATHINFO_EXTENSION);




        if (Storage::disk($disk)->exists($file)) {

            $new_path = $folder.'/'.$FileName.'.' .$ext;
         Storage::disk($disk)->makeDirectory($folder);
         $result = File::copy(Storage::disk($disk)->path($file), Storage::disk($disk)->path($new_path));

            \Log::info('файл который пришел ' .  $file); // в логи
            \Log::info('файл который создается ' .  $new_path); // в логи
            \Log::info($result); // в логи
         if($result) {
             return  $new_path;
         }

        }
        return false;

    }

    public function deleteFile($path, $disk = 'public'):void
    {
        Storage::disk($disk)->delete($path);
    }

    public function deleteDir($path):void
    {
        /** storage/ ... **/
        File::deleteDirectory($path);

    }


}
