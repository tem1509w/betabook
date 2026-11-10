<?php
/**
 * Class Database
 * Thực thi SQL queries với prepared statements
 * Tuân theo kiến trúc MiniShop Basic PHP
 */
class Database {
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;
    private $charset;
    private $conn;
    public $lastInsertId = null;
    public $rowCount = null;

    public function __construct() {
        $config = require __DIR__ . '/../config/database.php';
        $this->host = $config['host'];
        $this->port = (int)$config['port'];
        $this->db_name = $config['dbname'];
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->charset = $config['charset'];
        $this->conn = null;
    }

    public function getConnection() {
        if ($this->conn instanceof PDO) {
            return $this->conn;
        }

        $this->conn = new PDO(
            "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name . ";charset=" . $this->charset,
            $this->username,
            $this->password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        return $this->conn;
    }

    /**
     * Thực thi câu SQL (INSERT, UPDATE, DELETE)
     * Sử dụng prepared statements với mảng tham số
     *
     * @param string $sql
     * @param array $params
     * @return bool|int Trả về số dòng ảnh hưởng hoặc false nếu thất bại
     */
    public function execute($sql, $params = []) {
        $conn = $this->getConnection();
        $stmt = $conn->prepare($sql);
        $this->rowCount = $stmt->execute($params);
        $this->lastInsertId = $conn->lastInsertId();
        return $this->rowCount;
    }

    /**
     * Truy vấn và lấy tất cả kết quả (dùng cho SELECT, danh sách)
     *
     * @param string $sql
     * @param array $params
     * @return array Mảng chứa tất cả bản ghi
     */
    public function fetchAll($sql, $params = []) {
        $conn = $this->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Truy vấn và lấy duy nhất một bản ghi (dùng cho chi tiết, ID)
     *
     * @param string $sql
     * @param array $params
     * @return array|false Một bản ghi hoặc false nếu không có dữ liệu
     */
    public function fetchOne($sql, $params = []) {
        $conn = $this->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Chạy câu SQL thuần túy (dùng cho CREATE, ALTER, v.v.)
     *
     * @param string $sql
     * @return bool|int
     */
    public function run($sql) {
        $conn = $this->getConnection();
        $conn->exec($sql);
        return true;
    }

    /**
     * Lấy ID vừa chèn (sau khi INSERT)
     *
     * @return int|string ID mới chèn vào
     */
    public function insertId() {
        return $this->lastInsertId;
    }
}