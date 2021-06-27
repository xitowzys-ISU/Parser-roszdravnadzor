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
            set_time_limit(6000);
            $this->from = $dates_array['date-from'] ?? '1990-01-01';
            $this->to = $dates_array['date-from'] ?? strval(date("Y-m-d"));

            //TODO erase it
//            $this->to = '2005-01-01';
    }

    public function countRows($start, $end){
        $sth = $this->database->prepare("SELECT COUNT(*) as 'count' FROM `medical_products` WHERE (`validity_period` BETWEEN :start AND :end);");
        $sth->bindParam(':start', $start);
        $sth->bindParam(':end', $end);
        $sth->execute();
        $ans = $sth->fetchAll(PDO::FETCH_ASSOC);
        return intval($ans[0]['count']);
    }


    public function getDataFromDB($start, $end)
    {
        $sth = $this->database->prepare("SELECT `". implode("`, `", $this->attributes) . "` FROM `medical_products` WHERE (`validity_period` BETWEEN :start AND :end);");
        $sth->bindParam(':start', $start);
        $sth->bindParam(':end', $end);
        $sth->execute();

        $ans = $sth->fetchAll(PDO::FETCH_ASSOC);
        $this->db_data = $ans;
//        var_dump($this->db_data);
    }


    public function generateJSON($filename)
    {
        $start_year = date("Y", strtotime($this->from));
        $end_year = date("Y", strtotime($this->to));
        $file_json = fopen($filename. ".json", "w");
        fwrite($file_json, '{"data" : [');

        if ($start_year == $end_year)
        {
            $this->getDataFromDB($this->from, $this->to);
            fwrite($file_json, json_encode($this->db_data));
            $this->data_length = count($this->db_data);
        }
        else
        {
            $comma_flag = false;

            //first year
            $this->getDataFromDB($this->from, $start_year . '-12-31');
            if (count($this->db_data) > 0)
                $comma_flag = true;
            $this->data_length += count($this->db_data);
            for ($i = 0; $i < count($this->db_data); $i++)
            {
                fwrite($file_json, json_encode($this->db_data[$i]));
                if ($i != count($this->db_data) - 1) {
                    fwrite($file_json, ', ');
                }
            }

            //middle years
            for ($i = 0; $i < $end_year - $start_year - 1; $i++)
            {
                $this->getDataFromDB($start_year+$i+1 . '-01-01', $start_year+$i+1 . '-12-31');
                $this->data_length += count($this->db_data);
                if ($comma_flag and count($this->db_data)) {
                    $comma_flag = true;
                    fwrite($file_json, ', ');
                }

                for ($i = 0; $i < count($this->db_data); $i++)
                {
                    fwrite($file_json, json_encode($this->db_data[$i]));
                    if ($i != count($this->db_data) - 1) {
                        fwrite($file_json, ', ');
                    }
                }
            }

//            last year
            $this->getDataFromDB($end_year . '-01-01', $this->to);
            if ($comma_flag and count($this->db_data) > 0)
                fwrite($file_json, ', ');
            $this->data_length += count($this->db_data);

            for ($i = 0; $i < count($this->db_data); $i++)
            {
                fwrite($file_json, json_encode($this->db_data[$i]));
                if ($i != count($this->db_data) - 1) {
                    fwrite($file_json, ', ');
                }
            }
        }

        fwrite($file_json, ']');
        fwrite($file_json, ',');
//        json-array len
        fwrite($file_json, '"length": ');
        fwrite($file_json, $this->data_length);
//        json end
        fwrite($file_json, '}');


    }

    public function getJSON(): string
    {
        return $this->json;
    }

}