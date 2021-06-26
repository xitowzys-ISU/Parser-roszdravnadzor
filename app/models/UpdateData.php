<?php

namespace app\models;

use app\core\Model;
use app\core\Database;

class UpdateData extends Model
{

    protected $database;

    public function __construct()
    {
        $this->database = Database::getInstance();
    }

    /**
     * Get json from a website
     *
     * @return array
     */
    // TODO: Сделать обработчик ошибок
    public function getJSON($data)
    {
        if ($data === NULL)
            $data = require 'app/config/parser.php';

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => BASIC_URL_AJAX,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
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

    /**
     * Check if there are tables in the database
     *
     * @return bool
     */
    // TODO: Сделать обработчик ошибок
    public function checkTableDB()
    {
        $sth = $this->database->prepare("SHOW TABLES");
        $sth->execute();

        if (empty($sth->fetchAll()))
            return false;
        else
            return true;
    }

    /**
     * Create tables for storing data
     *
     * @return void
     */
    // TODO: Сделать обработчик ошибок
    public function createTableDB()
    {
        $this->database->exec(file_get_contents(SQL_DIR . "createTable.sql"));
    }

    /**
     * Number of records per year
     * 
     * @return array
     */
    public function getNumberRecordsYear()
    {

        $result = [];

        $data = require 'app/config/parser.php';

        $IterationCount = (date('Y') - 1990);

        for ($i = $IterationCount; $i > 0; $i--) {
            $dateFrom = date_create((date('Y') - $i) . '-01-01');
            $dateTo = date_create((date('Y') - $i) . '-01-01');

            if($i == 1)
                $dateTo = date_create(date("Y-m-d"));
            else
                date_add($dateTo, date_interval_create_from_date_string('1 year'));

                
            $data['length'] = 1;

            $data['dt_ru_from'] = date_format($dateFrom, 'd.m.Y');
            $data['dt_ru_to'] = date_format($dateTo, 'd.m.Y');

            $result += [date_format($dateFrom, 'Y') => $this->getJSON($data)["recordsFiltered"]];
        }

        debug($result);
        return [];
    }
}
