<?php
session_start();
//--------------------------CUERPO CONTROLADOR---------------------------------
if(isset($_POST['operacion'])){
	$la_Peticion=$_POST;
} else {
	$la_Peticion=$_GET;
}
$success=0;
//busco la entidad segun la peticion
$lobj_Entidad = obtenerEntidad($la_Peticion['entidad']);

if(isset($lobj_Entidad)){
	$lobj_Entidad->setPeticion($la_Peticion);
	$respuesta = $lobj_Entidad->gestionar();
}
$respuesta['entidad'] = $la_Peticion['entidad'];
header('Content-type: application/json; charset=utf-8');
echo json_encode($respuesta);
exit();
//------------------------------FIN CUERPO-------------------------------------

//funciones auxiliares
function obtenerEntidad($entidad){
	switch ($entidad) {
		case 'accesoZona':
			include_once('../clases/cls_AccesoZona.php');
			$lobj_Entidad = new cls_AccesoZona;
			break;

		case 'clase':
			include_once('../clases/cls_Clase.php');
			$lobj_Entidad = new cls_Clase;
			break;

		case 'cargaValidacion':
			include_once('../clases/cls_Carga_Validacion.php');
			$lobj_Entidad = new cls_Carga_Validacion;
			break;

		case 'finca':
			include_once('../clases/cls_Finca.php');
			$lobj_Entidad = new cls_Finca;
			break;

		case 'inventario':
			include_once('../clases/cls_Inventario.php');
			$lobj_Entidad = new cls_Inventario;
			break;

		case 'lote':
			include_once('../clases/cls_Lote.php');
			$lobj_Entidad = new cls_Lote;
			break;

		case 'productor':
			include_once('../clases/cls_Productor.php');
			$lobj_Entidad = new cls_Productor;
			break;

		case 'tablon':
			include_once('../clases/cls_Tablon.php');
			$lobj_Entidad = new cls_Tablon;
			break;

		case 'variedad':
			include_once('../clases/cls_Variedad.php');
			$lobj_Entidad = new cls_Variedad;
			break;

		case 'validarCorreo':
			include_once('../clases/cls_ValidarCorreo.php');
			$lobj_Entidad = new cls_ValidarCorreo;
			break;

		case 'zona':
			include_once('../clases/cls_Zona.php');
			$lobj_Entidad = new cls_Zona;
			break;

		case 'zafra':
			include_once('../clases/cls_Zafra.php');
			$lobj_Entidad = new cls_Zafra;
			break;

		default:
			$respuesta['success'] = 0;
			$respuesta['mensaje']['nombre_tipo'] =  'error';
			$respuesta['mensaje']['titulo'] = 'Entidad No soportada';
			$respuesta['mensaje']['cuerpo'] = 'Entidad '.$la_Peticion['entidad'].' no se encuentra entre las disponibles para esta aplicacion';
			$respuesta['tipo'] = 'error';
			break;
	}
	return $lobj_Entidad;
};
?>
