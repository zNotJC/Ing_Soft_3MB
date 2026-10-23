<?php

class AccesoDatosCatalogos
{
    private PDO $conexion;
    private array $tablas = [
        'lugares' => ['tabla' => 'lugar', 'id' => 'idLugar'],
        'prioridades' => ['tabla' => 'prioridad', 'id' => 'idPrioridad'],
        'estadosTicket' => ['tabla' => 'estado_ticket', 'id' => 'idEstado'],
        'estadosPrestamo' => ['tabla' => 'estado_prestamo', 'id' => 'idEstado']
    ];

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function validarTipo(string $tipo): array
    {
        if (!isset($this->tablas[$tipo])) throw new InvalidArgumentException('Catálogo no válido.');
        return $this->tablas[$tipo];
    }

    public function listar(string $tipo): array
    {
        $config = $this->validarTipo($tipo);
        return $this->conexion->query('SELECT ' . $config['id'] . ' AS id, nombre FROM ' . $config['tabla'] . ' ORDER BY nombre')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear(string $tipo, string $nombre): int
    {
        $config = $this->validarTipo($tipo);
        $consulta = $this->conexion->prepare('INSERT INTO ' . $config['tabla'] . ' (nombre) VALUES (:nombre)');
        $consulta->execute(['nombre' => $nombre]);
        return (int) $this->conexion->lastInsertId();
    }

    public function modificar(string $tipo, int $id, string $nombre): bool
    {
        $config = $this->validarTipo($tipo);
        $consulta = $this->conexion->prepare('UPDATE ' . $config['tabla'] . ' SET nombre = :nombre WHERE ' . $config['id'] . ' = :id');
        $consulta->execute(['id' => $id, 'nombre' => $nombre]);
        return $consulta->rowCount() > 0;
    }

    public function obtenerNombre(string $tipo, int $id): ?string
    {
        $config = $this->validarTipo($tipo);
        $consulta = $this->conexion->prepare('SELECT nombre FROM ' . $config['tabla'] . ' WHERE ' . $config['id'] . ' = :id');
        $consulta->execute(['id' => $id]);
        $nombre = $consulta->fetchColumn();
        return $nombre === false ? null : (string) $nombre;
    }

    public function eliminar(string $tipo, int $id): bool
    {
        $config = $this->validarTipo($tipo);
        $consulta = $this->conexion->prepare('DELETE FROM ' . $config['tabla'] . ' WHERE ' . $config['id'] . ' = :id');
        $consulta->execute(['id' => $id]);
        return $consulta->rowCount() > 0;
    }
}
