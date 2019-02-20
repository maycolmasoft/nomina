<?php

class TipoRubrosController extends ControladorBase{
    
    public function __construct() {
        parent::__construct();
    }
    
    //maycol
    
    public function index(){
        
        
        $tipo_rubros = new TipoRubrosModel();
        $resultSet=$tipo_rubros->getAll("id_tipo_rubros");
        $resultEdit = "";
        
        session_start();
        
        if (isset($_SESSION['nombre_usuarios']))
        {
             $nombre_controladores = "TipoRubros";
            $id_rol= $_SESSION['id_rol'];
            $resultPer = $tipo_rubros->getPermisosVer("   controladores.nombre_controladores = '$nombre_controladores' AND permisos_rol.id_rol = '$id_rol' " );
            
            if (!empty($resultPer))
            {
                
                
                if (isset ($_GET["id_tipo_rubros"]))
                {
                    
                    $nombre_controladores = "TipoRubros";
                    $id_rol= $_SESSION['id_rol'];
                    $resultPer = $tipo_rubros->getPermisosEditar("   controladores.nombre_controladores = '$nombre_controladores' AND permisos_rol.id_rol = '$id_rol' " );
                    
                    if (!empty($resultPer))
                    {
                        
                        $_id_tipo_rubros = $_GET["id_tipo_rubros"];
                        $columnas = " id_tipo_rubros, nombre_tipo_rubros";
                        $tablas   = "tipo_rubros";
                        $where    = "id_tipo_rubros = '$_id_tipo_rubros' ";
                        $id       = "nombre_tipo_rubros";
                        
                        $resultEdit = $tipo_rubros->getCondiciones($columnas ,$tablas ,$where, $id);
                        
                    }
                    else
                    {
                        $this->view("Error",array(
                            "resultado"=>"No tiene Permisos de Editar Tipo Rubros"
                            
                        ));
                        
                        
                    }
                    
                }
                
                
                $this->view("TipoRubros",array(
                    "resultSet"=>$resultSet, "resultEdit" =>$resultEdit
                    
                ));
                
            }
            else
            {
                $this->view("Error",array(
                    "resultado"=>"No tiene Permisos de Acceso a Tipo Rubros"
                    
                ));
                
                exit();
            }
            
        }
        else{
            
            $this->redirect("Usuarios","sesion_caducada");
            
        }
        
    }
    
    public function InsertaTipoRubros(){
        
        session_start();
        
        $permisos_rol=new PermisosRolesModel();
        $tipo_rubros=new TipoRubrosModel();
        $nombre_controladores = "TipoRubros";
        $id_rol= $_SESSION['id_rol'];
        $resultPer = $permisos_rol->getPermisosEditar("   controladores.nombre_controladores = '$nombre_controladores' AND permisos_rol.id_rol = '$id_rol' " );
        
        if (!empty($resultPer))
        {
            $resultado = null;
            
            $tipo_rubros=new TipoRubrosModel();
            
            if (isset ($_POST["nombre_tipo_rubros"]) )
            
            {
                
                
                
                $_nombre_tipo_rubros = $_POST["nombre_tipo_rubros"];
                $_id_tipo_rubros = $_POST["id_tipo_rubros"];
                
                if($_id_tipo_rubros>0)
                {
                    
                    $colval = " nombre_tipo_rubros = '$_nombre_tipo_rubros'   ";
                    $tabla = "tipo_rubros";
                    $where = "id_tipo_rubros = '$_id_tipo_rubros'    ";
                    
                    $resultado=$tipo_rubros->UpdateBy($colval, $tabla, $where);
                    
                    
                    
                }else {
                    
                    
                    
                    $funcion = "ins_tipo_rubros";
                    $parametros = " '$_nombre_tipo_rubros'  ";
                    $tipo_rubros->setFuncion($funcion);
                    $tipo_rubros->setParametros($parametros);
                    $resultado=$tipo_rubros->Insert();
                    
                }
                
            }
            $this->redirect("TipoRubros", "index");
            
        }
        else
        {
            $this->view("Error",array(
                
                "resultado"=>"No tiene Permisos de Insertar Tipo Rubros"
                
            ));
            
            
        }
        
    }
    
    public function borrarId()
    {
        
        session_start();
        
        $permisos_rol=new PermisosRolesModel();
        $nombre_controladores = "TipoRubros";
        $id_rol= $_SESSION['id_rol'];
        $resultPer = $permisos_rol->getPermisosEditar("   controladores.nombre_controladores = '$nombre_controladores' AND permisos_rol.id_rol = '$id_rol' " );
        
        if (!empty($resultPer))
        {
            if(isset($_GET["id_tipo_rubros"]))
            {
                $id_tipo_rubros=(int)$_GET["id_tipo_rubros"];
                
                $tipo_rubros=new TipoRubrosModel();
                
                $tipo_rubros->deleteBy(" id_tipo_rubros",$id_tipo_rubros);
                
            }
            
            $this->redirect("TipoRubros", "index");
            
            
        }
        else
        {
            $this->view("Error",array(
                "resultado"=>"No tiene Permisos de Borrar Tipo Rubros"
                
            ));
        }
        
    }
    
    
    
    
    
    
    
    
}
?>