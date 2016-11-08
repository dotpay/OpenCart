<?php

class ModelPaymentDotpayOc extends Model
{
    const TABLE = 'dotpay_on_cards';
    
    /**
     * Gets all cards for customer
     * @param int $userId
     * @param boolean $empty
     * @return array
     */
    public function getAllCardsForCustomer($userId, $empty = false) {
        $not = ($empty) ? '' : 'NOT';
        $cards = $this->db->query('
            SELECT *
            FROM `'.DB_PREFIX.self::TABLE.'` 
            WHERE customer_id = '.(int)$userId.' 
            AND 
            card_id IS '.$not.' NULL
        ');
        return $cards->rows;
    }
    
    /**
     * Returns credit card for selected order
     * @param type $order Order Id
     * @return array|null
     */
    public function getCreditCardByOrder($order) {
        $card = $this->db->query('
            SELECT *
            FROM `'.DB_PREFIX.self::TABLE.'` 
            WHERE order_id = '.(int)$order
        );
        if(!count($card->rows))
            return NULL;
        return $card->rows[0];
    }
    
    /**
     * Returns card data fot the given card id
     * @param int $id card id
     * @return type
     */
    public function getCardById($id) {
        $card = $this->db->query('
            SELECT * 
            FROM `'.DB_PREFIX.self::TABLE.'` 
            WHERE cc_id = '.(int)$id
        );
        if(!count($card->rows))
            return NULL;
        return $card->rows[0];
    }
    
    /**
     * Adds card to database and return card hash
     * @param int $customerId customer id
     * @param int $orderId order id
     * @return string
     */
    public function addCard($customerId, $orderId) {
        $existedCard = $this->db->query( 'SELECT * FROM `'.DB_PREFIX.self::TABLE.'` WHERE customer_id = '.(int)$customerId.' AND order_id = '.(int)$orderId);
        if(empty($existedCard->rows[0])) {
            $hash = $this->generateCardHash();
            $this->db->query( 
                'INSERT INTO `'.DB_PREFIX.self::TABLE.'`(customer_id, order_id, hash) VALUES('.(int)$customerId.','.(int)$orderId.',\''.$hash.'\')'
            );
        } else {
            $hash = $existedCard->rows[0]['hash'];
        }
        return $hash;
    }
    
    /**
     * Adds additional info to saved card
     * @param int $id card id
     * @param string $cardId card identifier from Dotpay
     * @param string $mask card mask name
     * @param string $brand card brand
     */
    public function updateCard($id, $cardId, $mask, $brand) {
        $this->db->query( 
            'UPDATE`'.DB_PREFIX.self::TABLE.'`
                SET 
                    card_id = \''.$cardId.'\',
                    mask = \''.$mask.'\',
                    brand = \''.$brand.'\',
                    register_date = \''.date('Y-m-d').'\'
                WHERE 
                    cc_id = '.(int)$id
        );
    }
    
    /**
     * Deletes all cards for customer
     * @param int $userId
     * @return boolean
     */
    public function deleteAllCardsForCustomer($userId) {
        $result = $this->db->query('
            DELETE
            FROM `'.DB_PREFIX.self::TABLE.'` 
            WHERE `customer_id` = '.(int)$userId
        );
        return $result;
    }
    
    /**
     * Deletes card for id
     * @param int $cardId
     * @return boolean
     */
    public function deleteCardForId($cardId) {
        $result = $this->db->query('
            DELETE
            FROM `'.DB_PREFIX.self::TABLE.'` 
            WHERE `cc_id` = '.(int)$cardId
        );
        return $result;
    }
    
    /**
     * Deletes all cards for non existing customers
     * @return boolean
     */
    public function deleteAllCardsForNonExistingCustomers() {
        return $this->db->query('
            DELETE 
            FROM `'.DB_PREFIX.self::TABLE.'` 
            WHERE customer_id NOT IN (
                SELECT id_customer 
                FROM `'.DB_PREFIX.self::TABLE.'`
            )
        ');
    }
    
    /**
     * Generates card hash for OneClick
     * @return string
     */
    private function generateCardHash() {
        $microtime = '' . microtime(true);
        $md5 = md5($microtime);

        $mtRand = mt_rand(0, 11);

        $md5Substr = substr($md5, $mtRand, 21);

        $a = substr($md5Substr, 0, 6);
        $b = substr($md5Substr, 6, 5);
        $c = substr($md5Substr, 11, 6);
        $d = substr($md5Substr, 17, 4);

        return "{$a}-{$b}-{$c}-{$d}";
    }
    
    /**
     * Checks, if generated card hash is unique
     * @return string|boolean
     */
    private function getUniqueCardHash() {
        $count = 200;
        $result = false;
        do {
            $cardHash = $this->generateCardHash();
            $test = Db::getInstance()->ExecuteS('
                SELECT count(*) as count  
                FROM `'._DB_PREFIX_.self::$definition['table'].'` 
                WHERE hash = \''.$cardHash.'\'
            ');
            
            if ($test[0]['count'] == 0) {
                $result = $cardHash;
                break;
            }

            $count--;
        } while ($count);
        
        return $result;
    }
}

?>