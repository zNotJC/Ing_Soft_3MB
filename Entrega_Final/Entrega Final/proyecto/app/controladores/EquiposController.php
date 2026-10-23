<?php
require_once __DIR__ . '/../nucleo/ApiController.php';
require_once __DIR__ . '/../modelos/AccesoDatosEquipos.php';
require_once __DIR__ . '/../modelos/AccesoDatosCatalogos.php';

final class EquiposController extends ApiController
{
    private const TIPOS = ['Ordenadores','Periféricos','Televisores','Aires Acondicionados'];
    private const ESTADOS = ['Disponible','En uso','Roto'];

    public function handle(): void
    {
        $method=$_SERVER['REQUEST_METHOD']??'GET';
        $role=$this->requireRole(['Administrador','Tecnico','Docente']);
        [$connector,$connection]=$this->connect();
        try {
            $equipos=new AccesoDatosEquipos($connection); $labs=new AccesoDatosCatalogos($connection);
            if ($method==='GET') ApiResponse::send(200,'success','Inventario consultado correctamente.',['equipos'=>$equipos->listar(),'laboratorios'=>$equipos->laboratorios(),'laboratoriosDisponibles'=>$equipos->laboratoriosDisponibles(),'tipos'=>self::TIPOS,'estados'=>self::ESTADOS]);
            if ($role!=='Administrador') ApiResponse::send(403,'error','Solo el Administrador puede modificar el inventario.');
            $input=ApiResponse::input(); $tipoInventario=(string)($input['tipoInventario']??'Equipo');
            if ($tipoInventario==='Laboratorio') {
                if ($method==='POST') { $nombre=$this->requiredString($input,'nombre',50); $estado=(string)($input['estado']??''); if(!in_array($estado,['Disponible','En uso'],true)) ApiResponse::send(422,'error','Selecciona el estado del laboratorio.'); $id=$labs->crearLaboratorio($nombre,$estado); ApiResponse::send(201,'success','Laboratorio registrado.',['idLaboratorio'=>$id]); }
                if ($method==='PUT') { $id=$this->positiveId('idLaboratorio',$input); $nombre=$this->requiredString($input,'nombre',50); $estado=(string)($input['estado']??''); if(!in_array($estado,['Disponible','En uso'],true)) ApiResponse::send(422,'error','Selecciona el estado del laboratorio.'); if(!$labs->modificarLaboratorio($id,$nombre,$estado)) ApiResponse::send(404,'error','No se encontró el laboratorio o no hubo cambios.'); ApiResponse::send(200,'success','Laboratorio actualizado.',['idLaboratorio'=>$id]); }
                if ($method==='DELETE') { $id=$this->positiveId('idLaboratorio',$input); if(!$labs->eliminarLaboratorio($id)) ApiResponse::send(409,'error','No se puede eliminar un laboratorio que contiene equipos o tickets.'); ApiResponse::send(200,'success','Laboratorio eliminado.',['idLaboratorio'=>$id]); }
            }
            if (!in_array($tipoInventario,['Equipo','Laboratorio'],true)) ApiResponse::send(422,'error','Tipo de inventario no válido.');
            if ($method==='POST'||$method==='PUT') {
                $nombre=$this->requiredString($input,'nombre',100); $tipo=(string)($input['tipo']??''); $estado=(string)($input['estado']??'Disponible');
                if(!in_array($tipo,self::TIPOS,true)||!in_array($estado,self::ESTADOS,true)) ApiResponse::send(422,'error','Selecciona un tipo y estado válidos.');
                if($method==='POST'){ $id=$equipos->crear($nombre,null,$tipo,$estado); ApiResponse::send(201,'success','Equipo registrado.',['idEquipo'=>$id]); }
                $id=$this->positiveId('idEquipo',$input); if(!$equipos->modificar($id,$nombre,null,$tipo,$estado)) ApiResponse::send(404,'error','No se encontró el equipo o no hubo cambios.'); ApiResponse::send(200,'success','Equipo actualizado.',['idEquipo'=>$id]);
            }
            if($method==='DELETE') { $id=$this->positiveId('idEquipo',$input); if(!$equipos->eliminar($id)) ApiResponse::send(409,'error','No se puede eliminar un equipo con préstamos asociados.'); ApiResponse::send(200,'success','Equipo eliminado.',['idEquipo'=>$id]); }
            $this->methodNotAllowed(['GET','POST','PUT','DELETE']);
        } catch(PDOException $error) { error_log('[API inventario] ' . $error->getMessage()); ApiResponse::send(409,'error','No se pudo guardar o consultar el inventario. Comprueba la base de datos, los laboratorios existentes y los registros duplicados.'); }
        catch(Throwable $error) { error_log('[API inventario] ' . $error->getMessage()); ApiResponse::send(500,'error','No se pudo completar la operación de inventario.'); }
        finally { $connector->desconectar(); }
    }
}
