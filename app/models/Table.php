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

            $this->from = $dates_array['date-from'] ?? '1990-01-01';
            $this->to = $dates_array['date-from'] ?? strval(date("Y-m-d"));
    }

    public function getDataFromDB()
    {

        $sth = $this->database->prepare("SELECT `". implode("`, `", $this->attributes) . "` FROM `medical_products` WHERE (`validity_period` BETWEEN :from AND :to);");
        $sth->bindParam(':from', $this->from);
        $sth->bindParam(':to', $this->to);
        $sth->execute();

        $ans = $sth->fetchAll(PDO::FETCH_ASSOC);
        $this->db_data = $ans;
//        var_dump($this->db_data);
    }

    public function splitTimeIntervals() {
        $start = date("Y", strtotime($this->from));
        $end = date("Y", strtotime($this->to));
        echo $this->from;
        echo "<br>";
        echo "$this->to";
        echo "<br><br>";

        echo $start;
        echo "<br>";
        echo $end;
        echo "<br>";
        echo $end - $start;




    }

    public function generateJSON($filename)
    {
        $start = date("Y", strtotime($this->from));
        $end = date("Y", strtotime($this->to));

        $file_json = fopen($filename. ".json", "w");
//        array start
        fwrite($file_json, '{"data" : ');
//        data
        fwrite($file_json, json_encode($this->db_data));
        fwrite($file_json, ',');
//        array len
        fwrite($file_json, '"length": ');
        fwrite($file_json, count($this->db_data));
//        json end
        fwrite($file_json, '}');
    }

    public function getJSON(): string
    {
        return $this->json;
    }

}