<?php

namespace Support\Traits;

use App\Models\UserToken;
use Illuminate\Support\Str;

trait CreatorToken
{

    public function save_token($id):string | bool
    {
        $hash = $this->token();

        $user_token = new UserToken;
            $user_token->token = $hash;
            $user_token->user_id = $id;
            $result = $user_token->save();

            if ($result) {
                return $hash;
            }

        return false;

    }

    protected function token():string
    {
        return md5(Str::random(5));
    }
}
