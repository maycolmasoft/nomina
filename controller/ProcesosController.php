<?php

class ProcesosController extends ControladorBase{

	public function __construct() {
		parent::__construct();
	}



	public function index(){
	
		
		session_start();

	
		if (isset(  $_SESSION['nombre_usuarios']) )
		{

		    $_biometrico = new LecturaBiometricoModel();
		    
			$nombre_controladores = "LecturasBiometrico";
			$id_rol= $_SESSION['id_rol'];
			$resultPer = $_biometrico->getPermisosVer("   controladores.nombre_controladores = '$nombre_controladores' AND permisos_rol.id_rol = '$id_rol' " );
			
			if (!empty($resultPer))
			{
				
				$this->view("Procesos",array(
						""=>""
				));
				
			}
			else
			{
				$this->view("Error",array(
						"resultado"=>"No tiene Permisos a Cargar Lecturas de Biométrico"
				
				));
				
				exit();	
			}
				
		}
	else{
       	
       	$this->redirect("Usuarios","sesion_caducada");
       	
       }
	
	}
	
	
	
	
	public function inserta_datos(){
	    
	    
	    
	    session_start();
	    $resultado = null;
	    $errores_importacion = new ErroresImportacionModel();
	    $empledos = new EmpleadosModel();
	    $temp_lectura_biometrico = new TempLecturaBiometricoModel();
	    
	    
	    if (isset(  $_SESSION['nombre_usuarios']) )
	    {
	        
	         if ($_FILES['file_biometrico']['tmp_name']!="")
	        {
	            
	            $errores_importacion->deleteAll("1=1");
	            
	           
	            $_id_usuarios= $_SESSION['id_usuarios'];
	            
	            $directorio = $_SERVER['DOCUMENT_ROOT'].'/nomina/view/biometrico/';
	            
	            $nombre = $_FILES['file_biometrico']['name'];
	            $tipo = $_FILES['file_biometrico']['type'];
	            $tamano = $_FILES['file_biometrico']['size'];
	            move_uploaded_file($_FILES['file_biometrico']['tmp_name'],$directorio.$nombre);
	            
	            
	            $lineas = file($directorio.$nombre);
	            $numero_linea=0;
	            $errores=false;
	            $error_encontrado="";
	            
	            if(!empty($lineas)){
	                
	                
	                foreach ($lineas as $linea_num => $linea)
	                {
	                    $numero_linea++;
	                    $error_encontrado="";
	                    $datos = explode("\t",$linea);
	                    
	                    if(count($datos) == 3 && !empty(trim($datos[0])) && !empty(trim($datos[1])) && !empty(trim($datos[2]))){
	                        
	                        $identificacion_empleado              = trim($datos[0]);
	                        $fecha_correspondiente                = trim($datos[1]);
	                        $hora_correspondiente                 = trim($datos[2]);
	                        
	                        
	                    }else{
	                        
	                        $errores=true;
	                        
	                        $error_encontrado="Esta linea no contiene el formato establecido (cedula, fecha, hora), las columnas no estan separadas por tabulaciones.";
	                        
	                        $funcion = "ins_errores_importacion";
	                        $parametros = "'$numero_linea',
							'$error_encontrado',
							'$_id_usuarios'";
	                        $errores_importacion->setFuncion($funcion);
	                        $errores_importacion->setParametros($parametros);
	                        $resultado=$errores_importacion->Insert();
	                        
	                    }
	                    
	                }
	                
	                
	                
	                
	                // cuando pasa la primera validacion de verificar tabulaciones.
	                if($errores==false){
	                    
	                    $numero_linea=0;
	                    
	                    $identificacion_empleado="";
	                    $fecha_correspondiente="";
	                    $hora_correspondiente="";
	                    // AQUI VALIDAMOS QUE LA CEDULA EXISTA REGISTRADA EN LA BASE DE DATOS.
	                    foreach ($lineas as $linea_num => $linea)
	                    {
	                        $numero_linea++;
	                        $error_encontrado="";
	                        $datos = explode("\t",$linea);
	                        
	                        if(count($datos) == 3 && !empty(trim($datos[0])) && !empty(trim($datos[1])) && !empty(trim($datos[2]))){
	                            
	                            $identificacion_empleado              = trim($datos[0]);
	                            $fecha_correspondiente                = trim($datos[1]);
	                            $hora_correspondiente                 = trim($datos[2]);
	                            
	                            
	                            
	                            if(!is_numeric($identificacion_empleado)){
	                                
	                                $errores=true;
	                                
	                                $error_encontrado="La cedula $identificacion_empleado debe ser solo numeros.";
	                                
	                                $funcion = "ins_errores_importacion";
	                                $parametros = "'$numero_linea',
                    								'$error_encontrado',
                    								'$_id_usuarios'";
	                                $errores_importacion->setFuncion($funcion);
	                                $errores_importacion->setParametros($parametros);
	                                $resultado=$errores_importacion->Insert();
	                                
	                            }else{
	                                
	                                
	                                if(strlen($identificacion_empleado)==10 || strlen($identificacion_empleado)==13){
	                                    
	                                }else{
	                                    
	                                    
	                                    $errores=true;
	                                    
	                                    $error_encontrado="La cedula $identificacion_empleado debe estar compuesta de 10 dígitos o 13 dígitos.";
	                                    
	                                    $funcion = "ins_errores_importacion";
	                                    $parametros = "'$numero_linea',
                									'$error_encontrado',
                									'$_id_usuarios'";
	                                    $errores_importacion->setFuncion($funcion);
	                                    $errores_importacion->setParametros($parametros);
	                                    $resultado=$errores_importacion->Insert();
	                                    
	                                }
	                                
	                                
	                                
	                            }
	                            
	                            
	                            $columnas="empleados.identificacion_empleados";
	                            
	                            $tablas ="empleados";
	                            
	                            $where ="empleados.identificacion_empleados= '$identificacion_empleado'";
	                            
	                            $id="empleados.identificacion_empleados";
	                            
	                            $resultEmple=$empledos->getCondiciones($columnas,$tablas,$where,$id);
	                            
	                            
	                            if(!empty($resultEmple)){
	                                
	                                
	                                
	                            }else{
	                                
	                                $errores=true;
	                                
	                                $error_encontrado="La cedula número $identificacion_empleado no existe registrada en la base de datos.";
	                                
	                                $funcion = "ins_errores_importacion";
	                                $parametros = "'$numero_linea',
								    '$error_encontrado',
								    '$_id_usuarios'";
	                                $errores_importacion->setFuncion($funcion);
	                                $errores_importacion->setParametros($parametros);
	                                $resultado=$errores_importacion->Insert();
	                                
	                            }
	                            
	                            
	                            $valores = explode('/', $fecha_correspondiente);
	                            
	                            if(count($valores) == 3){
	                                
	                            }else{
	                                
	                                $errores=true;
	                                
	                                $error_encontrado="La fecha $fecha_correspondiente debe contener el formato YYYY/MM/DD. Separada por (/)";
	                                
	                                $funcion = "ins_errores_importacion";
	                                $parametros = "'$numero_linea',
								                   '$error_encontrado',
								                   '$_id_usuarios'";
	                                $errores_importacion->setFuncion($funcion);
	                                $errores_importacion->setParametros($parametros);
	                                $resultado=$errores_importacion->Insert();
	                            }
	                            
	                          
	                            
	                            if(checkdate($valores[1], $valores[2], $valores[0])){
	                                
	                                
	                            }else{
	                                
	                                $errores=true;
	                                
	                                $error_encontrado="La fecha $fecha_correspondiente no existe en el calendario.";
	                                
	                                $funcion = "ins_errores_importacion";
	                                $parametros = "'$numero_linea',
								                   '$error_encontrado',
								                   '$_id_usuarios'";
	                                $errores_importacion->setFuncion($funcion);
	                                $errores_importacion->setParametros($parametros);
	                                $resultado=$errores_importacion->Insert();
	                            }
	                            
	                            
	                            
	                            $valores1 = explode(':', $hora_correspondiente);
	                            
	                            if(count($valores1) == 3){
	                                
	                            }else{
	                                
	                                $errores=true;
	                                
	                                $error_encontrado="La hora $hora_correspondiente debe contener el formato HH:MM:SS. Separada por (:)";
	                                
	                                $funcion = "ins_errores_importacion";
	                                $parametros = "'$numero_linea',
								                   '$error_encontrado',
								                   '$_id_usuarios'";
	                                $errores_importacion->setFuncion($funcion);
	                                $errores_importacion->setParametros($parametros);
	                                $resultado=$errores_importacion->Insert();
	                            }
	                           
	                            
	                            
	                            if ($valores1[0] < 0 || $valores1[0] > 23 || !is_numeric($valores1[0])) {
	                               
	                                $errores=true;
	                                $error_encontrado="La hora $valores1[0] no existe en el reloj. $hora_correspondiente";
	                                
	                                $funcion = "ins_errores_importacion";
	                                $parametros = "'$numero_linea',
								                   '$error_encontrado',
								                   '$_id_usuarios'";
	                                $errores_importacion->setFuncion($funcion);
	                                $errores_importacion->setParametros($parametros);
	                                $resultado=$errores_importacion->Insert();
	                            }
	                            
	                            
	                            if ($valores1[1] < 0 || $valores1[1] > 59 || !is_numeric($valores1[1])) {
	                                
	                                $errores=true;
	                                $error_encontrado="El minuto $valores1[1] no existe en el reloj. $hora_correspondiente";
	                                
	                                $funcion = "ins_errores_importacion";
	                                $parametros = "'$numero_linea',
								                   '$error_encontrado',
								                   '$_id_usuarios'";
	                                $errores_importacion->setFuncion($funcion);
	                                $errores_importacion->setParametros($parametros);
	                                $resultado=$errores_importacion->Insert();
	                            }
	                            
	                            
	                            if ($valores1[2] < 0 || $valores1[2] > 59 || !is_numeric($valores1[2])) {
	                                
	                                $errores=true;
	                                $error_encontrado="El segundo $valores1[2] no existe en el reloj. $hora_correspondiente";
	                                
	                                $funcion = "ins_errores_importacion";
	                                $parametros = "'$numero_linea',
								                   '$error_encontrado',
								                   '$_id_usuarios'";
	                                $errores_importacion->setFuncion($funcion);
	                                $errores_importacion->setParametros($parametros);
	                                $resultado=$errores_importacion->Insert();
	                            }
	                           
	                            
	                            
	                            
	                        }else{
	                            
	                            $errores=true;
	                            
	                            $error_encontrado="Esta linea no contiene el formato establecido, las columnas no estan separadas por tabulaciones.";
	                            
	                            $funcion = "ins_errores_importacion";
	                            $parametros = "'$numero_linea',
                    							'$error_encontrado',
                    							'$_id_usuarios'";
	                            $errores_importacion->setFuncion($funcion);
	                            $errores_importacion->setParametros($parametros);
	                            $resultado=$errores_importacion->Insert();
	                            
	                        }
	                        
	                        
	                    }
	                     
	                }
	                
	                
	                
	                
	                
	                // POR FIN INSERTO EN EL TEMPORAL
	                
	                if($errores==false){
	                   
	                    
	                    $numero_linea=0;
	                    
	                    $identificacion_empleado="";
	                    $fecha_correspondiente="";
	                    $hora_correspondiente="";
	                    
	                    $funcion = "ins_temp_lectura_biometrico";
	                    
	                    foreach ($lineas as $linea_num => $linea)
	                    {
	                        $numero_linea++;
	                        $error_encontrado="";
	                        $datos = explode("\t",$linea);
	                        
	                        if(count($datos) == 3 && !empty(trim($datos[0])) && !empty(trim($datos[1])) && !empty(trim($datos[2]))){
	                            
	                            $identificacion_empleado              = trim($datos[0]);
	                            $fecha_correspondiente                = trim($datos[1]);
	                            $hora_correspondiente                 = trim($datos[2]);
	                            
	                            
	                            
	                            
	                            $parametros = "'$identificacion_empleado',
							                   '$fecha_correspondiente',
							                   '$hora_correspondiente',
                                               '$numero_linea'";
	                            $temp_lectura_biometrico->setFuncion($funcion);
	                            $temp_lectura_biometrico->setParametros($parametros);
	                            $resultado=$temp_lectura_biometrico->Insert();
	                            
	                            
	                            
	                        }else{
	                            
	                            $errores=true;
	                            
	                            $error_encontrado="Esta linea no contiene el formato establecido (cedula, fecha, hora), las columnas no estan separadas por tabulaciones.";
	                            
	                            $funcion = "ins_errores_importacion";
	                            $parametros = "'$numero_linea',
							               '$error_encontrado',
							               '$_id_usuarios'";
	                            $errores_importacion->setFuncion($funcion);
	                            $errores_importacion->setParametros($parametros);
	                            $resultado=$errores_importacion->Insert();
	                            
	                        }
	                        
	                    }
	                    
	                    
	                    $resultEmpleAct = $empledos->getBy("id_estado=1");
	                    
	                    if(!empty($resultEmpleAct)){
	                        
	                        foreach ($resultEmpleAct as $resEmp){
	                            
	                            $_identificacion_empleados = $resEmp->identificacion_empleados;
	                            $_nombres_empleados = $resEmp->nombres_empleados;
	                            $_apellidos_empleados = $resEmp->nombres_empleados;
	                            
	                            
	                            $columnas_temp="cedula_temp_lectura_biometrico, fecha_temp_lectura_biometrico, count(*) as total";
	                            $tablas_temp="temp_lectura_biometrico";
	                            $where_temp="1=1 AND cedula_temp_lectura_biometrico='$_identificacion_empleados' group by cedula_temp_lectura_biometrico, fecha_temp_lectura_biometrico";
	                            $id_temp="cedula_temp_lectura_biometrico";
	                            
	                            $resultTempGrupo= $temp_lectura_biometrico->getCondiciones($columnas_temp,$tablas_temp,$where_temp,$id_temp);
	                            
	                            if(!empty($resultTempGrupo)){
	                                
	                                
	                                foreach ($resultTempGrupo as $resTempGrupo){
	                                    
	                                    $fecha_correspondiente_temp = $resTempGrupo->fecha_temp_lectura_biometrico;
	                                    
	                                  
	                                    
	                                    $mes_afectacion = substr($fecha_correspondiente_temp, -5, 2);
	                                    
	                                    $timestamp = strtotime( $fecha_correspondiente_temp );
	                                    $dias_afectacion = date( "t", $timestamp);
	                                    
	                                   
	                                 //tengo verificado los dias.
	                                    
	                                }
	                                
	                                
	                            }else{
	                                
	                                $errores=true;
	                                $error_encontrado="No existen Marcaciones del Empleado CI: $_identificacion_empleados Nombres: $_nombres_empleados $_apellidos_empleados";
	                                $funcion = "ins_errores_importacion";
	                                $parametros = "'0',
							               '$error_encontrado',
							               '$_id_usuarios'";
	                                $errores_importacion->setFuncion($funcion);
	                                $errores_importacion->setParametros($parametros);
	                                $resultado=$errores_importacion->Insert();
	                            }
	                            
	                            
	                            
	                        }
	                        
	                    }else{
	                        
	                        $errores=true;
	                        
	                        $error_encontrado="No existen Empleados Activos Para Registrar Marcaciones.";
	                        
	                        $funcion = "ins_errores_importacion";
	                        $parametros = "'0',
							               '$error_encontrado',
							               '$_id_usuarios'";
	                        $errores_importacion->setFuncion($funcion);
	                        $errores_importacion->setParametros($parametros);
	                        $resultado=$errores_importacion->Insert();
	                    }
	                    
	                    
	                    
	                    
	                    // RECUPERO EL TEMPORAL PARA SEGUIR LA VALIDACION
	                   
	                    /*
	                    
	                    $resultTemp=$temp_lectura_biometrico->getAll("cedula_temp_lectura_biometrico, fecha_temp_lectura_biometrico, hora_temp_lectura_biometrico");
	                    
	                    if(!empty($resultTemp)){
	                        
	                        
	                        
	                        
	                        
	                    }
	                    
	                    */
	                    
	                    
	                    
	                    
	                     
	                 }
	                
	                
	                
	                
	                
	                
	                
	                
	                
	                
	                
	                
	                
	                
	            }else{
	                
	                $errores=true;
	                
	                $error_encontrado="El archivo seleccionado no contiene registros, esta vacio.";
	                
	                $funcion = "ins_errores_importacion";
	                $parametros = "'0',
					'$error_encontrado',
					'$_id_usuarios'";
	                $errores_importacion->setFuncion($funcion);
	                $errores_importacion->setParametros($parametros);
	                $resultado=$errores_importacion->Insert();
	                
	            }
	            
	        }
	        
	        
	        $this->redirect("Procesos", "index");
	        
	        
	    }else{
	        
	        $error = TRUE;
	        $mensaje = "Te sesión a caducado, vuelve a iniciar sesión.";
	        
	        $this->view("Login",array(
	            "resultSet"=>"$mensaje", "error"=>$error
	        ));
	        
	        
	        die();
	        
	    }
	    
	    
	}

	
	
	
	public function is_date($date){
		return (bool)preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date);
	}
	
	public function is_time($time)
	{
	    return (bool)preg_match("/^([0-1][0-9]|[2][0-3])[\:]([0-5][0-9])[\:]([0-5][0-9])$/",$time);
	   
	}
	
}
?>