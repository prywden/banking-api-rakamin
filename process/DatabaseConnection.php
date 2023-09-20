<?php
class DatabaseConnection {
    private mysqli|false $db;

    public function __construct()
    {
        try {
            $this->db = mysqli_init();
            mysqli_options ($this->db, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);

            $this->db->ssl_set(
                NULL,
                NULL,
                'process/ssl/DigiCertGlobalRootCA.crt.pem',
                NULL,
                NULL);

            $conn = parse_ini_file('config.ini');

            mysqli_real_connect(
                $this->db,
                $conn['db_host'], // DB HOST
                $conn['db_username'], // DB USERNAME
                $conn['db_password'], // DB PASSWORD
                $conn['db_database'], // Database
                3306,
            );
        } catch (Exception $err) {
            header('Content-Type: application/json');
            http_response_code(response_code: 401);

            echo json_encode(array(
                'status' => 'Unauthorized',
                'stack trace' => $err->getTraceAsString(),
                'massage' => $err->getMessage(),
            ), JSON_PRETTY_PRINT);

            die();
        }

        // Display a message if database connection is successfully
        /*header('Content-Type: application/json');
        http_response_code(200);

        echo json_encode(array(
            'status' => 'OK',
            'message' => 'Connection successful',
        ), JSON_PRETTY_PRINT);*/
    }

    public function execute_insert_query(array $query) {
        try {
            $this->db->begin_transaction();

            foreach ($query as $q) {
                $statement = $this->db->prepare($q);
                $statement->execute();
            }

            $status = $this->db->commit();

            return $status ? array('status' => TRUE) : array('status' => FALSE);
        } catch (Exception $err) {
            header('Content-Type: application/json');
            http_response_code(response_code: 400);

            echo json_encode(array(
                'status' => 'Bad Request',
                'message' => $err->getMessage(),
            ), JSON_PRETTY_PRINT);
            $this->db->close();
            die();
        }
    }

    public function execute_update_query($query) {
        try {
            $statement = $this->db->prepare($query);
            $status = $statement->execute();

            return $status ? array('status' => TRUE) : array('status' => FALSE);
        } catch (Exception $err) {
            header('Content-Type: application/json');
            http_response_code(response_code: 400);

            echo json_encode(array(
                'status' => 'Bad Request',
                'message' => $err->getMessage(),
            ), JSON_PRETTY_PRINT);
            $this->db->close();
            die();
        }
    }

    public function execute_select_query($query) {
        try{
            $statement = $this->db->prepare($query);

            $statement->execute();
            $res = $statement->get_result();
            $rows = $res->fetch_all(MYSQLI_ASSOC);

            return array('result' => $rows);
        } catch (Exception $err) {
            header('Content-Type: application/json');
            http_response_code(response_code: 400);

            echo json_encode(array(
                'status' => 'Bad Request',
                'message' => $err->getMessage(),
            ), JSON_PRETTY_PRINT);
            $this->db->close();
            die();
        }
    }

    public function closeConnection() : void {
        $this->db->close();
    }
}