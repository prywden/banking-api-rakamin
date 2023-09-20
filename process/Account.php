<?php
require_once 'DatabaseConnection.php';

header('Content-Type: application/json');
class Account {
    private DatabaseConnection $db;

    public function __construct()
    {
        $this->db = new DatabaseConnection();
    }

    // Function to retrieve account balance
    public function account_balance($accountID): void
    {
        $query = "SELECT account_name, account_card_number, balance FROM user WHERE account_id = $accountID";
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

        $data = $result['result'][0];

        http_response_code(200);
        echo json_encode(array(
            'name' => $data['account_name'],
            'card number' => $data['account_card_number'],
            'balance' => $data['balance'],
        ), JSON_PRETTY_PRINT);

        $this->db->closeConnection();
    }

    public function account_profile($accountID): void
    {
        $query = "SELECT account_name, account_address, phone_number, email, account_card_number, balance FROM user WHERE account_id = $accountID";
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

        $data = $result['result'][0];

        http_response_code(200);
        echo json_encode(array(
            'name' => $data['account_name'],
            'address' => $data['account_address'],
            'phone number' => $data['phone_number'],
            'email' => $data['email'],
            'card number' => $data['account_card_number'],
            'balance' => $data['balance'],
        ), JSON_PRETTY_PRINT);

        $this->db->closeConnection();
    }
}