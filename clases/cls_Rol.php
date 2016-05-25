<?php 
include('cls_Conexion.php');
class cls_Rol extends cls_Conexion{
	
	private $aa_Atributos = array();

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
				$lb_Enc=$this->f_buscar();
				if($lb_Enc){
					$respuesta['registros'] = $this->aa_Atributos['registro'];
					$success=1;
				}
				break;

			case 'guardarDetalle':
				$respuesta=$this->guardarDetalle();
				if($respuesta!=false){
					$respuesta['registro'] = $respuesta;
					$success=1;
				}
			break;

			case 'eliminarDetalle':
				$lb_Hecho=$this->eliminarDetalle();
				if($lb_Hecho){
					$respuesta['mensaje'] = 'Eliminacion realizada con exito';
					$respuesta['cod_emp'] = $this->aa_Atributos['cod_emp'];
					$success=1;
				}
			break;

			default:
				$respuesta['mensaje'] = 'Operacion "'.strtoupper($this->aa_Atributos['operacion']).'" no existe para esta entidad';
				$success = 0;
				break;
		}
		
		$respuesta['success']=$success;
		
		return $respuesta;
	}
	
	private function f_Buscar(){
		$lb_Enc=false;
		//Busco El rol
		$ls_Sql="SELECT * FROM seguridad.rol where cod_rol='".$this->aa_Atributos['codigo']."'";
		$this->f_Con();
		$lr_tabla=$this->f_Filtro($ls_Sql);
		if($la_registros=$this->f_Arreglo($lr_tabla)){
			$la_respuesta['codigo']=$la_registros['cod_rol'];
			$la_respuesta['nombre']=$la_registros['nom'];
			$la_respuesta['descripcion']=$la_registros['desc'];
			$lb_Enc=true;
		}
		$this->f_Cierra($lr_tabla);
		$this->f_Des();

		if($lb_Enc){
			//guardo en atributo de la clase
			$this->aa_Atributos['registro']=$la_respuesta;
				
			//guardo en detalle
			$this->aa_Atributos['registro']['detalle']=$this->f_Buscar_Detalle();
		}

		return $lb_Enc;
	}

	private function f_Buscar_Detalle(){

		//Busco Detalle
		$ls_Sql="SELECT e.*,re.cod_rol_emp as codigoRelacion FROM seguridad.rol_emp AS re 
				INNER JOIN global.empresa AS e ON(re.cod_emp=e.cod_emp)
				WHERE re.cod_rol='".$this->aa_Atributos['codigo']."'";
		$this->f_Con();
		$lr_tabla=$this->f_Filtro($ls_Sql);
		$x=0;
		while($la_registros=$this->f_Arreglo($lr_tabla)){
			$la_respuesta[$x]['codigo']=$la_registros['cod_emp'];
			$la_respuesta[$x]['nombre']=$la_registros['nombre'];
			$la_respuesta[$x]['codigoRelacion']=$la_registros['codigorelacion'];
			$x++;
		}
		$this->f_Cierra($lr_tabla);
		$this->f_Des();

		return $la_respuesta;
	}
	
	private function f_Listar(){
		$x=0;
		$la_respuesta=array();
		$ls_Sql="SELECT * FROM seguridad.rol ";
		$this->f_Con();
		$lr_tabla=$this->f_Filtro($ls_Sql);
		while($la_registros=$this->f_Arreglo($lr_tabla)){
			$la_respuesta[$x]['codigo']=$la_registros['cod_rol'];
			$la_respuesta[$x]['nombre']=$la_registros['nom'];
			$la_respuesta[$x]['descripcion']=$la_registros['desc'];
			$x++;
		}
		$this->f_Cierra($lr_tabla);
		$this->f_Des();
		return $la_respuesta;
	}

	private function guardarDetalle(){
		$lb_Hecho=false;
		$ls_Sql='INSERT INTO seguridad.rol_emp (cod_rol,cod_emp) values ('.$this->aa_Atributos['codigo'].','.$this->aa_Atributos['cod_emp'].')';
		$this->f_Con();
		$lb_Hecho=$this->f_Ejecutar($ls_Sql);
		$this->f_Des();
		if($lb_Hecho){
			$ls_Sql="SELECT e.*,re.cod_rol_emp as codigoRelacion FROM seguridad.rol_emp AS re 
				INNER JOIN global.empresa AS e ON(re.cod_emp=e.cod_emp)
				WHERE re.cod_rol='".$this->aa_Atributos['codigo']."' AND re.cod_emp = '".$this->aa_Atributos['cod_emp']."'";
			$this->f_Con();
			$lr_tabla=$this->f_Filtro($ls_Sql);
			if($la_registros=$this->f_Arreglo($lr_tabla)){
				$la_respuesta['codigo']=$la_registros['cod_emp'];
				$la_respuesta['nombre']=$la_registros['nombre'];
				$la_respuesta['codigoRelacion']=$la_registros['codigorelacion'];
				$x++;
			}
			$this->f_Cierra($lr_tabla);
			$this->f_Des();
			return $la_respuesta;
		}
		return $lb_Hecho;
	}

	private function eliminarDetalle(){
		$lb_Hecho=false;
		$ls_Sql='DELETE FROM seguridad.rol_emp AS re WHERE re.cod_rol='.$this->aa_Atributos['codigo'].' and re.cod_emp='.$this->aa_Atributos['cod_emp'].'';
		$this->f_Con();
		$lb_Hecho=$this->f_Ejecutar($ls_Sql);
		$this->f_Des();
		return $lb_Hecho;
	}
}
?>