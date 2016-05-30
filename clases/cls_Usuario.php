 <?php 
session_start(); 
include_once('cls_Conexion.php');
class cls_Usuario extends cls_Conexion{
	
	private $aa_Atributos = array();
	private $aa_Campos = array('nombre','clave','cedula','correo','session_abierta','estado');

	public function setPeticion($pa_Peticion){
		$this->aa_Atributos=$pa_Peticion;
	}

	public function getAtributos(){
		return $this->aa_Atributos;
	}

	public function gestionar(){
		switch ($this->aa_Atributos['operacion']) {
			case 'buscar':
				$registros=$this->f_Listar();
				if(count($registros)!=0){
					$success=1;
					$respuesta['registros']=$registros;
				}
				break;

			case 'buscarRegistro':
				$lb_Enc=$this->f_Buscar();
				if($lb_Enc){
					$respuesta['registros']=$this->aa_Atributos['registro'];
					$success=1;
				}
				break;
			
			case 'guardar':
				$lb_Hecho=$this->f_Guardar();
				if($lb_Hecho){
					$this->f_Buscar();
					$respuesta['registros'] = $this->aa_Atributos['registro'];
					$respuesta['mensaje'] = 'Insercion realizada con exito';
					$success = 1;
				}else{
					$respuesta['mensaje'] = 'Error al ejecutar la insercion';
					$success = 0;
				}
				break;

			case 'modificar':
				$respuesta = $this->f_Modificar();
				break;

			case 'cambiarEstado':
				$respuesta = $this->f_CambiarEstado();
				if($respuesta['success']==1){
					$respuesta['tipo']='informacion';
				}else{
					$respuesta['tipo']='error';
				}
				break;
				
			case 'reactivarClave':
				$respuesta = $this->f_ReactivarClave();
				if($respuesta['success']==1){
					$respuesta['tipo']='informacion';
				}else{
					$respuesta['tipo']='error';
				}
				break;
				
			default:
				$respuesta['mensaje'] = 'Operacion "'.strtoupper($this->aa_Atributos['operacion']).'" no existe para esta entidad';
				$success = 0;
				break;
		}
		if(!isset($respuesta['success'])){
			$respuesta['success']=$success;
		}	
		return $respuesta;
	}
	private function f_Listar(){
		$x=0;
		$la_respuesta=array();
		$ls_Sql="SELECT * FROM seguridad.usuario ";
		$this->f_Con();
		$lr_tabla=$this->f_Filtro($ls_Sql);
		while($la_registros=$this->f_Arreglo($lr_tabla)){
			$la_respuesta[$x]['codigo']=$la_registros['nombre'];
			$la_respuesta[$x]['nombre']=$la_registros['nombre'];
			$x++;
		}
		$this->f_Cierra($lr_tabla);
		$this->f_Des();
		return $la_respuesta;
	}

	private function f_Buscar(){
		$lb_Enc=false;
		//Busco El rol
		$ls_Sql="SELECT * FROM seguridad.usuario where nombre='".$this->aa_Atributos['codigo']."'";
		$this->f_Con();
		$lr_tabla=$this->f_Filtro($ls_Sql);
		if($la_registros=$this->f_Arreglo($lr_tabla)){
			$la_respuesta['codigo']=$la_registros['nombre'];
			$la_respuesta['nombre']=$la_registros['nombre'];
			$la_respuesta['cedula']=$la_registros['cedula'];
			$la_respuesta['correo']=$la_registros['correo'];
			$la_respuesta['estado']=$la_registros['estado'];
			$la_respuesta['tipoUsuario']=$la_registros['cod_tip_usu'];
			$lb_Enc=true;
		}
		$this->f_Cierra($lr_tabla);
		$this->f_Des();

		if($lb_Enc){
			//guardo en atributo de la clase
			$this->aa_Atributos['registro']=$la_respuesta;
		}

		return $lb_Enc;
	}
	
	private function f_Guardar(){
		//encripto la contraseña
		include_once('cls_acceso.php');
		$lobj_Acceso = new cls_acceso;
		$this->aa_Atributos['clave'] = $lobj_Acceso->encriptarPass($this->aa_Atributos['clave']);

		$lb_Hecho=false;
		$ls_Sql="INSERT INTO seguridad.usuario (nombre,correo,cedula,clave,cod_tip_usu,estado) values 
				('".$this->aa_Atributos['codigo']."','".$this->aa_Atributos['correo']."','".$this->aa_Atributos['cedula']."',
				'".$this->aa_Atributos['clave']."','".$this->aa_Atributos['tipousuario']."','".$this->aa_Atributos['estado']."')";
		$this->f_Con();
		$lb_Hecho=$this->f_Ejecutar($ls_Sql);
		$this->f_Des();
		return $lb_Hecho;
	}

	private function f_Modificar(){
		$lb_Hecho=false;
		$contCampos = 0;
		$ls_Sql="UPDATE seguridad.usuario SET ";

		//arma la cadena sql en base a los campos pasados en la peticion
		$ls_Sql.=$this->armarCamposUpdate($this->aa_Campos,$this->aa_Atributos);

		$ls_Sql.="WHERE nombre ='".$this->aa_Atributos['codigo']."'";
		$this->f_Con();
		$lb_Hecho=$this->f_Ejecutar($ls_Sql);
		$this->f_Des();


		if($lb_Hecho){
			$this->f_Buscar();
			$respuesta['registro'] = $this->aa_Atributos['registro'];
			$respuesta['success'] = 1;
			$respuesta['mensaje'] = 'Modificacion Realizada Exitosamente';
			$respuesta['tipo'] = 'informacion';
			$respuesta['titulo'] = 'Informacion';
		}else{
			$respuesta['success'] = 0;
			$respuesta['mensaje'] = 'Error al Realizar la Modificacion';
			$respuesta['tipo'] = 'Error';
			$respuesta['titulo'] = 'Error';	
		}
		return $respuesta;
	}

	private function f_CambiarEstado(){
		//en un futuro cuando este la bitacora aqui se guardara primero que fue un cambio de estado
		//para diferenciarlo de un simple modificar
		return $this->f_Modificar();

	}

	private function f_ReactivarClave(){
		/*cuando exista la bitacora de acceso y el control de acceso full aqui estara el codigo de
		donde :
			1.- se registrara en la bitacora deacceso el cambio realizado
			2.- se reiniciara los periodos de caducidad de clave
			3.- se reiniciaran los intentos fallidos de inicio de sesion
		*/
		include_once('cls_acceso.php');
		$lobj_Acceso = new cls_acceso;
		$la_peticion = array('Pass' => $this->aa_Atributos['clave'],'Nombre' => $_SESSION['Usuario']['Nombre']);
		$lobj_Acceso->setForm($la_peticion);
		$lb_Enc = $lobj_Acceso->f_VerificarAcceso();
		if($lb_Enc){
			$this->aa_Atributos['clave'] = $lobj_Acceso->encriptarPass($this->aa_Atributos['codigo']);
			$respuesta = $this->f_Modificar();
			$respuesta['titulo'] = 'Informacion';
		}else{
			$respuesta['tipo'] = 'error'; 
			$respuesta['mensaje'] = 'Clave Suministrada Incorrecta';
			$respuesta['success'] = 0;
			$respuesta['titulo'] = 'Error';
		}
		return $respuesta;
	}	
}
?>