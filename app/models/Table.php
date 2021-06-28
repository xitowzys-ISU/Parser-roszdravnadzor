<?php


namespace app\models;

use app\core\Model;
use app\core\Database;
use PDO;

class Table extends Model
{
    protected $database;
    protected $json = '';
    protected $from = '1990-01-01';
    protected $to = '2100-01-01';
    protected $file_name = 'file';
    protected $batch_size = 11;
    protected $attributes = [
        'registry_entry_id',
        'registration_number',
        'validity_period',
        'registration_validity_period',
        'registration_validity_period_indefinitely',
        'name',
        'applicant_organization',
        'applicant_location',
        'applicant_legal_address',
        'manufacturing_organization',
        'manufacturer_location',
        'manufacturer_legal_address',
        'product_classification',
        'risk_level',
        'purpose',
        'product_type',
        'production_address',
        'analogs'
    ];
    
    public function __construct()
    {
        $this->database = Database::getInstance();
    }

    public function setName($file_name)
    {
        /*
         * default $this->file_name = 'file'
         */
        $this->file_name = $file_name;
    }

    public function setDate($dates_array = [])
    {
        /*
         * функция принимает опциональный массив.
         * В нем могут содержаться ключи 'date-from' и 'date-to',
         * если не содержится -> берется крайнее значение.
         * Формат записи YYYY-MM-DD.
         */

        //TODO test for time limit
        set_time_limit(600);
        $this->from = $dates_array['date-from'] ?? '1990-01-01';
        $this->to = $dates_array['date-to'] ?? strval(date("Y-m-d"));
    }


    public function countRows()
    {
        $sth = $this->database->prepare("SELECT COUNT(*) as 'count' FROM `medical_products` WHERE (`validity_period` BETWEEN :from AND :to);");
        $sth->bindParam(':from', $this->from);
        $sth->bindParam(':to', $this->to);
        $sth->execute();
        $ans = $sth->fetchAll(PDO::FETCH_ASSOC);
        return intval($ans[0]['count']);
    }


    public function getDataFromDB($offset)
    {
        $sth = $this->database->prepare("SELECT `" . implode("`, `", $this->attributes) . "` FROM `medical_products` WHERE (`validity_period` BETWEEN :from AND :to) LIMIT " . $this->batch_size . " OFFSET " . $offset);
        $sth->bindParam(':from', $this->from);
        $sth->bindParam(':to', $this->to);
        $sth->execute();

        $ans = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $ans;
    }


    public function generateJSON()
    {

        $rows = $this->countRows();
        $file_json = fopen($this->file_name . ".json", "w");
        $array_iterations = intdiv($rows + $this->batch_size - 1,  $this->batch_size);
        fwrite($file_json, '{"data" : [');
        for ($i = 0; $i < $array_iterations; $i++) {
            $data = $this->getDataFromDB($this->batch_size * $i);
            for ($j = 0; $j < count($data); $j++) {
                fwrite($file_json, json_encode($data[$j]));
                if ($i != $array_iterations - 1  or $j != count($data) - 1) {
                    fwrite($file_json, ', ');
                }
            }
        }
        fwrite($file_json, ']');
        fwrite($file_json, ',');
        //        json-array len
        fwrite($file_json, '"length": ');
        fwrite($file_json, $rows);
        //        json end
        fwrite($file_json, '}');
    }


    public function getJSON(): string
    {
        /*
         * НЕ РАБОТАЕТ!
         */
        return $this->json;
    }
}
