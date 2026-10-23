<?php

require_once __DIR__ . '/../config/database.php';

class ConectorPDO
{
    private string $servername;
    private string $port;
    private string $username;
    private string $password;
    private string $dbname;
    private ?PDO $conexion;

    public function __construct(
        ?string $servername = null,
        ?string $username = null,
        ?string $password = null,
        ?string $dbname = null,
        ?string $port = null
    ) {
        $config = DatabaseConfig::get();
        $this->servername = $servername ?? $config['host'];
        $this->port = $port ?? $config['port'];
        $this->username = $username ?? $config['username'];
        $this->password = $password ?? $config['password'];
        $this->dbname = $dbname ?? $config['database'];
        $this->conexion = null;
    }

    public function establecerConexion(): PDO
    {
        try {
            $this->conexion = new PDO(
                "mysql:host={$this->servername};port={$this->port};dbname={$this->dbname};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            throw new RuntimeException(
                "No se pudo conectar con la base de datos.",
                0,
                $e
            );
        }

        return $this->conexion;
    }

    public function desconectar(): void
    {
        $this->conexion = null;
    }
}

?>
