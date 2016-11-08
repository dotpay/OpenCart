<?php

/**
 * Model of information about ending of cash of transfer payments
 */
class ModelPaymentDotpayInfo extends Model
{
    /**
     * Name of table in database
     */
    const TABLE = 'dotpay_instructions';
    
    /**
     * Installs model and modifies database structure
     * @param type $db Database driver from OpencCart
     * @return type
     */
    public static function install($db) {
        return $db->query('
            CREATE TABLE IF NOT EXISTS `'.DB_PREFIX.self::TABLE.'` (
                `instruction_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `order_id` INT UNSIGNED NOT NULL,
                `number` varchar(64) NOT NULL,
                `hash` varchar(128) NOT NULL,
                `bank_account` VARCHAR(64),
                `is_cash` int(1) NOT NULL,
                `amount` decimal(10,2) NOT NULL,
                `currency` varchar(3) NOT NULL,
                `channel` INT UNSIGNED NOT NULL,
                PRIMARY KEY (`instruction_id`)
            ) DEFAULT CHARSET=utf8;'
        );
    }
    
    /**
     * Uninstalls model and clears database structure
     * @param type $db Database driver from OpencCart
     * @return type
     */
    public static function uninstall($db) {
        return $db->query(
            'DROP TABLE IF EXISTS `'.DB_PREFIX.self::TABLE.';'
        );
    }
}

?>