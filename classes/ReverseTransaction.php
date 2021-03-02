<?php

require_once '../includes/header.php';

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ReverseTransaction
 *
 * @author SEBASTIAN
 */
class ReverseTransaction {
    //put your code here
    
    
    public function reverseCurrentTransaction($pupil_id){
        
        $query = mysql_query("SELECT DISTINCT FROM fee_payments WHERE pupil_id'$pupil_id'");
        
    }
    
}
