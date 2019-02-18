<!DOCTYPE html>
<html lang="en">
  <head>
  
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Milenio</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    
    
    
   <?php include("view/modulos/links_css.php"); ?>
   
  </head>
   
  <body class="hold-transition skin-blue fixed sidebar-mini">   
  <?php
        
        $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        $fecha=$dias[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y') ;
        ?>
    
    <div class="wrapper">
  		<header class="main-header">
          <?php include("view/modulos/logo.php"); ?>
          <?php include("view/modulos/head.php"); ?>	    
  		</header>
   		<aside class="main-sidebar">
    		<section class="sidebar">
             <?php include("view/modulos/menu_profile.php"); ?>
              <br>
             <?php include("view/modulos/menu.php"); ?>
            </section>
         </aside>

  	  <div class="content-wrapper">
        <section class="content-header">
          <h1>
            
            <small><?php echo $fecha; ?></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="<?php echo $helper->url("Usuarios","Bienvenida"); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Biométrico</li>
          </ol>
        </section>
        
        <section class="content">
          <div class="box box-primary">
            <div class="box-header">
              <h3 class="box-title">Datos Biometrico</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                  <i class="fa fa-minus"></i></button>
                
              </div>
            </div>
            
            <div class="box-body">
            
                <form action="<?php echo $helper->url("Procesos","inserta_datos"); ?>" method="post" enctype="multipart/form-data" class="col-lg-12 col-md-12 col-xs-12">
          		 	               		    
                  <div class="row">
                    <div class="col-xs-12 col-md-5 col-md-5 ">
                      <div class="form-group">
                              <label for="file_biometrico" class="control-label">Seleccione Archivo:</label>                              
                              <input type="file" class="form-control" id="file_biometrico" name="file_biometrico">
                              <div id="mensaje_file_biometrico" class="errores"></div>
                        </div>
                      </div>
                      
                      
                      
                      
                      
                      
                    </div>	
                    		
                    
                  <div class="row">
                      <div class="col-xs-12 col-md-12 col-lg-12" style="text-align: center; margin-top:20px">
                        <div class="form-group">
                          <button type="submit" id="Subir" name="Subir" class="btn btn-success"><i class="glyphicon glyphicon-floppy-saved"> Subir Datos</i></button>
                          <button type="button" id="Cancelar" name="Cancelar" class="btn btn-primary"><i class="glyphicon glyphicon-floppy-remove"> Cancelar</i></button>
                          </div>
                        </div>
                    </div>
                    	           	
                    
          		 	</form>
          
        			</div>
      			</div>
    		</section>
    		
    <!-- seccion para el listado de roles -->
    	<section class="content">
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Resultados</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                  <i class="fa fa-minus"></i></button>                
              </div>
            </div>
        
             <div class="ibox-content">  
              <?php 
                if(isset($error) && !$error){
                  echo '<h2> ERROR EN EL ARCHIVO - archivo no procesado</h2>';
                  $arrayerror = explode("|",$detallerespuesta);
                  for($i=0; $i<count($arrayerror); $i++){

               ?>
                  <p><?php echo $arrayerror[$i]; ?> </p>
                  <?php }} ?>
            	</div>
               </div>
            </section>
  		</div>
  
  
 
 	<?php include("view/modulos/footer.php"); ?>	

   <div class="control-sidebar-bg"></div>
 </div>
    
    
   <?php include("view/modulos/links_js.php"); ?>
    	
    	
    	<script>
		    // cada vez que se cambia el valor del combo
		    $(document).ready(function(){
		    
		    $("#Subir").click(function() 
			{
		    	var regex = /[\w-\.]{2,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;
		    	var validaFecha = /([0-9]{4})\-([0-9]{2})\-([0-9]{2})/;

		    	var file_biometrico = $("#file_biometrico").val();
		    	
		    	 extensiones_permitidas = new Array(".txt"); 
		    	 mierror = ""; 
		    	
		    	if (file_biometrico == "")
		    	{
			    	
		    		$("#mensaje_file_biometrico").text("Seleccione Archivo");
		    		$("#mensaje_file_biometrico").fadeIn("slow"); //Muestra mensaje de error
		            return false;
			    }
		    	else 
		    	{
		    		$("#mensaje_file_biometrico").fadeOut("slow"); //Muestra mensaje de error


		    		if(file_biometrico != ""){ 
			  		      extension = (file_biometrico.substring(file_biometrico.lastIndexOf("."))).toLowerCase(); 
			  		      permitida = false; 
			  		      for (var i = 0; i < extensiones_permitidas.length; i++) { 
			  		         if (extensiones_permitidas[i] == extension) { 
			  		         permitida = true; 
			  		         break; 
			  		         } 
			  		      } 
			  		      if (!permitida) { 
			  		    	  $("#mensaje_file_biometrico").text("Sólo se pueden subir archivos con extensiones: "+ extensiones_permitidas.join());
			  		    	  $("#mensaje_file_biometrico").fadeIn("slow"); //Muestra mensaje de error

			  		    	   return false;

				  			}else{ 
			  			        $("#mensaje_file_biometrico").fadeOut("slow"); //Muestra mensaje de error
			  		      	} 
			  		   }

				}   


		    	
			}); 


		        $( "#file_biometrico" ).focus(function() {
				  $("#mensaje_file_biometrico").fadeOut("slow");
			    });
		        		      
				    
		}); 

	</script>
    	
    	
  </body>
</html>


       
       
      
 




