<?php
class Database {
    private $host = "localhost"; // Mantén "localhost" en XAMPP
    private $db_name = "dentista_pro"; // Nombre exacto de la base de datos
    private $username = "csilva"; // Usuario predeterminado en XAMPP
    private $password = "csilva"; // Contraseña vacía en XAMPP (a menos que la hayas configurado)
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>
