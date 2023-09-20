<?php
date_default_timezone_set("Asia/Jakarta");
header('Content-Type: application/json');
require_once 'DatabaseConnection.php';
class Transactions
{
    private DatabaseConnection $db;

    public function __construct()
    {
        $this->db = new DatabaseConnection();
    }

    public function transfer_money($senderAccount, $receiverAccount, $transferAmount, $senderAccountPin): void
    {
        if ($transferAmount < 0) {
            http_response_code(response_code: 400);
            echo json_encode(array(
                'status' => '400',
                'message' => 'Transfer amount must be > 0'
            ), JSON_PRETTY_PRINT);
            $this->db->closeConnection();
            die();
        }

        $query = "SELECT sender.balance as b1, receiver.balance as b2 FROM user sender, user receiver WHERE sender.account_id = $senderAccount AND receiver.account_id = $receiverAccount";
        $result = $this->db->execute_select_query($query);

        if (count($result['result']) < 1) {
            http_response_code(response_code: 400);
            echo json_encode(array(
                'status' => '400',
                'message' => 'Bad Request.'
            ), JSON_PRETTY_PRINT);
            $this->db->closeConnection();
            die();
        }

        $senderCurrentBalance = $result['result'][0]['b1'];
        $receiverCurrentBalance = $result['result'][0]['b2'];

        if ($senderCurrentBalance < $transferAmount) {
            http_response_code(response_code: 400);
            echo json_encode(array(
                'status' => '400',
                'message' => 'Account balance is less than transfer amount.',
            ), JSON_PRETTY_PRINT);
            $this->db->closeConnection();
            die();
        }
        
        $totalAmount = $receiverCurrentBalance + $transferAmount;
        $currentBalance = $senderCurrentBalance - $transferAmount;
        $transferDate = date('Y-m-d');
        $transferTime = date('h:m:s');

        $batchQuery = array(
            "UPDATE user sender, user receiver SET sender.balance = $currentBalance, receiver.balance = $totalAmount WHERE sender.account_id = $senderAccount AND receiver.account_id = $receiverAccount AND sender.account_pin = $senderAccountPin",
            "INSERT INTO transfer (account_id) VALUES ($senderAccount)",
            "SET @last_id = LAST_INSERT_ID()",
            "INSERT INTO transfer_detail (transfer_id, account_id, date, time, amount) VALUES (@last_id, $receiverAccount, '$transferDate', '$transferTime', $transferAmount)"
        );

        $result = $this->db->execute_insert_query($batchQuery);

        if(!$result['status']) {
            http_response_code(response_code: 400);
            echo json_encode(array(
                'status' => '400',
                'message' => 'Bad Request'
            ), JSON_PRETTY_PRINT);
            $this->db->closeConnection();
            die();
        }
        
        http_response_code(200);
        echo json_encode(array(
            'status' => 'success',
            'date' => $transferDate,
            'time' => $transferTime,
            'message' => 'Transfer to account ' . $receiverAccount . ' successful.'
        ), JSON_PRETTY_PRINT);

        $this->db->closeConnection();
    }

    // Function to retrieve transaction history for an account
    public function transaction_history($userId): void
    {
        $query = "SELECT u.account_name, td.amount, td.date as transfer_date, td.time as transfer_time FROM transfer_detail td, transfer t, user u 
                       WHERE td.transfer_id = t.transfer_id AND 
                             td.account_id = u.account_id AND 
                             t.account_id = $userId 
                       ORDER BY td.date";

        $result = $this->db->execute_select_query($query);

        if(count($result['result']) < 1) {
            http_response_code(response_code: 200);
            echo json_encode(array(
                'status' => '200',
                'message' => 'No transactions history for this account.'
            ), JSON_PRETTY_PRINT);
            $this->db->closeConnection();
            die();
        }

        http_response_code(200);

        echo json_encode(array(
            'transactions history' => $result['result'],
        ), JSON_PRETTY_PRINT);

        $this->db->closeConnection();
    }

    public function filter_transactions_history($userId, $minTransferAmount) : void
    {
        $query = "SELECT u.account_name, td.amount, td.date as transfer_date, td.time as transfer_time FROM transfer_detail td, transfer t, user u 
                       WHERE td.transfer_id = t.transfer_id AND 
                             td.account_id = u.account_id AND 
                             t.account_id = $userId AND 
                             td.amount >= $minTransferAmount 
                       ORDER BY td.date";

        $result = $this->db->execute_select_query($query);

        if(count($result['result']) < 1) {
            http_response_code(response_code: 200);
            echo json_encode(array(
                'status' => '200',
                'message' => 'No transactions history for this account.'
            ), JSON_PRETTY_PRINT);
            $this->db->closeConnection();
            die();
        }

        http_response_code(200);

        echo json_encode(array(
            'transactions history' => $result['result'],
        ), JSON_PRETTY_PRINT);

        $this->db->closeConnection();
    }
}