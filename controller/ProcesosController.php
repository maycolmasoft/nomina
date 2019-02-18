<?php

class ProcesosController extends ControladorBase{

	public function __construct() {
		parent::__construct();
	}



	public function index(){
	
		//Creamos el objeto usuario
     	$roles=new RolesModel();
					//Conseguimos todos los usuarios
		$resultSet=$roles->getAll("id_rol");
				
		$resultEdit = "";

		
		session_start();

	
		if (isset(  $_SESSION['nombre_usuarios']) )
		{

			$nombre_controladores = "Roles";
			$id_rol= $_SESSION['id_rol'];
			$resultPer = $roles->getPermisosVer("   controladores.nombre_controladores = '$nombre_controladores' AND permisos_rol.id_rol = '$id_rol' " );
			
			if (!empty($resultPer))
			{
				if (isset ($_GET["id_rol"])   )
				{

					$nombre_controladores = "Roles";
					$id_rol= $_SESSION['id_rol'];
					$resultPer = $roles->getPermisosEditar("   controladores.nombre_controladores = '$nombre_controladores' AND permisos_rol.id_rol = '$id_rol' " );
						
					if (!empty($resultPer))
					{
					
						$_id_rol = $_GET["id_rol"];
						$columnas = " id_rol, nombre_rol ";
						$tablas   = "rol";
						$where    = "id_rol = '$_id_rol' "; 
						$id       = "nombre_rol";
							
						$resultEdit = $roles->getCondiciones($columnas ,$tablas ,$where, $id);

					}
					else
					{
						$this->view("Error",array(
								"resultado"=>"No tiene Permisos de Editar Roles"
					
						));
					
					
					}
					
				}
		
				
				$this->view("Procesos",array(
						"resultSet"=>$resultSet, "resultEdit" =>$resultEdit
			
				));
		
				
				
			}
			else
			{
				$this->view("Error",array(
						"resultado"=>"No tiene Permisos de Acceso a Roles"
				
				));
				
				exit();	
			}
				
		}
	else{
       	
       	$this->redirect("Usuarios","sesion_caducada");
       	
       }
	
	}
	
	public function InsertaRoles(){
			
		session_start();
		$roles=new RolesModel();

		$nombre_controladores = "Roles";
		$id_rol= $_SESSION['id_rol'];
		$resultPer = $roles->getPermisosEditar("   controladores.nombre_controladores = '$nombre_controladores' AND permisos_rol.id_rol = '$id_rol' " );
			
		if (!empty($resultPer))
		{
		
		
		
			$resultado = null;
			$roles=new RolesModel();
		
			if (isset ($_POST["nombre_rol"])   )
			{
				
				$_nombre_rol = $_POST["nombre_rol"];
				$_id_rol =  $_POST["id_rol"];
				
				if($_id_rol > 0){
					
					$columnas = " nombre_rol = '$_nombre_rol'";
					$tabla = "rol";
					$where = "id_rol = '$_id_rol'";
					$resultado=$roles->UpdateBy($columnas, $tabla, $where);
					
				}else{
					
					$funcion = "ins_rol";
					$parametros = " '$_nombre_rol'";
					$roles->setFuncion($funcion);
					$roles->setParametros($parametros);
					$resultado=$roles->Insert();
				}
				
				
				
		
			}
			$this->redirect("Roles", "index");

		}
		else
		{
			$this->view("Error",array(
					"resultado"=>"No tiene Permisos de Insertar Roles"
		
			));
		
		
		}
		
	}
	
	public function borrarId()
	{

		session_start();
		$roles=new RolesModel();
		$nombre_controladores = "Roles";
		$id_rol= $_SESSION['id_rol'];
		$resultPer = $roles->getPermisosEditar("   controladores.nombre_controladores = '$nombre_controladores' AND permisos_rol.id_rol = '$id_rol' " );
			
		if (!empty($resultPer))
		{
			if(isset($_GET["id_rol"]))
			{
				$id_rol=(int)$_GET["id_rol"];
		
				
				
				$roles->deleteBy(" id_rol",$id_rol);
				
				
			}
			
			$this->redirect("Roles", "index");
			
			
		}
		else
		{
			$this->view("Error",array(
				"resultado"=>"No tiene Permisos de Borrar Roles"
			
			));
		}
				
	}
	
	
	public function Reporte(){
	
		//Creamos el objeto usuario
		$roles=new RolesModel();
		//Conseguimos todos los usuarios
		
	
	
		session_start();
	
	
		if (isset(  $_SESSION['usuario']) )
		{
			$resultRep = $roles->getByPDF("id_rol, nombre_rol", " nombre_rol != '' ");
			$this->report("Roles",array(	"resultRep"=>$resultRep));
	
		}
					
	
	}
	
	public function inserta_datos(){
		session_start();
		$roles=new RolesModel();
		$nombre_controladores = "Roles";
		$id_rol= $_SESSION['id_rol'];
		$resultPer = $roles->getPermisosEditar("   controladores.nombre_controladores = '$nombre_controladores' AND permisos_rol.id_rol = '$id_rol' " );
			
		if (!empty($resultPer))
		{
			
			$archivo = (isset($_FILES['file_biometrico'])) ? $_FILES['file_biometrico'] : null;
			if ($archivo) {		
				
				$extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
				$extension = strtolower($extension);
				$extension_correcta = ($extension == 'txt');
				if ($extension_correcta) {
					$ruta_destino_archivo = dirname(__FILE__).'\..\view\biometrico\\'.$archivo["name"];
					$archivo_ok = move_uploaded_file($archivo['tmp_name'], $ruta_destino_archivo);
				}

				//para validacion de archivo
				$error = false;
				$detallerespuesta = "";

				if(file_exists($ruta_destino_archivo)){

					$abrir=fopen($ruta_destino_archivo, "r");
 
					if ($abrir) {
					
						$datos=file($ruta_destino_archivo);
					
						$NumFilas=count($datos);
					
						for($n=0;$n<$NumFilas;$n++) {
							$linea=explode(";", $datos[$n]);
							//echo $linea[0];
							$l=$n+1;
							if(is_string($linea[0]) && $this->is_date($linea[1]) ){
								$error = true;
							}else{								
								$error = false;
								$detallerespuesta.="error linea {$l}|";
							}
					
						}
					}

				}
				$detallerespuesta=trim($detallerespuesta,'|');
				//para funcion
				$parametros="";
				$funcion="ins_temp_lectura_biometrico";
				$temp_biometrico = new TempLecturaBiometricoModel();

				if(file_exists($ruta_destino_archivo) && $error){

					$abrir=fopen($ruta_destino_archivo, "r");
 
					if ($abrir) {
					
						$datos=file($ruta_destino_archivo);
					
						$NumFilas=count($datos);
					
						for($n=0;$n<$NumFilas;$n++) {
							$linea=explode(";", $datos[$n]);
							//echo $linea[0];
							$l=$n+1;
							if(is_string($linea[0]) && $this->is_date($linea[1]) ){
								$parametros="'$linea[0]','$linea[1]', '$linea[2]',{$l}";
								$temp_biometrico->setFuncion($funcion);
								$temp_biometrico->setParametros($parametros);
								$resultadotemp = $temp_biometrico->llamafuncion();
							}
					
						}
					}

				}

			}

			$this->View('Procesos', array('error'=>$error, 'detallerespuesta'=>$detallerespuesta));
			
		}
		else
		{
			$this->view("Error",array(
				"resultado"=>"No tiene Permisos de Borrar Roles"
			
			));
		}
		
	}

	public function is_date($date){
		return (bool)preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date);
	}
	
}
?>