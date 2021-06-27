CREATE TABLE `medical_products`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `registry_entry_id` varchar(255) NULL COMMENT 'Уникальный номер реестровой записи',
  `registration_number` varchar(255) NULL COMMENT 'Регистрационный номер медицинского изделия',
  `validity_period` date NULL COMMENT 'Дата государственной регистрации медицинского изделия',
  `registration_validity_period` date NULL COMMENT 'Срок действия регистрационного удостоверения',
  `registration_validity_period_indefinitely` tinyint NULL COMMENT 'Срок действия регистрационного удостоверения (Бессрочно)',
  `name` mediumtext NULL COMMENT 'Наименование медицинского изделия',
  `applicant_organization` varchar(255) NULL COMMENT 'Наименование организации-заявителя медицинского изделия',
  `applicant_location` varchar(255) NULL COMMENT 'Место нахождения организации-заявителя медицинского изделия',
  `applicant_legal_address` varchar(255) NULL COMMENT 'Юридический адрес организации-заявителя медицинского изделия',
  `manufacturing_organization` varchar(255) NULL COMMENT 'Наименование организации-производителя медицинского\nизделия или организации-изготовителя медицинского изделия',
  `manufacturer_location` varchar(255) NULL COMMENT 'Место нахождения организации-производителя медицинского\nизделия или организации - изготовителя медицинского изделия',
  `manufacturer_legal_address` varchar(255) NULL COMMENT 'Юридический адрес организации-производителя медицинского\nизделия или организации - изготовителя медицинского изделия',
  `product_classification` varchar(20) NULL COMMENT 'ОКП/ОКПД2',
  `risk_level` varchar(5) NULL COMMENT 'Класс потенциального риска применения медицинского изделия в соответствии с номенклатурной классификацией медицинских изделий, утверждаемой Министерством здравоохранения Российской Федерации',
  `purpose` text NULL COMMENT 'Назначение медицинского изделия, установленное производителем',
  `product_type` varchar(255) NULL COMMENT 'Вид медицинского изделия в соответствии с номенклатурной классификацией медицинских изделий, утверждаемой Министерством здравоохранения Российской Федерации',
  `production_address` varchar(255) NULL COMMENT 'Адрес места производства или изготовления медицинского изделия',
  `analogs` text NULL COMMENT 'Сведения о взаимозаменяемых медицинских изделиях',
  `is_exist` tinyint NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `registry_entry_id_unique`(`registry_entry_id`) USING BTREE,
  INDEX `registration_number_index`(`registration_number`) USING BTREE
);

CREATE TABLE `products_log`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `added_at` timestamp(0) NULL,
  `added_quantity` int NULL,
  PRIMARY KEY (`id`)
);

