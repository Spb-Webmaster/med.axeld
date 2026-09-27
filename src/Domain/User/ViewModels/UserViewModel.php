<?php

namespace Domain\User\ViewModels;


use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Domain\User\DTOs\UserUpdateDto;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Support\Traits\Makeable;
use Throwable;

class UserViewModel
{
    use Makeable;

}
