<?php

namespace app\models;

use app\core\Model;
use \PDO;
use \PDOException;

class UpdateData extends Model
{
    public function __construct()
    {
    }
    
    public function getJSON()
    {

        $data = require 'app/config/parser.php';
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://roszdravnadzor.gov.ru/ajax/services/misearch?=",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_COOKIE => "uid=4415501648854257000",
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/x-www-form-urlencoded"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }

    }
}
