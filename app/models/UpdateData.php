<?php

namespace app\models;

use app\core\Model;
use app\core\Database;
use \PDOException;
use \DateTime;
use \DatePeriod;
use \DateInterval;

require 'app/lib/CLIProgressBar.php';

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
            // CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 1000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_NONE,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/x-www-form-urlencoded"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return -1;
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
     * Number of records for the period
     * 
     * @return array
     */
    public function getNumberRecordsPeriod(string $dateFrom, string $dateTo)
    {

        $result = [];

        $data = require 'app/config/parser.php';

        $dateFrom = date_create($dateFrom);
        $dateTo = date_create($dateTo);

        $interval = $dateTo->diff($dateFrom);

        if ($interval->y > 1 || ($interval->y === 1 && ($interval->d >= 1 || $interval->m >= 1))) {

            $dates = [];

            $years  = intval($dateTo->format('Y')) - intval($dateFrom->format('Y'));

            $period = new DatePeriod($dateFrom, new DateInterval('P1Y'), $years);

            foreach ($period as $date) {
                array_push($dates, $date->format('d.m.Y'));
            }

            for ($i = 0; $i < count($dates); $i++) {

                if (count($dates) - 1 === $i) {
                    if (!($interval->d >= 1 || $interval->m >= 1))
                        continue;

                    $result += [$dates[$i] . '-' . $dateTo->format('d.m.Y') => NULL];
                } else
                    $result += [$dates[$i] . '-' . $dates[$i + 1] => NULL];
            }
        } else {
            $result += [$dateFrom->format('d.m.Y') . '-' . $dateTo->format('d.m.Y') => NULL];
        }

        foreach ($result as $key => $value)
        {
            $dates = explode("-", $key);
            $data['length'] = 1;

            $data['dt_ru_from'] = $dates[0];
            $data['dt_ru_to'] = $dates[1];

            $json = $this->getJSON($data)["recordsFiltered"];

            // * Prevents getting incomplete data
            if ($json === NULL) {
                continue;
            }

            $result[$key] = $this->getJSON($data)["recordsFiltered"];
        }

        return $result;
    }

    /**
     * Get data for a certain period
     *
     * @param string $period
     * @param integer $start
     * @return array
     */
    public function getData($period, $start = 0)
    {
        $data = require 'app/config/parser.php';

        $data['start'] = $start;

        $date = explode('-', $period);

        $data['dt_ru_from'] = $date[0];
        $data['dt_ru_to'] = $date[1];

        return $this->getJSON($data);
    }

    /**
     * Save data to the database
     *
     * @param array $data
     * @return bool
     */
    protected function saveDataInDB(array $data)
    {
        foreach ($data['data'] as $key => $value) {

            $isDateRegistrationValidityPeriod = false;

            if ((!preg_match('/^[0-9]{1,2}.[0-9]{1,2}.[0-9]{4}$/', $value['col4']['label'])))
                if ($value['col4']['label'] != NULL)
                    $isDateRegistrationValidityPeriod = true;

            $params = [
                ':registry_entry_id' => $value['col1']['label'],
                ':registration_number' => $value['col2']['label'],
                ':validity_period' => date_format(date_create_from_format('d.m.Y', $value['col3']['label']), 'Y-m-d'),
                ':registration_validity_period' => $value['col4']['label'] === NULL || $isDateRegistrationValidityPeriod ? null : date_format(date_create_from_format('d.m.Y', $value['col4']['label']), 'Y-m-d'),
                ':registration_validity_period_other' => $isDateRegistrationValidityPeriod ? $value['col4']['label'] : null,
                ':name' => array_key_exists('title', $value['col5']) ? $value['col5']['title'] : $value['col5']['label'],
                ':applicant_organization' => $value['col6']['label'],
                ':applicant_location' => $value['col7']['label'],
                ':applicant_legal_address' => $value['col8']['label'],
                ':manufacturing_organization' => $value['col9']['label'],
                ':manufacturer_location' => $value['col10']['label'],
                ':manufacturer_legal_address' => $value['col11']['label'],
                ':product_classification' => $value['col12']['label'],
                ':risk_level' => $value['col13']['label'],
                ':purpose' => $value['col14']['label'],
                ':product_type' => $value['col15']['label'],
                ':production_address' => $value['col16']['label'],
                ':analogs' => $value['col17']['label'],
                ':is_exist' => 0,
            ];

            try {
                $this->database->prepare(file_get_contents(SQL_DIR . "insertData.sql"))->execute($params);
            } catch (PDOException $e) {
                echo 'Не удалось добавить данные!<br />Причина: ' . $e->getMessage() . '<br>';
                echo '<pre>';
                var_dump($value);
                echo '</pre><br>';
                echo '<pre>';
                var_dump($params);
                echo '</pre><br>';
                return false;
            }
        }

        return true;
    }

    /**
     * Deleting duplicate rows by a unique registry number
     *
     * @param array $data
     * @return void
     */
    protected function addUniqueIndex()
    {
       $this->database->exec('ALTER IGNORE TABLE `medical_products` ADD UNIQUE INDEX(registry_entry_id);');
    }

    public function updateDataWeek()
    {
        $this->database->exec('ALTER TABLE `medical_products` DROP INDEX registry_entry_id;');
        $amountData = $this->getNumberRecordsPeriod(date("Y-m-d", strtotime("-1 week")), date("Y-m-d"));
        $this->saveData($amountData);
        $this->addUniqueIndex();


//        $this->deleteDuplicateRows();
//        $total = 4535;
//        for ($i = 0; $i < $total; $i++) {
//            $percentage = $i / $total * 100;
//            showProgressBar($percentage, 2);
//        }
//
//        print PHP_EOL;
//        print "done!" . PHP_EOL;
    }

    public function saveData ($amountData)
    {
        $data = require 'app/config/parser.php';

        foreach ($amountData as $key => $value) {
            $numberPages = intval(ceil($value / $data['length']));

            for ($i = 0; $i < $numberPages; $i++) {
                if ($i === 0)
                    $json = $this->getData($key);
                else
                    $json = $this->getData($key, $data['length'] * $i);


                if ($json === -1) {
                    sleep(30);
                    --$i;
                    continue;
                }

                if (array_key_exists('message', $json['data'])) {
                    sleep(30);
                    --$i;
                    continue;
                }

                $this->saveDataInDB($json);
            }
        }
    }
}
