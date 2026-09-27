<?php

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Support\Flash\Flash;
use Illuminate\Support\Facades\Route;
use \Support\Traits\Upload;

if (!function_exists('flash')) {

    function flash(): Flash
    {
        return app(Flash::class);
    }
}


/**
 * Телефон
 */

if (!function_exists('format_phone')) {

    function format_phone($from): string
    {
        if ($from) {
            $to = sprintf("%s (%s) %s-%s-%s",
                substr($from, 0, 1),
                substr($from, 1, 3),
                substr($from, 4, 3),
                substr($from, 7, 2),
                substr($from, 9)
            );
            return '+' . $to;
        }
        return '';
    }
}

/**
 * Телефон вырезаем из телефона все, кроме цифр
 */
if (!function_exists('phone')) {
    function phone(string $phone = null): string|int
    {
        return trim(preg_replace('/^1|\D/', "", $phone));
    }
}


/**
 * Удаляем кэш
 */
if (!function_exists('cache_clear ')) {

    function cache_clear($model = null)
    {
        Cache::forget('businesses');
        Cache::forget('individuals');
        Cache::forget('insights');
        Cache::forget('works');
        Cache::forget('solution_categories');
        Cache::forget('solution_items');
        Cache::forget('contacts');

    }
}

/**
 * День рождения
 */
if (!function_exists('birthdate')) {

    function birthdate($birthdate, $integer = null): string
    {

        if ($birthdate) {

            $birthday = new DateTime($birthdate);
            $interval = $birthday->diff(new DateTime);
            $int = $interval->y;
            if ($integer) {
                return (int)$int; // сокращенный вариант
            }
            $date = new DateTime($birthdate);
            $formattedDate = $date->format('d.m.Y');
            return $formattedDate . ' (' . $int . ')';

        }
        return '';
    }
}
/**
 * Более корректная дата рождения birthdate
 */
if (!function_exists('birthdate2')) {
    function birthdate2(string $birthdate = null): string|int|null
    {
        if ($birthdate == '1970-01-01') {
            return null;
        }
        if (strtotime($birthdate)) {
            $date = new DateTime($birthdate);
            $formattedDate = $date->format('d.m.Y');
            return $formattedDate;
        }
        return null;
    }
}

if (!function_exists('rusbirthdate')) {

    function rusbirthdate($birthdate): string
    {
        if ($birthdate) {

            $birthday = new DateTime($birthdate);
            $interval = $birthday->diff(new DateTime);
            $int = $interval->y;
            $date = new DateTime($birthdate);
            $formattedDate = $date->format('d.m.Y');
            return $formattedDate;


        }
        return '';
    }
}


if (!function_exists('rusdate_month')) {
    function rusdate_month($date): string|null
    {
        $timestump = strtotime($date);
        $month = [1 => "Январь", 2 => "Феврал", 3 => "Март", 4 => "Апрель", 5 => "Май", 6 => "Июнь", 7 => "Июль", 8 => "Август", 9 => "Сентябрь", 10 => "Октябрь", 11 => "Ноябрь", 12 => "Декабрь"];
        $y = date('Y', $timestump);


        $m = $month[date('n', $timestump)];


        return $m . ' ' . $y;

    }
}

/** Основная функция  */
if (!function_exists('active_linkMenu')) {
    function active_linkMenu($url, string $find = null, string $class = 'active'): string|null
    {

        $parse_url = parse_url(url()->current(), PHP_URL_PATH) ?? '/';

        if ($parse_url == '/') {
            /** * мы на главной  */
            if ($url == $parse_url) {
                return $class;
            }
        }


        if ($find) {

            if (str_starts_with(url()->current(), trim($url))) {
                return $class;
            }

            return str_starts_with(url()->current(), trim($url));
        }


        return ($url == url()->current()) ? $class : null;
    }
}

/** Функция для вложенного меню */
if (!function_exists('active_linkParentMenu')) {
    function active_linkParentMenu(array $childs = null, string $class = 'active'): string|null
    {

        if ($childs) {

            foreach ($childs as $child) {
                if(active_linkMenu(asset($child['link']), 'find') == $class) {
                 return $class;
                }

            }

             return null;

        }
        return null;

    }
}


/**
 *  route_name
 */

if (!function_exists('route_name')) {
    function route_name(): string|null
    {

        return Route::currentRouteName();
    }
}


/**
 *  работа с датами
 */

if (!function_exists('english_date')) {
    function english_date($date): string|null
    {
        $timestump = strtotime($date);


        $return = date('M j, Y', $timestump);

        return $return;
    }
}

if (!function_exists('rusdate')) {
    function rusdate($date): string|null
    {
        $timestump = strtotime($date);

        $month = [1 => "Янв", 2 => "Фев", 3 => "Мар", 4 => "Апр", 5 => "Май", 6 => "Июн", 7 => "Июл", 8 => "Авг", 9 => "Сен", 10 => "Окт", 11 => "Ноя", 12 => "Дек"];
        $return = date('d', $timestump);
        $return .= " " . $month[date('n', $timestump)];

        return $return;
    }
}


if (!function_exists('rusdate2')) {
    function rusdate2($date): string|null
    {
        $timestump = strtotime($date);
        $month = [1 => "янв.", 2 => "фев.", 3 => "мар.", 4 => "апр.", 5 => "май", 6 => "июн.", 7 => "июл.", 8 => "авг.", 9 => "сен.", 10 => "окт.", 11 => "ноя.", 12 => "дек."];

        $days = ['(вс)', '(пн)', '(вт)', '(ср)', '(чт)', '(пт)', '(сб)'];

        $day = $days[date("w", $date)];
        $m = $month[date('n', $timestump)];
        $d = date('d', $timestump);

        return $d . ' ' . $m . ' ' . $day;

    }
}


if (!function_exists('rusdate3')) {
    function rusdate3($date): string|null
    {
        $timestump = strtotime($date);
        $month = [1 => "января", 2 => "февраля", 3 => "марта", 4 => "апреля", 5 => "мая", 6 => "июня", 7 => "июля", 8 => "августа", 9 => "сентября", 10 => "октября", 11 => "ноября", 12 => "декабря"];

        $days = ['(вс)', '(пн)', '(вт)', '(ср)', '(чт)', '(пт)', '(сб)'];

        $day = $days[date("w", strtotime($date))];
        $m = $month[date('n', $timestump)];
        $y = date('Y', $timestump);
        $d = date('d', $timestump);

        return $d . ' ' . $m . ' ' . $y . 'г.';

    }
}

if (!function_exists('rusdate4')) {
    function rusdate4($date): string|null
    {
        $timestump = strtotime($date);
        $month = [1 => "янв.", 2 => "фев.", 3 => "мар.", 4 => "апр.", 5 => "май", 6 => "июн.", 7 => "июл.", 8 => "авг.", 9 => "сен.", 10 => "окт.", 11 => "ноя.", 12 => "дек."];

        $days = ['(вс)', '(пн)', '(вт)', '(ср)', '(чт)', '(пт)', '(сб)'];

        $day = $days[date("w", strtotime($date))];
        $m = $month[date('n', $timestump)];
        $y = date('Y', $timestump);
        $d = date('d', $timestump);
        $H = date('H', $timestump);
        $i = date('i', $timestump);

        return $d . ' ' . $m . ' ' . $y . 'г. (' . $H . ':' . $i . ')';

    }
}
if (!function_exists('rusdate5')) {
    function rusdate5($date): string|null
    {
        $timestump = strtotime($date);
        $month = [1 => "января", 2 => "февраля", 3 => "марта", 4 => "апреля", 5 => "мая", 6 => "июня", 7 => "июля", 8 => "августа", 9 => "сентября", 10 => "октября", 11 => "ноября", 12 => "декабря"];


        $d = date('d', $timestump);
        $m = $month[date('n', $timestump)];


        return $d . ' ' . $m;

    }
}

if (!function_exists('date_minute')) {
    function date_minute($date): string|null
    {
        $timestump = strtotime($date);
        $month = [1 => "янв", 2 => "фев", 3 => "мар", 4 => "апр", 5 => "мая", 6 => "июн", 7 => "июл", 8 => "авг", 9 => "сен", 10 => "окт", 11 => "ноя", 12 => "дек"];

        $days = ['(вс)', '(пн)', '(вт)', '(ср)', '(чт)', '(пт)', '(сб)'];

        $day = $days[date("w", strtotime($date))];
        $m = $month[date('n', $timestump)];
        $y = date('Y', $timestump);
        $d = date('d', $timestump);

        return $d . ' ' . $m . ' ' . $y . 'г. ' . date("H:i", $timestump);

    }
}


/**
 *  currency
 */

if (!function_exists('currency')) {
    function currency($cur)
    {
        foreach (config('currency.currency') as $k => $currency) {
            if ($k == $cur) {
                return $currency;
            }
        }
        return '';
    }
}

/**
 *  цены
 */

if (!function_exists('price')) {
    function price($price)
    {
        $price = (int)$price;
        if (is_int($price)) {
            return number_format($price, 0, '.', ' ');
        }
        return $price;
    }

}

/**
 *  получение переменных из папки storage
 */

if (!function_exists('config2')) {
    function config2($path = null)
    {
        if (is_null($path)) {
            return '';
        }
        $ar = explode(".", $path);

        $p = array_pop($ar); // последний элемент, это нужный ключ массива
        $storage_congig = implode("/", $ar) . '.php'; // складываеи в строку, и добавляем '.php', получаем точный путь файла

        if (Storage::disk('config')->exists($storage_congig)) {
            $result = include(storage_path('app/public/config/' . $storage_congig));
            // если есть такой файл, то получим содержимое (должен быть массив (return array))
        } else {
            return '';
        }


        return (isset($result[$p])) ? $result[$p] : '';
    }

}


if (!function_exists('config2_array')) {
    function config2_array($path = null): array|null
    {
        if (is_null($path)) {
            return null;
        }
        $ar = explode(".", $path);


        $storage_congig = implode("/", $ar) . '.php'; // складываеи в строку, и добавляем '.php', получаем точный путь файла

        if (Storage::disk('config')->exists($storage_congig)) {
            $result = include(storage_path('app/public/config/' . $storage_congig));
            // если есть такой файл, то получим содержимое (должен быть массив (return array))
        } else {
            return null;
        }


        return (isset($result)) ? $result : null;
    }

}


/**
 * url2
 */
if (!function_exists('url2')) {

    function url2()
    {
        $url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $url = explode('?', $url);
        $url = $url[0];
        return $url;
    }
}

/**
 * intervention
 */

if (!function_exists('intervention')) {
    function intervention(string $size, string $image = null, string $dir = 'countries', string $method = 'cover')
    {
        if (!$image) {
            return null;
        }
        if (!Storage::disk('intervention')->exists($image)) {
            return null;
        }


        // $dir = 'countries';
        // $method = 'fit'; // 'resize|crop|fit'
        $file = File::basename($image);

        // dd($image);
        abort_if(!in_array($size, config('thumbnail.allowed_sizes', [])),
            403,
            'size not allowed'
        );


        $storage = Storage::disk('intervention');


        $realPath = $image;
        $newDirPath = "$dir/$method/$size";
        $resultPaht = "$newDirPath/$file";

    /*    if (!$storage->exists($newDirPath)) {
            $storage->makeDirectory($newDirPath);
        }*/

        $newFullPath = $storage->path($newDirPath);
        if (!is_dir($newFullPath)) {

            $storage->makeDirectory($newDirPath);
          //  chmod($newDirPath, 0775);       // Устанавливаем правильные права на вновь созданные директории
        }

        if (!$storage->exists($resultPaht)) {

            $image = Image::decode($storage->path($realPath));


            [$w, $h] = explode('x', $size);
            if ($h == 0) {
                $image->{$method}(width: $w);
            } else {
                $image->{$method}($w, $h);

            }


            try {
                $image->save($storage->path($resultPaht));
            } catch (NotWritableException $e) {
                logger("Error saving image: {$e->getMessage()} at {$e->getFile()}:{$e->getLine()}");
                throw $e;
            }

        }


        return 'storage/' . $resultPaht;

    }
}
/**
 * Textarea оставляем некоторые теги
 */

if (!function_exists('textarea')) {
    function textarea($str): string
    {
        if (is_string($str)) {
            $result = strip_tags(nl2br($str), '<code><p><br><br /><br/><b><i><strong>');
            return $result;
        }

        return '';

    }
}


if (!function_exists('usortDate')) {


    function usortDate($data)
    {
        $d = $data;
        if (is_object($data)) {
            $d = $data->toArray();
        }

        /**  Универсальная сортировка по возрастанию дат **/
        usort($d, function ($a, $b) {
            return strtotime($a['created_at']) <=> strtotime($b['created_at']);
        });
        return $d;
    }
}


if (!function_exists('to_object')) {


    function to_object($array)

        /** Преобразуем в объект */
    {

        return array_map(function ($array) {
            return (object)$array;
        }, $array);

    }
}

if (!function_exists('logErrors')) {

    function logErrors($th): void

    {
        if ($th) {
            Log::error("Ошибка в методе: {$th->getFile()} строка: {$th->getLine()} сообщение: {$th->getMessage()}");
        }

    }
}





