<?php

require_once '../includes/header.php';
require_once '../classes/AcademicTerm.php';
require_once '../classes/User.php';
require_once '../classes/ClassMembership.php';
require_once '../classes/Bill.php';
require_once '../classes/Admission.php';

class Payments {
    
    

    public function insertFeePayments() {
        global $connection;

        $user = new User();
        $academicTerm = new AcademicTerm();
        $bill = new Bill();
        
               $payments = new Payments();


        $pupil_id = trim(escape_value($_POST["pupil_id"]));
        $school_number = trim(escape_value($_POST["school_number"]));
        //HERE WE GET AMOUNT PAYING
        $amount = (double) trim(escape_value($_POST["amount"]));
        $mode_of_payment = trim(escape_value($_POST["mode_of_payment"]));
        $mode_of_payment_number = trim(escape_value($_POST["mode_of_payment_number"]));
        $fees_paid_by = trim(ucwords(escape_value($_POST["fees_paid_by"])));
        $receipt_no = generate_receipt_numbers();

        $getUser = $user->getUserByUsername($_SESSION["username"]);
        $name = $getUser["other_names"] . " " . $getUser["family_name"];

        $getAcademicTerm = $academicTerm->getActivatedTerm();
        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

        $getLedgerTotals = $bill->getLedgerTotals($pupil_id);
        $getAllAmountDebited = $getLedgerTotals["ledgerFeeAmount"];

        $getTotaFeesPaid = $this->getAllFeesPaid($pupil_id);
        $getAllTotalFeesPaid = $getTotaFeesPaid["allFeesPaid"];

        $getPTAPayable = $bill->getPTAFeesAll($pupil_id);
        $getPTAFeesAll = $getPTAPayable["ptaFeesAll"];
        
        
        $getPTAPaid = $bill->getPTAFeesPaid($pupil_id);
            $get_PTAPaid = $getPTAPaid["ptaFeesPaid"];
      
     
        $total_amount_on_bill = $getAllAmountDebited + $getPTAFeesAll;
        
        
        //HERE IM SETTING THE AMOUNT,IS MY FOCUS
        
        if($amount){
            
            
            if($amount==$total_amount_on_bill){
                $full_fees_paid = $getAllAmountDebited;
                $full_pta_paid = $amount - $full_fees_paid;
                
                
                $query = "INSERT INTO fee_payments (";
                $query .= "pupil_id, school_number, amount, receipt_no, mode_of_payment, mode_of_payment_number, date, time, fees_paid_by, fees_received_by, academic_term";
                $query .= ") VALUES (";
                $query .= "'{$pupil_id}', '{$school_number}', '$amount', '$receipt_no', '{$mode_of_payment}', '{$mode_of_payment_number}', NOW(), NOW(), '{$fees_paid_by}', '{$name}', '{$get_academic_term}'";
                $query .= ")";

                $query_result = mysqli_query($connection, $query) or die(mysql_error());
                
                
                   $query2 = "INSERT INTO pta_fee_payments (";
                    $query2 .= "school_number, pupil_id, amount,time, date,academic_term, entered_by";
                    $query2 .= ") VALUES (";
                    $query2 .= "'{$school_number}', '{$pupil_id}', '$full_pta_paid', NOW(), NOW(), '{$get_academic_term}', '{$name}'";
                    $query2 .= ")";

                    $query_result2 = mysqli_query($connection, $query2); 
                    
                    
                    $query3 = "INSERT INTO student_ledger_payments (";
                    $query3 .= "school_number, pupil_id, amount,time, date,academic_term, entered_by";
                    $query3 .= ") VALUES (";
                    $query3 .= "'{$school_number}', '{$pupil_id}', '$full_fees_paid', NOW(), NOW(), '{$get_academic_term}', '{$name}'";
                    $query3 .= ")";

                    $query_result3 = mysqli_query($connection, $query3);     
                
                    redirect_to("ledger_on.php");
                
                
            }elseif ($amount < $total_amount_on_bill) {
                
                $check_previous_payment = $getAllTotalFeesPaid;
                
                $accumulated_payments  = $check_previous_payment + $amount;
                
                
                
                 $query = "INSERT INTO fee_payments (";
                $query .= "pupil_id, school_number, amount, receipt_no, mode_of_payment, mode_of_payment_number, date, time, fees_paid_by, fees_received_by, academic_term";
                $query .= ") VALUES (";
                $query .= "'{$pupil_id}', '{$school_number}', '$amount', '$receipt_no', '{$mode_of_payment}', '{$mode_of_payment_number}', NOW(), NOW(), '{$fees_paid_by}', '{$name}', '{$get_academic_term}'";
                $query .= ")";

                $query_result = mysqli_query($connection, $query) or die(mysql_error());
                
                 if($check_previous_payment=="" && $amount <= $getAllAmountDebited){
                      $query4 = "INSERT INTO student_ledger_payments (";
                    $query4 .= "school_number, pupil_id, amount,time, date,academic_term, entered_by";
                    $query4 .= ") VALUES (";
                    $query4 .= "'{$school_number}', '{$pupil_id}', '$amount', NOW(), NOW(), '{$get_academic_term}', '{$name}'";
                    $query4 .= ")";

                    $query_result3 = mysqli_query($connection, $query4);    
                 } elseif ($check_previous_payment=="" & $amount > $getAllAmountDebited) {
                      
                       $differnece_for_pta_on_first_pay = $amount - $getAllAmountDebited;
                     
                       $query4i = "INSERT INTO student_ledger_payments (";
                    $query4i .= "school_number, pupil_id, amount,time, date,academic_term, entered_by";
                    $query4i .= ") VALUES (";
                    $query4i .= "'{$school_number}', '{$pupil_id}', '$getAllAmountDebited', NOW(), NOW(), '{$get_academic_term}', '{$name}'";
                    $query4i .= ")";

                    $query_result3i = mysqli_query($connection, $query4i);
                    
                    
                    $query4ii = "INSERT INTO pta_fee_payments (";
                    $query4ii .= "school_number, pupil_id, amount,time, date,academic_term, entered_by";
                    $query4ii .= ") VALUES (";
                    $query4ii .= "'{$school_number}', '{$pupil_id}', '$differnece_for_pta_on_first_pay', NOW(), NOW(), '{$get_academic_term}', '{$name}'";
                    $query4ii .= ")";

                    $query_result4ii = mysqli_query($connection, $query4ii);
                     
                 
             }  else {
                     
                 
                    
                    if($accumulated_payments <= $getAllAmountDebited){
                         $query_for_update = "UPDATE student_ledger_payments SET amount = '$accumulated_payments' WHERE pupil_id ='$pupil_id' AND deleted='NO'";
                         
                       $results =   mysqli_query($connection,$query_for_update);
                   
                   confirm_query($results);  
                   
                     // $payments ->updateStudentLedger($accumulated_payments, $pupil_id);   
                    }  else {
                        
                         $query_for_updater = "UPDATE student_ledger_payments SET amount = '$getAllAmountDebited' WHERE pupil_id ='$pupil_id' AND deleted='NO'";
                         
                       $resultss =   mysqli_query($connection,$query_for_updater);
                   
                   confirm_query($resultss); 
                        
                         // $payments ->updateStudentLedger($getAllAmountDebited, $pupil_id);
                        
                        
                        $previous_payments_on_pta = $get_PTAPaid;
                        
                        $pta_remind_on_balance = $accumulated_payments - $getAllAmountDebited;
                        
                        if($previous_payments_on_pta=="" && $pta_remind_on_balance <= $getPTAFeesAll ){
                    $query2i = "INSERT INTO pta_fee_payments (";
                    $query2i .= "school_number, pupil_id, amount,time, date,academic_term, entered_by";
                    $query2i .= ") VALUES (";
                    $query2i .= "'{$school_number}', '{$pupil_id}', '$pta_remind_on_balance', NOW(), NOW(), '{$get_academic_term}', '{$name}'";
                    $query2i .= ")";

                    $query_result2 = mysqli_query($connection, $query2i); 
                        }elseif ($previous_payments_on_pta=="" && $pta_remind_on_balance > $getPTAFeesAll) {
                             
                            $credit_for_next_term = $pta_remind_on_balance - $getPTAFeesAll;
                            
                    $query2ii = "INSERT INTO pta_fee_payments (";
                    $query2ii .= "school_number, pupil_id, amount,time, date,academic_term, entered_by";
                    $query2ii .= ") VALUES (";
                    $query2ii .= "'{$school_number}', '{$pupil_id}', '$getPTAFeesAll', NOW(), NOW(), '{$get_academic_term}', '{$name}'";
                    $query2ii .= ")";

                    $query_result2 = mysqli_query($connection, $query2ii); 
                    
                    
                    
                     $query_for_update_credit_pta = "UPDATE fee_payments SET credit_balance = '$credit_for_next_term' WHERE pupil_id ='$pupil_id' AND deleted='NO'";
                         
                       $results_total_pta_credit_next_sem =   mysqli_query($connection,$query_for_update_credit_pta);
                   
                   confirm_query($results_total_pta_credit_next_sem);
                            
                   redirect_to("ledger_on.php");
                            
                            
                        }  else {
                                  //$previous_payments_on_pta  = $get_PTAPaid;
                                   
                                  $new_reminder_fees_from_school_fees =  $pta_remind_on_balance;
                                   
                                   $pta_payment_accum = $previous_payments_on_pta + $new_reminder_fees_from_school_fees;
                                   
                                   
                            
                              if($pta_payment_accum <= $getPTAFeesAll){
                                   $query_for_update_PTA = "UPDATE pta_fee_payments SET amount = '$pta_payment_accum' WHERE pupil_id ='$pupil_id' AND deleted='NO'";
                         
                       $results_pta =   mysqli_query($connection,$query_for_update_PTA);
                   
                   confirm_query($results_pta); 
                                  
                                  
                                 // $payments ->updatePTAFees($pta_payment_accum, $pupil_id);
                              }  else {
                                  
                                  
                                   $query_for_update_total = "UPDATE pta_fee_payments SET amount = '$getPTAFeesAll' WHERE pupil_id ='$pupil_id' AND deleted='NO'";
                         
                       $results_total_ptaa =   mysqli_query($connection,$query_for_update_total);
                   
                   confirm_query($results_total_ptaa); 
                                  
                                   // $payments ->updatePTAFees($getPTAFeesAll, $pupil_id);
                                    
                                    $credit_balance_pta = $pta_payment_accum - $getPTAFeesAll;
                                    
                                    
                                      $query_for_update_credit = "UPDATE fee_payments SET credit_balance = '$credit_balance_pta' WHERE pupil_id ='$pupil_id' AND deleted='NO'";
                         
                       $results_total_pta_credit =   mysqli_query($connection,$query_for_update_credit);
                   
                   confirm_query($results_total_pta_credit);
                                    
                                   // $payments ->updateFeesPaymentAsCredit($credit_balance, $pupil_id);
                                  
                              }
                             
                            
                        }
                        
                        
                    }
                   
                     
                 }
                 
                    
                 redirect_to("ledger_on.php");
                
                
            }  else {
                
              //AMOUNT GREATER THAN THE BILL
                
                $query5 = "INSERT INTO fee_payments (";
                $query5 .= "pupil_id, school_number, amount, receipt_no, mode_of_payment, mode_of_payment_number, date, time, fees_paid_by, fees_received_by, academic_term";
                $query5 .= ") VALUES (";
                $query5 .= "'{$pupil_id}', '{$school_number}', '$amount', '$receipt_no', '{$mode_of_payment}', '{$mode_of_payment_number}', NOW(), NOW(), '{$fees_paid_by}', '{$name}', '{$get_academic_term}'";
                $query5 .= ")";

                $query_result = mysqli_query($connection, $query5) or die(mysql_error());
              
                
                
                
                $query6 = "INSERT INTO pta_fee_payments (";
                    $query6 .= "school_number, pupil_id, amount,time, date,academic_term, entered_by";
                    $query6 .= ") VALUES (";
                    $query6 .= "'{$school_number}', '{$pupil_id}', '$getPTAFeesAll', NOW(), NOW(), '{$get_academic_term}', '{$name}'";
                    $query6 .= ")";

                    $query_result2 = mysqli_query($connection, $query6); 
                    
                    
                    $query7 = "INSERT INTO student_ledger_payments (";
                    $query7 .= "school_number, pupil_id, amount,time, date,academic_term, entered_by";
                    $query7 .= ") VALUES (";
                    $query7 .= "'{$school_number}', '{$pupil_id}', '$getAllAmountDebited', NOW(), NOW(), '{$get_academic_term}', '{$name}'";
                    $query7 .= ")";

                    $query_result3 = mysqli_query($connection, $query7);  
                    
                    
                    $remain_for_credit_balance = $amount - $total_amount_on_bill;
                
                   

// $payments ->updateFeesPaymentAsCredit($remain_for_credit_balance, $pupil_id) or die(mysql_error());
                    
                    
                    
                    $query_for_credit = "UPDATE fee_payments SET credit_balance = '$remain_for_credit_balance' WHERE pupil_id ='$pupil_id' AND deleted='NO'";
                    
                   $results =   mysqli_query($connection,$query_for_credit);
                   
                   confirm_query($results);
                   
                redirect_to("ledger_on.php");
                    
                    
                
            }
    }
    }

    public function getFeesPaidByType($id) {
        global $connection;
//        $escape_academic_term = mysqli_real_escape_string($connection, $academic_term);

        $query = "SELECT bill_type, SUM(amount) AS billTypeAmount ";
        $query .= "FROM fee_payments ";
        $query .= "WHERE pupil_id = '$id' ";
//        $query .= "AND academic_term = '{$escape_academic_term}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "GROUP BY biil_type";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($row = mysqli_fetch_assoc($query_results)) {
            return $row;
        } else {
            return NULL;
        }
    }

    public function getAllFeesPaid($id) {
        global $connection;

        $query = "SELECT SUM(amount) AS allFeesPaid ";
        $query .= "FROM student_ledger_payments ";
        $query .= "WHERE pupil_id = '{$id}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "GROUP BY pupil_id";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($row = mysqli_fetch_assoc($query_results)) {
            return $row;
        } else {
            return NULL;
        }
    }

    public function getPTAPaid($id) {
        global $connection;

        $query = "SELECT SUM(amount) AS ptaFeesPaid ";
        $query .= "FROM pta_fee_payments ";
        $query .= "WHERE pupil_id = '{$id}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "GROUP BY pupil_id";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($row = mysqli_fetch_assoc($query_results)) {
            return $row;
        } else {
            return NULL;
        }
    }
    
     public function get_Credit_Balance($id) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM fee_payments ";
        $query .= "WHERE pupil_id = '{$id}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "GROUP BY pupil_id";

        $query_results = mysqli_query($connection, $query) or die(mysql_error());
        confirm_query($query_results);

        if (mysqli_num_rows($query_results) > 0) {
            while ($row = mysqli_fetch_array($query_results)) {
                 return['credit_balance'];
            }
        } else {
            return NULL;
        }
    }

    public function getTermFeePayments($id, $academic_term) {
        global $connection;
        $escape_academic_term = mysqli_real_escape_string($connection, $academic_term);

        $query = "SELECT SUM(amount) AS termFeesPaid ";
        $query .= "FROM fee_payments ";
        $query .= "WHERE pupil_id = '$id' ";
        $query .= "AND academic_term = '{$escape_academic_term}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "GROUP BY pupil_id";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($row = mysqli_fetch_assoc($query_results)) {
            return $row;
        } else {
            return NULL;
        }
    }
    
    public function getTermFeePaymentsById($id) {
        global $connection;
        $escape_academic_term = mysqli_real_escape_string($connection, $academic_term);

        $query = "SELECT SUM(amount) AS termFeesPaid ";
        $query .= "FROM fee_payments ";
        $query .= "WHERE pupil_id = '$id' ";
       // $query .= "AND academic_term = '{$escape_academic_term}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "GROUP BY pupil_id";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($row = mysqli_fetch_assoc($query_results)) {
            return $row;
        } else {
            return NULL;
        }
    }

    public function getTransactionDetails($pupil_id) {
        global $connection;
        $escape_pupil_id = mysqli_real_escape_string($connection, $pupil_id);

        $query = "SELECT * ";
        $query .= "FROM fee_payments ";
        $query .= "WHERE pupil_id = '{$escape_pupil_id}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY id DESC ";
        $query .= "LIMIT 1";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($row = mysqli_fetch_assoc($query_results)) {
            return $row;
        } else {
            return NULL;
        }
    }

    public function getPTATransactionDetails($pupil_id) {
        global $connection;

        $escape_pupil_id = mysqli_real_escape_string($connection, $pupil_id);

        $query = "SELECT * ";
        $query .= "FROM pta_fee_payments ";
        $query .= "WHERE pupil_id = '{$escape_pupil_id}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY pta_id DESC ";
        $query .= "LIMIT 1";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($row = mysqli_fetch_assoc($query_results)) {
            return $row;
        } else {
            return NULL;
        }
    }

    public function getPTADailyPayments($name, $date) {
        global $connection;

        $safe_name = escape_value($name);
        $safe_date = escape_value($date);

        $query = "SELECT *, SUM(amount) AS ptaDailyPayment ";
        $query .= "FROM pta_fee_payments ";
        $query .= "WHERE date = '$safe_date' ";
        $query .= "AND entered_by = '{$safe_name}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "GROUP BY entered_by ";
        $query .= "LIMIT 1";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($row = mysqli_fetch_assoc($query_results)) {
            return $row;
        } else {
            return NULL;
        }
    }
    
     public function getTermPTAPayments($academic_term) {
        global $connection;

        $safe_academic_term = escape_value($academic_term);

        $query = "SELECT *, SUM(amount) AS ptaTermPayment ";
        $query .= "FROM pta_fee_payments ";
        $query .= "WHERE academic_term = '{$safe_academic_term}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "GROUP BY academic_term ";
        $query .= "LIMIT 1";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($row = mysqli_fetch_assoc($query_results)) {
            return $row;
        } else {
            return NULL;
        }
    }
    
    public function getPTADailyPaid($name, $date) {
        global $connection;

        $safe_name = escape_value($name);
        $safe_date = escape_value($date);

        $query = "SELECT * ";
        $query .= "FROM pta_fee_payments ";
        $query .= "WHERE date = '$safe_date' ";
        $query .= "AND entered_by = '{$safe_name}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "GROUP BY entered_by ";
        $query .= "ORDER BY pta_id ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        return $query_results;
    }

    public function getTotalDailyFeesCollected($account_clerk, $date) {
        global $connection;

        $escape_account_clerk = mysqli_real_escape_string($connection, $account_clerk);
        $escape_date = mysqli_real_escape_string($connection, $date);

        $query = "SELECT * ";
        $query .= "FROM fee_payments ";
        $query .= "WHERE fees_received_by = '{$escape_account_clerk}' ";
        $query .= "AND date = '$escape_date' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY id ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        return $query_results;
    }

    public function getFeesCollectedBetweenSelectedDates($clerk, $begin_date, $end_date) {
        global $connection;

        $escape_clerk = mysqli_real_escape_string($connection, $clerk);
        $escape_begin_date = mysqli_real_escape_string($connection, $begin_date);
        $escape_end_date = mysqli_real_escape_string($connection, $end_date);

        $sql = "SELECT * ";
        $sql .= "FROM fee_payments ";
        $sql .= "WHERE date BETWEEN '$escape_begin_date' AND '$escape_end_date' ";
        $sql .= "AND fees_received_by = '{$escape_clerk}' ";
        $sql .= "AND deleted = 'NO' ";
        $sql .= "ORDER BY id ASC";

        $result = mysqli_query($connection, $sql);
        confirm_query($result);

        return $result;
    }

     public function getPTAFeesCollectedBetweenSelectedDates($clerk, $begin_date, $end_date) {
        global $connection;

        $escape_clerk = mysqli_real_escape_string($connection, $clerk);
        $escape_begin_date = mysqli_real_escape_string($connection, $begin_date);
        $escape_end_date = mysqli_real_escape_string($connection, $end_date);

        $sql = "SELECT * ";
        $sql .= "FROM pta_fee_payments ";
        $sql .= "WHERE date BETWEEN '$escape_begin_date' AND '$escape_end_date' ";
        $sql .= "AND entered_by = '{$escape_clerk}' ";
        $sql .= "AND deleted = 'NO' ";
        $sql .= "ORDER BY pta_id ASC";

        $result = mysqli_query($connection, $sql);
        confirm_query($result);

        return $result;
    }
    
    public function getTotalFeesCollectedBetweenSelectedDatesByClerk($clerk, $begin_date, $end_date) {
        global $connection;

        $escape_clerk = mysqli_real_escape_string($connection, $clerk);
        $escape_begin_date = mysqli_real_escape_string($connection, $begin_date);
        $escape_end_date = mysqli_real_escape_string($connection, $end_date);

        $query = "SELECT SUM(amount) AS totalFeesCollected ";
        $query .= "FROM fee_payments ";
        $query .= "WHERE date BETWEEN '$escape_begin_date' AND '$escape_end_date' ";
        $query .= "AND fees_received_by = '{$escape_clerk}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY id DESC ";
        $query .= "LIMIT 1";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($row = mysqli_fetch_assoc($query_results)) {
            return $row;
        } else {
            return NULL;
        }
    }

    public function getTotalPTAFeesCollectedBetweenSelectedDatesByClerk($clerk, $begin_date, $end_date) {
        global $connection;

        $escape_clerk = mysqli_real_escape_string($connection, $clerk);
        $escape_begin_date = mysqli_real_escape_string($connection, $begin_date);
        $escape_end_date = mysqli_real_escape_string($connection, $end_date);

        $query = "SELECT SUM(amount) AS totalPTAFeesCollected ";
        $query .= "FROM pta_fee_payments ";
        $query .= "WHERE date BETWEEN '$escape_begin_date' AND '$escape_end_date' ";
        $query .= "AND entered_by = '{$escape_clerk}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "GROUP BY entered_by ";
        $query .= "ORDER BY pta_id ASC ";
        $query .= "LIMIT 1";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($row = mysqli_fetch_assoc($query_results)) {
            return $row;
        } else {
            return NULL;
        }
    }
    
    public function getFeesCollectedTodayByClerk($account_clerk, $date) {
        global $connection;

        $escape_account_clerk = mysqli_real_escape_string($connection, $account_clerk);
        $escape_date = mysqli_real_escape_string($connection, $date);

        $query = "SELECT * ";
        $query .= "FROM fee_payments ";
        $query .= "WHERE fees_received_by = '{$escape_account_clerk}' ";
        $query .= "AND date = '$escape_date' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY id ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        return $query_results;
    }

    public function getSumOfDailyFeesCollected($account_clerk, $date) {
        global $connection;

        $escape_account_clerk = mysqli_real_escape_string($connection, $account_clerk);
        $escape_date = mysqli_real_escape_string($connection, $date);

        $query = "SELECT SUM(amount) AS totalAmountCollected ";
        $query .= "FROM fee_payments ";
        $query .= "WHERE fees_received_by = '{$escape_account_clerk}' ";
        $query .= "AND date = '$escape_date' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY id DESC ";
        $query .= "LIMIT 1";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

//        return $query_results;
        if ($row = mysqli_fetch_assoc($query_results)) {
            return $row;
        } else {
            return NULL;
        }
    }
    
    function updateStudentLedger($amount,$pupil_id){
        
          global $connection;
          
          $current_date = date("Y-m-d");
          $current_time = time();
          
         $query = "UPDATE student_ledger_payments SET ";
        $query .= "amount = '{$amount}', ";
        $query .= "time = '{$current_time}', ";
        $query .= "date = '{$current_date}', ";
        
         
        $query .= "WHERE pupil_id = '{$pupil_id}' ";
        $query .= "AND deleted = 'NO' ";
         
        
        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);
    }
    
    
      function updatePTAFees($amount,$pupil_id){
        
          global $connection;
          
          $current_date = date("Y-m-d");
          $current_time = time();
          
         $query = "UPDATE pta_fee_payments SET ";
        $query .= "amount = '{$amount}', ";
        $query .= "time = '{$current_time}', ";
        $query .= "date = '{$current_date}', ";
        
         
        $query .= "WHERE pupil_id = '{$pupil_id}' ";
        $query .= "AND deleted = 'NO' ";
         
        
        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);
    }
    
    
    
      function updateFeesPaymentAsCredit($amount,$pupil_id){
        
          global $connection;
          
          $current_date = date("Y-m-d");
          $current_time = time();
          
         $query = "UPDATE fee_payments SET ";
        $query .= "credit_balance = '{$amount}', ";
         
        $query .= "WHERE pupil_id = '{$pupil_id}' ";
        $query .= "AND deleted = 'NO' ";
         
        
        $query_result = mysqli_query($connection, $query) or die(mysql_error());
        //confirm_query($query_result);
    }
    
    

    public function saveFeesPaySuccessBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-success'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li><strong>TRANSACTION PERFORMED HERE,</strong>is successful!</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

}
