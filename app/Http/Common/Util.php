<?php

namespace App\Http\Common;
use Exception;

class Util {

    public static function response($response, $record)
    {
        $data = $record;
        return $response->json(['resultKey' => $data['status'], 'resultValue' => $data['result'], 'errorCode' => $data['errorcode'], 'defaultError' => $data['defaultError']], 200);
    }
}