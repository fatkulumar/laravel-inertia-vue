<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function createUUID36()
    {
        return substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 36);
    }
}
