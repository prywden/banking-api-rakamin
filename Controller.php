<?php
require_once 'process/Account.php';
require_once 'process/Transactions.php';
class Controller {
    private string $method;
    private string $path;
    private bool $request_processed = true;
    private Transactions $transactions;
    private Account $account;

    public function __construct($method, $path)
    {
        $this->method = $method;
        $this->path = $path;
        $this->transactions = new Transactions();
        $this->account = new Account();
    }

    public function process_uri(): void
    {
        switch ($this->method) {
            case 'GET':
                $this->process_get_request($this->path);
                break;
            case 'POST':
               $this->process_post_request($this->path);
                break;
            default:
                $this->request_processed = false;
        }

        if(!$this->request_processed) {
            http_response_code(404);
            echo json_encode(array(
                'status' => '404',
                'massage' => 'Not Found',
            ), JSON_PRETTY_PRINT);
        }
    }
    
    private function process_get_request($path): void
    {
        switch ($path) {
            case '/transactions':
                if (isset($_GET['userId'])) {
                    $user_id = $_GET['userId'];
                    $this->transactions->transaction_history($user_id);
                }
                break;
            case '/transactions/filter':
                if (isset($_GET['userId']) && isset($_GET['minTransferAmount'])) {
                    $user_id = $_GET['userId'];
                    $minTransferAmount = $_GET['userId'];

                    $this->transactions->filter_transactions_history($user_id, $minTransferAmount);
                }
                break;
            case '/profile':
                if (isset($_GET['userId'])) {
                    $user_id = $_GET['userId'];
                    $this->account->account_profile($user_id);
                }
                break;
            case '/balance':
                if (isset($_GET['userId'])) {
                    $user_id = $_GET['userId'];
                    $this->account->account_balance($user_id);
                }
                break;
            default:
                $this->request_processed = false;
        }
    }
    
    private function process_post_request($path): void
    {
        switch ($path) {
            case '/transfer':
                if (isset($_POST['senderAccount']) && isset($_POST['receiverAccount']) && isset($_POST['amount']) && $_POST['senderAccountPin']) {
                    $senderAccount = $_POST['senderAccount'];
                    $receiverAccount = $_POST['receiverAccount'];
                    $transferAmount = $_POST['amount'];
                    $senderAccountPin = $_POST['senderAccountPin'];

                    $this->transactions->transfer_money($senderAccount, $receiverAccount, $transferAmount, $senderAccountPin);
                }
                break;
            default:
                $this->request_processed = false;
        }
    }
}