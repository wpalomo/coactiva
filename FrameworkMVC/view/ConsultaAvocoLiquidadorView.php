 <!DOCTYPE HTML>
<html lang="es">

      <head>
      
        <meta charset="utf-8"/>
        <title>Firmar Avoco Liquidador - coactiva 2016</title>
        
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		  			   
          <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	      <script src="//code.jquery.com/jquery-1.10.2.js"></script>
		  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
		
		<link rel="stylesheet" href="http://jqueryvalidation.org/files/demo/site-demos.css">
        <script src="http://jqueryvalidation.org/files/dist/jquery.validate.min.js"></script>
        <script src="http://jqueryvalidation.org/files/dist/additional-methods.min.js"></script>
 		
 		<script src="//cdn.jsdelivr.net/webshim/1.14.5/polyfiller.js"></script>
		
		<script>
		    webshims.setOptions('forms-ext', {types: 'date'});
			webshims.polyfill('forms forms-ext');
		</script>
		
           <!-- AQUI NOTIFICAIONES -->
		<script type="text/javascript" src="view/css/lib/alertify.js"></script>
		<link rel="stylesheet" href="view/css/themes/alertify.core.css" />
		<link rel="stylesheet" href="view/css/themes/alertify.default.css" />
		
		
		
		<script>

		function Ok(){
				alertify.success("Has Pulsado en Reporte"); 
				return false;
			}
			
			function Borrar(){
				alertify.success("Has Pulsado en Borrar"); 
				return false; 
			}

			function notificacion(){
				alertify.success("Has Pulsado en Buscar"); 
				return false; 
			}
		</script>
		
		
		
		<!-- TERMINA NOTIFICAIONES -->
        
       <style>
            input{
                margin-top:5px;
                margin-bottom:5px;
            }
            .right{
                float:right;
            }
                
            
        </style>
         
         
	<script>
	$(document).ready(function(){
			$("#fecha_hasta").change(function(){
				 var startDate = new Date($('#fecha_desde').val());

                 var endDate = new Date($('#fecha_hasta').val());

                 if (startDate > endDate){
 
                    $("#mensaje_fecha_hasta").text("Fecha desde no debe ser mayor ");
		    		$("#mensaje_fecha_hasta").fadeIn("slow"); //Muestra mensaje de error  
		    		$("#fecha_hasta").val("");

                        }
				});

			 $( "#fecha_hasta" ).focus(function() {
				  $("#mensaje_fecha_hasta").fadeOut("slow");
			   });
			});
        </script>
        
  <script>
		$(document).ready(function(){
			
				$("#firmar").click(function(){
	
					  var selected = '';  
			          
				        $('.marcados').each(function(){
				            if (this.checked) {
				                selected +=$(this)+' esta '+$(this).val()+', ';
				            }
				        }); 
	
				        if (selected != '') {
				            return true;
				        }
				        else{
				            alert('Seleccione Documento(s) a firmar.');
				            return false;
				        }
	
				      
				});
		});
   </script>

    </head>
    <body style="background-color: #d9e3e4;">
    
       <?php include("view/modulos/modal.php"); ?>
       <?php include("view/modulos/head.php"); ?>
       <?php include("view/modulos/menu.php"); ?>
       
       <?php
       
       $sel_id_ciudad = "";
       $sel_id_usuarios = "";
       $sel_identificacion="";
       $sel_numero_juicio="";
      
       $sel_fecha_desde="";
       $sel_fecha_hasta="";
       
       if($_SERVER['REQUEST_METHOD']=='POST' )
       {
       
       
       	$sel_id_ciudad = $_POST['id_ciudad'];
       	$sel_id_usuarios = $_POST['id_usuarios'];
       	$sel_identificacion=$_POST['identificacion'];
       	$sel_numero_juicio=$_POST['numero_juicio'];
      
       	$sel_fecha_desde=$_POST['fecha_desde'];
       	$sel_fecha_hasta=$_POST['fecha_hasta'];
       	 
       }
       
     	?>
 
  
  <div class="container">
  
  <div class="row" style="background-color: #ffffff;">
  
       <!-- empieza el form --> 
       
      <form action="<?php echo $helper->url("ConsultaAvocoLiquidador","consulta_liquidador_avoco"); ?>" method="post" enctype="multipart/form-data"  class="col-lg-12">
         
         <!-- comienxza busqueda  -->
         <div class="col-lg-12" style="margin-top: 10px">
         
       	 <h4 style="color:#ec971f;">Firmar Avoco Liquidador</h4>
       	 
       	 
       	 <div class="panel panel-default">
  			<div class="panel-body">
  			
  			
		   			
          <div class="col-xs-2">
			  	<p  class="formulario-subtitulo" style="" >Juzgado:</p>
			  	<select name="id_ciudad" id="id_ciudad"  class="form-control" readonly>
			  		<?php foreach($resultDatos as $res) {?>
						 <option value="<?php echo $res->id_ciudad; ?>" <?php if($sel_id_ciudad==$res->id_ciudad){echo "selected";}?>  ><?php echo $res->nombre_ciudad; ?> </option>
			            <?php } ?>
				</select>
		 </div>
		 
		 <div class="col-xs-2">
			  	<p  class="formulario-subtitulo" style="" >Impulsores:</p>
			  	<select name="id_usuarios" id="id_usuarios"  class="form-control" >
			  	<option value="0">--Todos--</option>
			  		<?php foreach($resultImpul as $res) {?>
						 <option value="<?php echo $res->id_abogado; ?>"<?php if($sel_id_usuarios==$res->id_abogado){echo "selected";}?>  ><?php echo $res->impulsores; ?> </option>
			           
			            <?php } ?>  
			            
				</select>
		 </div>
		 
		 <div class="col-xs-2 ">
			  	<p  class="formulario-subtitulo" >Identificacion:</p>
			  	<input type="text"  name="identificacion" id="identificacion" value="<?php echo $sel_identificacion;?>" class="form-control"/> 
			    <div id="mensaje_identificacion" class="errores"></div>

         </div>
		 
		  <div class="col-xs-2 ">
			  	<p  class="formulario-subtitulo" >Nº Juicio:</p>
			  	<input type="text"  name="numero_juicio" id="numero_juicio" value="<?php echo $sel_numero_juicio;?>" class="form-control"/> 
			    <div id="mensaje_nombres" class="errores"></div>

         </div>
          
         
         <div class="col-xs-2 ">
         		<p class="formulario-subtitulo" >Desde:</p>
			  	<input type="date"  name="fecha_desde" id="fecha_desde" value="<?php echo $sel_fecha_desde;?>" class="form-control "/> 
			    <div id="mensaje_fecha_desde" class="errores"></div>
		</div>
         
          <div class="col-xs-2 ">
          		<p class="formulario-subtitulo" >Hasta:</p>
			  	<input type="date"  name="fecha_hasta" id="fecha_hasta" value="<?php echo $sel_fecha_hasta;?>" class="form-control "/> 
			    <div id="mensaje_fecha_hasta" class="errores"></div>
		</div>
		 
  			</div>
  		<div class="col-lg-12" style="text-align: center; margin-bottom: 20px">
  		
		 <input type="submit" id="buscar" name="buscar" value="Buscar" onClick="notificacion()" class="btn btn-warning " style="margin-top: 10px;"/> 	
		 
		 <?php if(!empty($resultSet))  {?>
		 <input type="submit" value="Firmar" id="firmar" name="firmar" class="btn btn-info" onclick="this.form.action='index.php?controller=ConsultaAvocoLiquidador&action=EnviarApplet'" style="margin-top: 10px;"/>
		         
		  <?php } else {?>
		  
		  <?php } ?>
		 </div>
		</div>
        	
		 </div>
		 
		 
		 <div class="col-lg-12">
		 
	     <div class="col-lg-12">
		 <div class="col-lg-10"></div>
		 <div class="col-lg-2">
		 <span class="form-control"><strong>Registros:</strong><?php if(!empty($resultSet)) echo "  ".count($resultSet);?></span>
		 </div>
		 
		 </div>
		 
		 <div class="col-lg-12">
		 
		 <section class="" style="height:300px;overflow-y:scroll;">
        <table class="table table-hover ">
	         <tr >
	            <th style="color:#456789;font-size:80%;"></th>
	    		<th style="color:#456789;font-size:80%;"><b>Id</b></th>
	    		<th style="color:#456789;font-size:80%;">Nº Juicio Referido</th>
	    		<th style="color:#456789;font-size:80%;">Cliente</th>
	    		<th style="color:#456789;font-size:80%;">Identificacion</th>
	    		<th style="color:#456789;font-size:80%;">Ciudad</th>
	    		<th style="color:#456789;font-size:80%;">Secretario Entrante</th>
	    		<th style="color:#456789;font-size:80%;">Impulsor Entrante</th>
	    		<th style="color:#456789;font-size:80%;">Fecha </th>
	    		
	    		<th></th>
	    		<th></th>
	  		</tr>
            
	            <?php if (!empty($resultSet)) {  foreach($resultSet as $res) {?>
	        		<tr>
	        		   <td> <input type="checkbox" name="file_firmar[]" id="file_firmar" class="marcados" value="<?php echo $res->id_avoco_conocimiento; ?>"/>      </td>
	        		 <td style="color:#000000;font-size:80%;"> <?php echo $res->id_avoco_conocimiento; ?></td>
		               <td style="color:#000000;font-size:80%;"> <?php echo $res->juicio_referido_titulo_credito; ?>     </td> 
		               <td style="color:#000000;font-size:80%;"> <?php echo $res->nombres_clientes; ?>     </td> 
		               <td style="color:#000000;font-size:80%;"> <?php echo $res->identificacion_clientes; ?>     </td> 
		               <td style="color:#000000;font-size:80%;"> <?php echo $res->nombre_ciudad; ?></td> 
		               <td style="color:#000000;font-size:80%;"> <?php echo $res->secretarios; ?>     </td> 
		               <td style="color:#000000;font-size:80%;"> <?php echo $res->impulsores; ?>     </td>
		               <td style="color:#000000;font-size:80%;"> <?php echo $res->creado; ?>     </td> 
		            
		               <td style="color:#000000;font-size:90%;">
		               <a href="<?php echo $helper->url("ConsultaAvocoLiquidador","abrirPdf"); ?>&id=<?php echo $res->id_avoco_conocimiento; ?>" onclick="window.open(this.href, this.target, ' width=1000, height=800, menubar=no');return false" class="btn btn-success" onClick="Ok()" style="font-size:80%;"> VER </a>
		               </td>
		                <td style="color:#000000;font-size:90%;">
		               <a href="<?php echo $helper->url("ConsultaAvocoLiquidador","rechazarPdf"); ?>&id=<?php echo $res->id_avoco_conocimiento; ?>" class="btn btn-danger" onClick="Ok()" style="font-size:80%;">Eliminar</a>
		               </td>  
		    		</tr>
		        <?php } }else {  ?>

                <tr>
	                  	<td></td>
            			<td></td>
            			<td></td>
            			<td></td>
            			<td></td>
            			<td colspan="5" style="color:#ec971f;font-size:8;" style="text-aling:center";> <?php echo '<span id="snResult">NO EXISTE DATOS PARA ESOS FILTROS</span>' ?></td>
	       				
		    	</tr>
		    		
		    		<?php }?>
               
               
       	</table>
       </section>
		</div>
		 </div>
<div class="text-center">	
  <ul class="pagination">
  <li><a href="#">&laquo;</a></li>
  <li><a href="#">1</a></li>
  <li><a href="#">2</a></li>
  <li><a href="#">3</a></li>
  <li><a href="#">4</a></li>
  <li><a href="#">5</a></li>
  <li><a href="#">&raquo;</a></li>
</ul>                       
 </div>


       </form>
     
      </div>
     
  </div>
      <!-- termina
       busqueda  -->
   </body>  

    </html>   