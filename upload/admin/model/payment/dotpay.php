<?php
class ModelPaymentDotpay extends Model {
	public function install() {
		$this->db->query("
			CREATE TABLE `" . DB_PREFIX . "order_amazon` (
				`order_id` int(11) NOT NULL,
				`amazon_order_id` varchar(255) NOT NULL,
				`free_shipping`  tinyint NOT NULL DEFAULT 0,
				KEY `amazon_order_id` (`amazon_order_id`),
				PRIMARY KEY `order_id` (`order_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");

		$this->db->query("
			CREATE TABLE `" . DB_PREFIX . "order_amazon_product` (
			`order_product_id`  int NOT NULL ,
			`amazon_order_item_code`  varchar(255) NOT NULL,
			PRIMARY KEY (`order_product_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");

		$this->db->query("
			CREATE TABLE `" . DB_PREFIX . "order_amazon_report` (
				`order_id`  int NOT NULL ,
				`submission_id`  varchar(255) NOT NULL ,
				`status` enum('processing','error','success') NOT NULL ,
				`text`  text NOT NULL,
				PRIMARY KEY (`submission_id`),
				INDEX (`order_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");

		$this->db->query("
			CREATE TABLE `" . DB_PREFIX . "order_total_tax` (
				`order_total_id`  INT,
				`code` VARCHAR(255),
				`tax` DECIMAL(10, 4) NOT NULL,
				PRIMARY KEY (`order_total_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");
	}

	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "order_amazon`;");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "order_amazon_product`;");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "order_amazon_report`;");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "order_total_tax`;");
	}

}