<?php

namespace app\models;

use app\core\Model;

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
            CURLOPT_URL => BASIC_URL_AJAX,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/x-www-form-urlencoded",
                'Accept: application/json'
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return json_decode($response, true);
        }

    }
}
