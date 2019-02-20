<?php

class FormulasController extends ControladorBase{
    
    public function __construct() {
        parent::__construct();
    }
    
    //maycol
    
    public function index(){
        
        
        $formulas = new FormulasModel();
        $resultSet=$formulas->getAll("id_formulas");
        $resultEdit = "";
        $tipo_formulas = new TipoFormulasModel();
        $resultFor=$tipo_formulas->getAll("nombre_tipo_formulas");
        
        
        session_start();
        
        if (isset($_SESSION['nombre_usuarios']))
        {
            $nombre_controladores = "Formulas";
            $id_rol= $_SESSION['id_rol'];
            $resultPer = $formulas->getPermisosVer("   controladores.nombre_controladores = '$nombre_controladores' AND permisos_rol.id_rol = '$id_rol' " );
            
            if (!empty($resultPer))
            {
                
                
                if (isset ($_GET["id_formulas"]))
                {
                    
                    $nombre_controladores = "Formulas";
                    $id_rol= $_SESSION['id_rol'];
                    $resultPer = $formulas->getPermisosEditar("   controladores.nombre_controladores = '$nombre_controladores' AND permisos_rol.id_rol = '$id_rol' " );
                    
                    if (!empty($resultPer))
                    {
                        
                        $_id_formulas = $_GET["id_formulas"];
                        $columnas = " id_formulas, id_tipo_formulas, porcentaje_formulas";
                        $tablas   = "formulas";
                        $where    = "id_formulas = '$_id_formulas' ";
                        $id       = "id_formulas";
                        
                        $resultEdit = $formulas->getCondiciones($columnas ,$tablas ,$where, $id);
                        
                    }
                    else
                    {
                        $this->view("Error",array(
                            "resultado"=>"No tiene Permisos de Editar Formulas"
                            
                        ));
                        
                        
                    }
                    
                }
                
                
                $this->view("Formulas",array(
                    "resultSet"=>$resultSet, "resultEdit" =>$resultEdit, "resultFor" =>$resultFor
                    
                ));
                
            }
            else
            {
                $this->view("Error",array(
                    "resultado"=>"No tiene Permisos de Acceso a Formulas"
                    
                ));
                
                exit();
            }
            
        }
        else{
            
            $this->redirect("Usuarios","sesion_caducada");
            
        }
        
    }
    
    public function InsertaFormulas(){
        
        session_start();
        
        $permisos_rol=new PermisosRolesModel();
        $formulas=new FormulasModel();
        $nombre_controladores = "Formulas";
        
        $id_rol= $_SESSION['id_rol'];
        $resultPer = $permisos_rol->getPermisosEditar("   controladores.nombre_controladores = '$nombre_controladores' AND permisos_rol.id_rol = '$id_rol' " );
        
        if (!empty($resultPer))
        {
            $resultado = null;
            
            $formulas=new FormulasModel();
            
            if (isset ($_POST["id_formulas"]) )
            
            {
                
                
                
                $_id_tipo_formulas = $_POST["id_tipo_formulas"];
                $_porcentaje_formulas = $_POST["porcentaje_formulas"];
                
                if($_id_formulas>0)
                {
                    
                    $colval = " id_tipo_formulas = '$_id_tipo_formulas', porcentaje_formulas = '$_porcentaje_formulas'   ";
                    $tabla = "formulas";
                    $where = "porcentaje_formulas = '$_porcentaje_formulas'    ";
                    
                    $resultado=$formulas->UpdateBy($colval, $tabla, $where);
                    
                    
                    
                }else {
                    
                    
                    
                    $funcion = "ins_formulas";
                    $parametros = " '$_id_tipo_formulas','$_porcentaje_formulas'  ";
                    $formulas->setFuncion($funcion);
                    $formulas->setParametros($parametros);
                    $resultado=$formulas->Insert();
                    
                }
                
            }
            $this->redirect("Formulas", "index");
            
        }
        else
        {
            $this->view("Error",array(
                
                "resultado"=>"No tiene Permisos de Insertar Formulas"
                
            ));
            
            
        }
        
    }
    
    public function borrarId()
    {
        
        session_start();
        
        $permisos_rol=new PermisosRolesModel();
        $nombre_controladores = "Formulas";
        $id_rol= $_SESSION['id_rol'];
        $resultPer = $permisos_rol->getPermisosEditar("   controladores.nombre_controladores = '$nombre_controladores' AND permisos_rol.id_rol = '$id_rol' " );
        
        if (!empty($resultPer))
        {
            if(isset($_GET["id_formulas"]))
            {
                $id_formulas=(int)$_GET["id_formulas"];
                
                $formulas=new FormulasModel();
                
                $formulas->deleteBy(" id_formulas");
                
            }
            
            $this->redirect("Formulas", "index");
            
            
        }
        else
        {
            $this->view("Error",array(
                "resultado"=>"No tiene Permisos de Borrar Formulas"
                
            ));
        }
        
    }
    
    
    
    
    
    
    
    
}
?>