<?php

namespace Pterodactyl\Services;

use Illuminate\Support\Facades\DB;
use Pterodactyl\Models\Error;

class ErrorService
{
    public Error $error;

    public function __construct(Error $error)
    {
        $this->error = $error;
    }

    /**
     * @param string $error_str
     * @return string
     */
    public function create(string $error_str): string
    {
        $error = $this->error;
        $error->code = 'ERR-'.$this->genString(8);
        $error->error = $error_str;
        DB::table('errors')->insert($error->getAttributes());
        return $error->code;
    }

    /**
     * @param int $length
     * @return string
     */
    public function genString(int $length): string
    {
        $chars = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        return substr(str_shuffle($chars), 0, $length);
    }
}
