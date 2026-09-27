<?php

namespace Domain\User\DTOs;

use Illuminate\Http\Request;
use Support\Traits\Makeable;

class UserUpdateDto
{
    use Makeable;

    /** Список полей, которые будем сохранять **/
    const FIELDS = [
        'username', 'email', 'about_me', 'date_birthday',  'address', 'telegram', 'max', 'vk', 'website', 'published'
    ];

    public function __construct(
        public readonly ?string $username = null,
        public readonly ?string $email = null,
        public readonly ?string $about_me = null,
        public readonly ?string $date_birthday = null,
        public readonly ?array $address = null,
        public readonly ?string $telegram = null,
        public readonly ?string $max = null,
        public readonly ?string $vk = null,
        public readonly ?string $website = null,
        public  ?int $published = 1,

    )
    {

    }

    public static function formRequest(Request $request):UserUpdateDto
    {
        return self::make( ... $request->only(self::FIELDS));

    }


    /** Формирование массива нужных полей **/
    public function toArray(): array
    {
        $result = [];
        foreach (self::FIELDS as $field) {
            if (isset($this->$field)) {
                $result[$field] = $this->$field;
            }
        }
        return $result;
    }
}
