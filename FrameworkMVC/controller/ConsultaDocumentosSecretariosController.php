<?php
class ConsultaDocumentosSecretariosController extends ControladorBase{
	
	public function __construct() {
		parent::__construct();
	}

	
	public function consulta_secretarios(){

		session_start();

		//Creamos el objeto usuario
		$resultSet="";
		$documentos_secretarios=new DocumentosModel();
		$usuarios = new UsuariosModel();
		// saber la ciudad del usuario
		$_id_usuario= $_SESSION["id_usuarios"]; 
		
		$columnas = " usuarios.id_ciudad, 
					  ciudad.nombre_ciudad, 
					  usuarios.nombre_usuarios";
			
		$tablas   = "public.usuarios, 
                     public.ciudad";
			
		$where    = "ciudad.id_ciudad = usuarios.id_ciudad AND usuarios.id_usuarios = '$_id_usuario'";
			
		$id       = "usuarios.id_ciudad";
		$resultDatos=$usuarios->getCondiciones($columnas ,$tablas ,$where, $id);
		
		
		// saber los impulsores del secretario
		$_id_usuarios= $_SESSION["id_usuarios"];
		
		$columnas = " asignacion_secretarios_view.id_abogado,
					  asignacion_secretarios_view.impulsores";
			
		$tablas   = "public.asignacion_secretarios_view";
			
		$where    = "public.asignacion_secretarios_view.id_secretario = '$_id_usuarios'";
			
		$id       = "asignacion_secretarios_view.id_abogado";
		$resultImpul=$documentos_secretarios->getCondiciones($columnas ,$tablas ,$where, $id);
		
		
		$ciudad = new CiudadModel();
		$resultCiu = $ciudad->getAll("nombre_ciudad");
		
		
		$usuarios = new UsuariosModel();
		$resultUsu = $usuarios->getAll("nombre_usuarios");
		

		$documentos_secretarios=new DocumentosModel();


		if (isset(  $_SESSION['usuario_usuarios']) )
		{
			//notificaciones
			$documentos_secretarios->MostrarNotificaciones($_SESSION['id_usuarios']);
			
			$permisos_rol = new PermisosRolesModel();
			$nombre_controladores = "ConsultaDocumentosSecretarios";
			$id_rol= $_SESSION['id_rol'];
			$resultPer = $documentos_secretarios->getPermisosVer("   controladores.nombre_controladores = '$nombre_controladores' AND permisos_rol.id_rol = '$id_rol' " );

			if (!empty($resultPer))
			{
					
				if(isset($_POST["buscar"])){

					$id_ciudad=$_POST['id_ciudad'];
					$id_usuarios=$_POST['id_usuarios'];
					$identificacion=$_POST['identificacion'];
					$numero_juicio=$_POST['numero_juicio'];
					$fechadesde=$_POST['fecha_desde'];
					$fechahasta=$_POST['fecha_hasta'];

					$documentos_secretarios=new DocumentosModel();


					$columnas = "documentos.id_documentos, 
								  ciudad.nombre_ciudad, 
								  juicios.juicio_referido_titulo_credito, 
								  clientes.nombres_clientes, 
								  clientes.identificacion_clientes, 
								  estados_procesales_juicios.nombre_estados_procesales_juicios, 
								  documentos.fecha_emision_documentos, 
								  documentos.hora_emision_documentos, 
								  documentos.detalle_documentos, 
								  documentos.observacion_documentos, 
								  documentos.avoco_vistos_documentos, 
								  asignacion_secretarios_view.impulsores,
  					              asignacion_secretarios_view.secretarios";

					$tablas=" public.documentos, 
							  public.ciudad, 
							  public.juicios, 
							  public.asignacion_secretarios_view, 
							  public.clientes, 
							  public.estados_procesales_juicios";

					$where="ciudad.id_ciudad = documentos.id_ciudad AND
						  juicios.id_juicios = documentos.id_juicio AND
						  documentos.id_usuario_registra_documentos = asignacion_secretarios_view.id_abogado AND
						  clientes.id_clientes = juicios.id_clientes AND
						  estados_procesales_juicios.id_estados_procesales_juicios = documentos.id_estados_procesales_juicios
						 AND documentos.firma_impulsor ='TRUE' AND documentos.firma_secretario ='FALSE' AND asignacion_secretarios_view.id_secretario='$_id_usuarios'";
					
					$id="documentos.id_documentos";
						
						
					$where_0 = "";
					$where_1 = "";
					$where_2 = "";
					$where_3 = "";
					$where_4 = "";


					if($id_ciudad!=0){$where_0=" AND ciudad.id_ciudad='$id_ciudad'";}
					
					if($id_usuarios!=0){$where_1=" AND asignacion_secretarios_view.id_abogado='$id_usuarios'";}
						
					if($identificacion!=""){$where_2=" AND clientes.identificacion_clientes='$identificacion'";}
						
					if($numero_juicio!=""){$where_3=" AND juicios.juicio_referido_titulo_credito='$numero_juicio'";}
					
					if($fechadesde!="" && $fechahasta!=""){$where_4=" AND  documentos.fecha_emision_documentos BETWEEN '$fechadesde' AND '$fechahasta'";}

                    $where_to  = $where . $where_0 . $where_1 . $where_2. $where_3 . $where_4;
                    $resultSet=$documentos_secretarios->getCondiciones($columnas ,$tablas , $where_to, $id);


				}
				
				if(isset($_POST['firmar']))
				{
					/*
					$firmas= new FirmasDigitalesModel();
					$documentos = new DocumentosModel();
					$tipo_notificacion = new TipoNotificacionModel();
					$asignacion_secreatario= new AsignacionSecretariosModel();
					
					$ruta="";
					$nombrePdf="";
					
					$destino = $_SERVER['DOCUMENT_ROOT'].'/documentos/';
					
					$array_documento=$_POST['file_firmar'];
					
					$permisosFirmar=$permisos_rol->getPermisosFirmarPdfs($_id_usuarios);
					
					//para las notificaciones 
					$_nombre_tipo_notificacion="documentos";					
					$resul_tipo_notificacion=$tipo_notificacion->getBy("descripcion_notificacion='$_nombre_tipo_notificacion'");						
					$id_tipo_notificacion=$resul_tipo_notificacion[0]->id_tipo_notificacion;					
					$descripcion="Documento Firmado por";
					$numero_movimiento=0;
					$id_impulsor="";
					
					if($permisosFirmar['estado'])
					{
						
						$id_firma = $permisosFirmar['valor'];
						
						
						foreach ($array_documento as $id )
						{
														
							if(!empty($id))
							{
								
								$id_documento = $id;
								
								$resultDocumento=$documentos->getBy("id_documentos='$id'");
								
								$nombrePdf=$resultDocumento[0]->nombre_documento;
								
								$nombrePdf=$nombrePdf.".pdf";
								
								$ruta=$resultDocumento[0]->ruta_documento;
				
								$id_rol=$_SESSION['id_rol'];
								
								$destino.=$ruta.'/';
								
								
								try {
									
									$res=$firmas->FirmarPDFs( $destino, $nombrePdf, $id_firma,$id_rol);
									
									$firmas->UpdateBy("firma_secretario='TRUE'", "documentos", "id_documentos='$id_documento'");
									
									
									//dirigir notificacion
									$usuarioDestino=$resultDocumento[0]->id_usuario_registra_documentos;
									
									$result_notificaciones=$firmas->CrearNotificacion($id_tipo_notificacion, $usuarioDestino, $descripcion, $numero_movimiento, $nombrePdf);
											
									
									
																		
								} catch (Exception $e) {
									
									echo $e->getMessage();
								}
								
							}
						}
					}*/
				}




				$this->view("ConsultaDocumentosSecretarios",array(
						"resultSet"=>$resultSet,"resultCiu"=>$resultCiu, "resultUsu"=>$resultUsu, "resultDatos"=>$resultDatos, "resultImpul"=>$resultImpul
							
				));



			}
			else
			{
				$this->view("Error",array(
						"resultado"=>"No tiene Permisos de Acceso a Firmar Documentos Secretarios"

				));

				exit();
			}

		}
		else
		{
			$this->view("ErrorSesion",array(
					"resultSet"=>""

			));

		}

	}


	
	public function consulta_secretarios_firmados(){
	
		session_start();
	
		//Creamos el objeto usuario
		$resultSet="";
		
		$documentos_secretarios=new DocumentosModel();
		$usuarios = new UsuariosModel();
		// saber la ciudad del usuario
		$_id_usuario= $_SESSION["id_usuarios"];
	
		$columnas = " usuarios.id_ciudad,
					  ciudad.nombre_ciudad,
					  usuarios.nombre_usuarios";
			
		$tablas   = "public.usuarios,
                     public.ciudad";
			
		$where    = "ciudad.id_ciudad = usuarios.id_ciudad AND usuarios.id_usuarios = '$_id_usuario'";
			
		$id       = "usuarios.id_ciudad";
		$resultDatos=$usuarios->getCondiciones($columnas ,$tablas ,$where, $id);
	
	
		// saber los impulsores del secretario
		$_id_usuarios= $_SESSION["id_usuarios"];
	
		$columnas = " asignacion_secretarios_view.id_abogado,
					  asignacion_secretarios_view.impulsores";
			
		$tablas   = "public.asignacion_secretarios_view";
			
		$where    = "public.asignacion_secretarios_view.id_secretario = '$_id_usuarios'";
			
		$id       = "asignacion_secretarios_view.id_abogado";
		$resultImpul=$documentos_secretarios->getCondiciones($columnas ,$tablas ,$where, $id);
	
	
		$ciudad = new CiudadModel();
		$resultCiu = $ciudad->getAll("nombre_ciudad");
	
	
		$usuarios = new UsuariosModel();
		$resultUsu = $usuarios->getAll("nombre_usuarios");
	
	
		$documentos_secretarios=new DocumentosModel();
	
	
		if (isset(  $_SESSION['usuario_usuarios']) )
		{
			$permisos_rol = new PermisosRolesModel();
			$nombre_controladores = "ConsultaDocumentosSecretarios";
			$id_rol= $_SESSION['id_rol'];
			$resultPer = $documentos_secretarios->getPermisosVer("   controladores.nombre_controladores = '$nombre_controladores' AND permisos_rol.id_rol = '$id_rol' " );
	
			if (!empty($resultPer))
			{
					
				if(isset($_POST["buscar"])){
	
					$id_ciudad=$_POST['id_ciudad'];
					$id_usuarios=$_POST['id_usuarios'];
					$identificacion=$_POST['identificacion'];
					$numero_juicio=$_POST['numero_juicio'];
					$fechadesde=$_POST['fecha_desde'];
					$fechahasta=$_POST['fecha_hasta'];
	
					$documentos_secretarios=new DocumentosModel();
	
	
					$columnas = "documentos.id_documentos,
								  ciudad.nombre_ciudad,
							      juicios.id_juicios,
								  juicios.juicio_referido_titulo_credito,
							      clientes.id_clientes,
								  clientes.nombres_clientes,
								  clientes.identificacion_clientes,
								  estados_procesales_juicios.nombre_estados_procesales_juicios,
								  documentos.fecha_emision_documentos,
								  documentos.hora_emision_documentos,
								  documentos.detalle_documentos,
								  documentos.observacion_documentos,
								  documentos.avoco_vistos_documentos,
								  asignacion_secretarios_view.impulsores,
  					              asignacion_secretarios_view.secretarios";
	
					$tablas=" public.documentos,
							  public.ciudad,
							  public.juicios,
							  public.asignacion_secretarios_view,
							  public.clientes,
							  public.estados_procesales_juicios";
	
					$where="ciudad.id_ciudad = documentos.id_ciudad AND
						  juicios.id_juicios = documentos.id_juicio AND
						 documentos.id_usuario_registra_documentos = asignacion_secretarios_view.id_abogado AND
						  clientes.id_clientes = juicios.id_clientes AND
						  estados_procesales_juicios.id_estados_procesales_juicios = documentos.id_estados_procesales_juicios
						 AND documentos.firma_impulsor ='TRUE' AND documentos.firma_secretario ='TRUE' AND asignacion_secretarios_view.id_secretario='$_id_usuarios'";
	
					$id="documentos.id_documentos";
	
	
					$where_0 = "";
					$where_1 = "";
					$where_2 = "";
					$where_3 = "";
					$where_4 = "";
	
	
					if($id_ciudad!=0){$where_0=" AND ciudad.id_ciudad='$id_ciudad'";}
						
					if($id_usuarios!=0){$where_1=" AND asignacion_secretarios_view.id_abogado='$id_usuarios'";}
	
					if($identificacion!=""){$where_2=" AND clientes.identificacion_clientes='$identificacion'";}
	
					if($numero_juicio!=""){$where_3=" AND juicios.juicio_referido_titulo_credito='$numero_juicio'";}
						
					if($fechadesde!="" && $fechahasta!=""){$where_4=" AND  documentos.fecha_emision_documentos BETWEEN '$fechadesde' AND '$fechahasta'";}
	
	
					$where_to  = $where . $where_0 . $where_1 . $where_2. $where_3 . $where_4;
	
	
					$resultSet=$documentos_secretarios->getCondiciones($columnas ,$tablas , $where_to, $id);
	
	         }
	
				$this->view("ConsultaDocumentosSecretariosFirmados",array(
						"resultSet"=>$resultSet,"resultCiu"=>$resultCiu, "resultUsu"=>$resultUsu, "resultDatos"=>$resultDatos, "resultImpul"=>$resultImpul
							
				));
	
	         }
			else
			{
				$this->view("Error",array(
						"resultado"=>"No tiene Permisos de Acceso a Consulta Documentos Secretarios Firmados"
	
				));
	
				exit();
			}
	
		}
		else
		{
			$this->view("ErrorSesion",array(
					"resultSet"=>""
	
			));
	
		}
	
	}

	
	public function abrirPdf()
	{
		$documentos = new DocumentosModel();
		
		if(isset($_GET['id']))
		{
			
			$id_documento = $_GET ['id'];
			
			$resultDocumento = $documentos->getBy ( "id_documentos='$id_documento'" );
			
			if (! empty ( $resultDocumento )) {
				
				$nombrePdf = $resultDocumento [0]->nombre_documento;
				
				$nombrePdf .= ".pdf";
				
				$ruta = $resultDocumento [0]->ruta_documento;
				
				$directorio = $_SERVER ['DOCUMENT_ROOT'] . '/documentos/' . $ruta . '/' . $nombrePdf;
				
				header('Content-type: application/pdf');
				header('Content-Disposition: inline; filename="'.$directorio.'"');
				readfile($directorio);
			}
		
		
		}
		
	
		
	}
	
	public function rechazarPdf()
	{
		session_start();
		
		$documentos = new DocumentosModel();

		$firmas= new FirmasDigitalesModel();
		$documentos = new DocumentosModel();
		$tipo_notificacion = new TipoNotificacionModel();
		$asignacion_secreatario= new AsignacionSecretariosModel();
	
			
		//para las notificaciones
		$_nombre_tipo_notificacion="rechazado";
		$resul_tipo_notificacion=$tipo_notificacion->getBy("descripcion_notificacion='$_nombre_tipo_notificacion'");
		$id_tipo_notificacion=$resul_tipo_notificacion[0]->id_tipo_notificacion;
		$descripcion="Documento Rechazo por";
		$numero_movimiento=0;
		$id_impulsor="";
	
		if(isset($_GET['id']))
		{
	
			$id_documento = $_GET ['id'];
			$resultDocumento = $documentos->getBy ( "id_documentos='$id_documento'" );
				
				
			if (! empty ( $resultDocumento )) {
	
				$nombrePdf = $resultDocumento [0]->nombre_documento;
	
				$nombrePdf .= ".pdf";
	
				$ruta = $resultDocumento [0]->ruta_documento;
	
				$directorio = $_SERVER ['DOCUMENT_ROOT'] . '/documentos/' . $ruta . '/' . $nombrePdf;
	
	
				try {
						
					$eliminado=unlink($directorio);
					$resultDocumento=$documentos->deleteById("id_documentos='$id_documento'");
					
					
					//dirigir notificacion
					$usuarioDestino=$resultDocumento[0]->id_usuario_registra_documentos;
						
					$result_notificaciones=$firmas->CrearNotificacion($id_tipo_notificacion, $usuarioDestino, $descripcion, $numero_movimiento, $nombrePdf);
										
					//$this->view("Error", array("resultado"=>"no se elimino el archivo <br>".print_r($result_notificaciones)));
					//$this->redirect("ConsultaDocumentosSecretarios","consulta_secretarios");
					
				} catch (Exception $e)
				{
					$this->view("Error", array("resultado"=>"no se elimino el archivo <br>".$e->getMessage()));
				}
					
			}
	
	
		}
	
	
	}

//129;681;19;4;"2016-07-22";"02:11:00";"DSFSD";"FSDFSD";" SDFSD";41;FALSE;FALSE;FALSE;"Providencias1065";"Providencias";"2016-07-21 19:10:34.858-05";""

	public  function EnviarApplet()
	{
		//pasar parametros
	
		session_start();
	
		$consulta = array();
	
		$resultUsuario="";
		$resultnombreFiles="";
		$ruta="";
		$resultIds="";
		
		$documentos= new DocumentosModel();
	
		if(isset($_POST['file_firmar']))
		{
			$resultUsuario=$_SESSION['id_usuarios'];
	
			$arrayFilesAfirmar=$_POST['file_firmar'];
			$cadenaFiles="";
			$cadenaId="";
			$ruta="Providencias";
			$nombreUsuario="";
			$controlador="ConsultaDocumentosSecretarios";
			$accion="FirmarProvidenciasApplet";
	
			foreach ($arrayFilesAfirmar as $res)
			{
				$cadenaId.=$res.",";
			}
	
			//$cadenaFiles = substr($cadenaFiles, 0, -1);
			//$cadenaId = substr($cadenaId, 0, -1);
	
			$resultIds = trim($cadenaId,",");
	
			$consulta=$documentos->getBy("id_documentos in ('$resultIds')");
	
			if (!empty($consulta)) {  foreach($consulta as $res) {
	
				$cadenaFiles.=$res->nombre_documento;
			}
			}
	
			$resultnombreFiles = trim($cadenaFiles,",");
	
			$certificado=new CertificadosModel();
			$firma = new FirmasDigitalesModel();
	
			$msg="";
	
			$conCertificado= $certificado->getBy("id_usuarios_certificado_digital='$resultUsuario'");
	
			$conFirma=$firma->getBy("id_usuarios='$resultUsuario'");
	
			if(empty($conCertificado)){$msg="Usted no tiene registrado un Certfificado electronico";}
	
			if(empty($conFirma)){$msg="Usted no cuenta con una firma digital registrado en el sistema";}
	
			$nombreUsuario=$_SESSION['nombre_usuarios'];
	
	
			$this->view("FirmarPdf",array(
	
					"resultUsuario"=>$resultUsuario,"resultnombreFiles"=>$resultnombreFiles,"ruta"=>$ruta,"resultIds"=>$resultIds,"msg"=>$msg,"nombreUsuario"=>$nombreUsuario,"controlador"=>$controlador,"accion"=>$accion
	
			));
	
	
			/*$this->view("Error",array(
	
			"resultado"=>$resultUsuario."resultnombreFiles".$resultnombreFiles."ruta".$ruta."resultIds".$resultIds."msg".$msg."nombreUsuario".$nombreUsuario
	
	
			));*/
	
	
	
		}else {
	
			$this->view("Error",array(
	
					"resultado"=>"no hay archivos"
	
			));
		}
	
	}
	
	//metodo utilizado por el applet para firmar avoco
	public  function FirmarProvidenciasApplet()
	{
		session_start();
	
		if(isset($_POST['filesIds'])&&isset($_POST['mac'])&&isset($_POST['ruta'])&&isset($_POST['id_usuario']))
		{
			if(!is_null($_POST['filesIds']) || !is_null($_POST['mac']) || !is_null($_POST['ruta']) || !is_null($_POST['id_usuario'])){
	
				$rutaXfirmar=$_POST['ruta'];
				$macCliente=$_POST['mac'];
				$idsFiles=$_POST['filesIds'];
				$id_usuario=$_POST['id_usuario'];
	
				$user = new UsuariosModel();
				$permisosFirmar=$user->getPermisosFirmarPdfs($id_usuario,$macCliente);
	
				//para obtener rol de usuario
				$consultaUsuarios=$user->getCondiciones("id_rol", "usuarios", "id_usuarios='$id_usuario'", "id_rol");
				$id_rol=$consultaUsuarios[0]->id_rol;
	
				//para las notificaciones
				$tipo_notificacion = new TipoNotificacionModel();
				$asignacion_secretario= new AsignacionSecretariosModel();
				$_nombre_tipo_notificacion="providencia_secretario";
				$descripcion="Providencia Firmada por";
				$numero_movimiento=0;
				$id_impulsor="";
				$respuestaCliente="";
				$resul_tipo_notificacion=$tipo_notificacion->getBy("descripcion_notificacion='$_nombre_tipo_notificacion'");
				$id_tipo_notificacion=$resul_tipo_notificacion[0]->id_tipo_notificacion;
	
				//saber si tiene permiso para firmar
	
				if($permisosFirmar['estado'])
				{
					$id_firma = $permisosFirmar['valor'];
	
					$cantidadFirmados=0;
					$consultaUsuarios=null;
	
					$firmas= new FirmasDigitalesModel();
					$documentos= new DocumentosModel();
	
					$_id_usuarios=$id_usuario;
					//$_ruta=$rutaFiles;
					$_id_documentos=$idsFiles;
					$_nombreDocumentos="";
	
					$destino = $_SERVER['DOCUMENT_ROOT'].'/documentos/';
	
	
	
					$array_documento = explode(",", $_id_documentos);
					$respuestaCliente="Documentos firmados (";
	
					foreach ($array_documento as $id )
					{
	
	
						if(!empty($id))
						{
							$cantidadFirmados=$cantidadFirmados+1;
	
							$id_providencias = $id;
	
							$resultDocumento=$documentos->getBy("id_documentos='$id_providencias'");
	
							$nombrePdf=$resultDocumento[0]->nombre_documento;
	
							$nombrePdf=$nombrePdf.".pdf";
	
							$_ruta=$resultDocumento[0]->ruta_documento;
	
							//para metodo dentro del farmework
							//$id_rol=$_SESSION['id_rol'];
	
							$destino.=$_ruta.'/';
	
							try {
									
								$res=$firmas->FirmarPDFs( $destino, $nombrePdf, $id_firma,$id_rol,$_id_usuarios);
	
								$firmas->UpdateBy("firma_secretario='TRUE'", "documentos", "id_documentos='$id_providencias'");
	
								//crear notificacion usa variable de session
								//$this->notificacionImpulsor($nombrePdf);
	
							} catch (Exception $e) {
									
								$respuestaCliente= $e->getMessage();
							}
	
	
						}
	
	
					}
	
					$respuestaCliente.=$cantidadFirmados.")";
	
	
	
				}else {
	
					$traza=new TrazasModel();
					$_nombre_controlador = "Firmas Digitales";
					$_accion_trazas  = "Se intento Firmar desde ";
					$_parametros_trazas = $macCliente;
					$resultado = $traza->AuditoriaControladores($_accion_trazas, $_parametros_trazas, $_nombre_controlador,$id_usuario);
	
					$respuestaCliente=$permisosFirmar['error'];
	
				}
	
				echo $respuestaCliente;
	
	
			}else{
	
				echo 'error en el envio de datos';
			}
	
		}else{
			echo 'error sus Datos no han sido enviados';
		}
	
	}
	

}
?>