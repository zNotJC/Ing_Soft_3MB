<?php





class Usuario
{
    


    private string $cedula;


    


    private string $usuario;


    


    private string $correo;


    


    private string $claveHash;


    



    private bool $activo;


    


    private bool $administrador;


    


    private bool $docente;
    private bool $tecnico;


    










    public function __construct(
        string $cedula,
        string $usuario,
        string $correo,
        string $claveHash,
        bool $activo,
        bool $administrador,
        bool $docente,
        bool $tecnico = false
    ) {
        $this->cedula = $cedula;
        $this->usuario = $usuario;
        $this->correo = $correo;
        $this->claveHash = $claveHash;
        $this->activo = $activo;
        $this->administrador = $administrador;
        $this->docente = $docente;
        $this->tecnico = $tecnico;
    }


    


    public function getCedula(): string
    {
        return $this->cedula;
    }


    


    public function getUsuario(): string
    {
        return $this->usuario;
    }


    


    public function getCorreo(): string
    {
        return $this->correo;
    }


    


    public function getClaveHash(): string
    {
        return $this->claveHash;
    }


    


    public function estaActivo(): bool
    {
        return $this->activo;
    }


    


    public function esAdministrador(): bool
    {
        return $this->administrador;
    }


    


    public function esDocente(): bool
    {
        return $this->docente;
    }


    


    public function esTecnico(): bool
    {
        return $this->tecnico;
    }

    public function noTieneRoles(): bool
    {
        return !$this->administrador && !$this->docente && !$this->tecnico;
    }

    public function tieneRolesInconsistentes(): bool
    {
        return (int) $this->administrador + (int) $this->docente + (int) $this->tecnico !== 1;
    }
}


?>