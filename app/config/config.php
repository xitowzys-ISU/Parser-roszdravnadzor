<?php

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'parser_roszdravnadzor');
define('DB_USER', 'raidemot');
define('DB_PASS', 'PASSWORD');

/**
 * Root path to the application
 * 
 * @var string
 */
define('ROOT', $_SERVER['DOCUMENT_ROOT'] . '/');

/**
 * Path to sql files
 * 
 * @var string
 */
define('SQL_DIR', ROOT . 'app/sql/');

/**
 * Path to templates
 * 
 * @var string
 */
define('TEMPLATES_DIR', 'templates/');

/**
 * Application Template
 * 
 * @var string
 */
define('TEMPLATE', 'Default/');

/**
 * Link to the site that will parse
 * 
 * @var string
 */
define('BASIC_URL_AJAX', 'https://roszdravnadzor.gov.ru/ajax/services/misearch?=');
