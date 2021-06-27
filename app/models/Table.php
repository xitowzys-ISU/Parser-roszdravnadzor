<?php


namespace app\models;

use app\core\Model;
use app\core\Database;
use PDO;

class Table extends Model
{

    protected $database;
    protected array $data;
    protected array $db_data;
    protected int $data_length = 0;
    protected string $json = '';
    protected string $from = '1990-01-01';
    protected string $to;
    protected array $attributes = ['registry_entry_id', 'registration_number', 'validity_period', 'registration_validity_period', 'registration_validity_period_indefinitely',
        'name', 'applicant_organization', 'applicant_location', 'applicant_legal_address', 'manufacturing_organization',
        'manufacturer_location', 'manufacturer_legal_address', 'product_classification', 'risk_level', 'purpose',
        'product_type', 'production_address', 'analogs'];
    public function __construct()
    {
        $this->database = Database::getInstance();
    }

    public function setDate($dates_array =[])
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

    public function countRows($start, $end){
        $sth = $this->database->prepare("SELECT COUNT(*) as 'count' FROM `medical_products` WHERE (`validity_period` BETWEEN :start AND :end);");
        $sth->bindParam(':start', $start);
        $sth->bindParam(':end', $end);
        $sth->execute();
        $ans = $sth->fetchAll(PDO::FETCH_ASSOC);
        return intval($ans[0]['count']);
    }


    public function getDataFromDB($from, $to, $batch_size, $offset)
    {
        $sth = $this->database->prepare("SELECT `". implode("`, `", $this->attributes) . "` FROM `medical_products` WHERE (`validity_period` BETWEEN :from AND :to) LIMIT " . $batch_size . " OFFSET " . $offset);
        $sth->bindParam(':from', $from);
        $sth->bindParam(':to', $to);
        $sth->execute();

        $ans = $sth->fetchAll(PDO::FETCH_ASSOC);
        $this->db_data = $ans;
    }


    public function generateJSON($filename)
    {
        $batch = 2;
        $rows = $this->countRows($this->from, $this->to);
        $file_json = fopen($filename. ".json", "w");
        fwrite($file_json, '{"data" : [');

        for ($i = 0; $i < intdiv($rows + $batch - 1,  $batch); $i++)
        {
            $this->getDataFromDB($this->from, $this->to, $batch, $batch * $i);
            for ($j = 0; $j < count($this->db_data); $j++)
            {
                fwrite($file_json, json_encode($this->db_data[$j]));
                if ($i != intdiv($rows + $batch - 1,  $batch) -1  or $j != count($this->db_data) -1) {
                    fwrite($file_json,', ');
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