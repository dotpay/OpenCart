<?php

/**
 * Model of credit cards, saved in shop for One Click cards payment.
 */
class ModelExtensionPaymentDotpayOc extends Model
{
    /**
     * Name of table in database.
     */
    const TABLE = 'dotpay_on_cards';

    /**
     * Installs model and modifies database structure.
     *
     * @param type $db Database driver from OpencCart
     *
     * @return type
     */
    public static function install($db)
    {
        return $db->query(
            'CREATE TABLE IF NOT EXISTS `'.DB_PREFIX.self::TABLE.'` (
                `cc_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `order_id` INT UNSIGNED NOT NULL,
                `customer_id` INT(11) NOT NULL,
                `mask` varchar(20) DEFAULT NULL,
                `brand` varchar(20) DEFAULT NULL,
                `hash` varchar(100) NOT NULL,
                `card_id` VARCHAR(128) DEFAULT NULL,
                `register_date` DATE DEFAULT NULL,
                PRIMARY KEY (`cc_id`),
                UNIQUE KEY `hash` (`hash`),
                UNIQUE KEY `cc_order` (`order_id`),
                UNIQUE KEY `card_id` (`card_id`),
                KEY `customer_id` (`customer_id`)
            ) DEFAULT CHARSET=utf8;'
        );
    }

    /**
     * Uninstalls model and clears database structure.
     *
     * @param type $db Database driver from OpencCart
     *
     * @return type
     */
    public static function uninstall($db)
    {
        return $db->query(
            'DROP TABLE IF EXISTS `'.DB_PREFIX.self::TABLE.'`;'
        );
    }

    /**
     * Gets all cards.
     *
     * @param bool $empty
     *
     * @return array
     */
    public function getAllCards($empty = false)
    {
        $not = ($empty) ? '' : 'NOT';
        $cards = $this->db->query('
            SELECT oc.*, us.firstname, us.lastname, us.email
            FROM `'.DB_PREFIX.self::TABLE.'` oc
            JOIN `'.DB_PREFIX.'user` us ON customer_id = user_id
            WHERE
                card_id IS '.$not.' NULL
        ');

        return $cards->rows;
    }

    /**
     * Deletes card for id.
     *
     * @param int $cardId
     *
     * @return bool
     */
    public function deleteCardForId($cardId)
    {
        $result = $this->db->query('
            DELETE
            FROM `'.DB_PREFIX.self::TABLE.'` 
            WHERE `cc_id` = '.(int) $cardId
        );

        return $result;
    }
}
