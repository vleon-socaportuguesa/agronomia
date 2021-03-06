<?php
session_start();
//--------------------------CUERPO CONTROLADOR---------------------------------
if(isset($_POST['modulo'])){
	$la_Peticion=$_POST;
} else {
	$la_Peticion=$_GET;
}
$success=0;
//busco la entidad segun la peticion
$lobj_Entidad = obtenerEntidad($la_Peticion['entidad']);

if($lobj_Entidad != false){
	$lobj_Entidad->setPeticion($la_Peticion);
	$respuesta = $lobj_Entidad->gestionar();
}else{
	$respuesta['success'] = 0;
	$respuesta['mensaje']['nombre_tipo'] =  'error';
	$respuesta['mensaje']['titulo'] = 'Entidad No soportada';
	$respuesta['mensaje']['cuerpo'] = 'Entidad '.$la_Peticion['entidad'].' no se encuentra entre las disponibles para esta aplicacion';
	$respuesta['tipo'] = 'error';
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

		case 'arrimadaVsCampo':
			include_once('../clases/cls_ArrimadaVsCampo.php');
			$lobj_Entidad = new cls_ArrimadaVsCampo;
			break;

		case 'calculoLiquidacion':
			include_once('../clases/cls_CalculoLiquidacion.php');
			$lobj_Entidad = new cls_CalculoLiquidacion;
			break;

		case 'clase':
			include_once('../clases/cls_Clase.php');
			$lobj_Entidad = new cls_Clase;
			break;

		case 'cargaValidacion':
			include_once('../clases/cls_Carga_Validacion.php');
			$lobj_Entidad = new cls_Carga_Validacion;
			break;

		case 'concepto':
			include_once('../clases/cls_Concepto.php');
			$lobj_Entidad = new cls_Concepto;
			break;

		case 'detTabuladorLabor':
			include_once('../clases/cls_DetTabuladorLabor.php');
			$lobj_Entidad = new cls_DetTabuladorLabor;
			break;

		case 'detTabuladorTransp':
			include_once('../clases/cls_DetTabuladorTransp.php');
			$lobj_Entidad = new cls_DetTabuladorTransp;
			break;

		case 'diaZafra':
			include_once('../clases/cls_DiaZafra.php');
			$lobj_Entidad = new cls_DiaZafra;
			break;

		case 'FechaCorte':
			include_once('../clases/cls_ReportesCosecha.php');
			$lobj_Entidad = new cls_ReportesCosecha;
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

		case 'liquidacionManual':
			include_once('../clases/cls_LiquidacionManual.php');
			$lobj_Entidad = new cls_LiquidacionManual;
			break;

		case 'liquidacion':
			include_once('../clases/cls_Liquidacion.php');
			$lobj_Entidad = new cls_Liquidacion;
			break;

		case 'nucleo':
			include_once('../clases/cls_Nucleo.php');
			$lobj_Entidad = new cls_Nucleo;
			break;

		case 'productor':
			include_once('../clases/cls_Productor.php');
			$lobj_Entidad = new cls_Productor;
			break;

		case 'reportesCosecha':
			include_once('../clases/cls_ReportesCosecha.php');
			$lobj_Entidad = new cls_ReportesCosecha;
			break;

		case 'reportesPagos':
			include_once('../clases/cls_ReportesPagos.php');
			$lobj_Entidad = new cls_ReportesPagos;
			break;

		case 'tablon':
			include_once('../clases/cls_Tablon.php');
			$lobj_Entidad = new cls_Tablon;
			break;

		case 'tabuladorLabor':
			include_once('../clases/cls_TabuladorLabor.php');
			$lobj_Entidad = new cls_TabuladorLabor;
			break;

		case 'tabuladorTransp':
			include_once('../clases/cls_TabuladorTransp.php');
			$lobj_Entidad = new cls_TabuladorTransp;
			break;

		case 'tipoLiquidacion':
			include_once('../clases/cls_TipoLiquidacion.php');
			$lobj_Entidad = new cls_TipoLiquidacion;
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
		

		//---------M01 Modulo credito a añicultor-----------
		
		case 'm01componente':
			include_once('../clases/cls_M01Componente.php');
			$lobj_Entidad = new cls_M01Componente;
			break;

		case 'detalle_componente':
			include_once('../clases/cls_M01_Detalle_Componente.php');
			$lobj_Entidad = new cls_M01_Detalle_Componente;
			break;

		case 'm01_tipo_formula':
			include_once('../clases/cls_M01_Tipo_Formula.php');
			$lobj_Entidad = new cls_M01_Tipo_Formula;
			break;

		case 'm01_formula':
			include_once('../clases/cls_M01_Formula.php');
			$lobj_Entidad = new cls_M01_Formula;
			break;

		case 'm01_detalle_formula':
			include_once('../clases/cls_M01_Detalle_Formula.php');
			$lobj_Entidad = new cls_M01_Detalle_Formula;
			break;

		case 'crecanicultor':
			include_once('../clases/cls_creditoCanicultorGarantia.php');
			$lobj_Entidad = new cls_creditoCanicultorGarantia;
			break;

		case 'm01_tipo_formula_tipo_garantia':
			include_once('../clases/cls_M01_Tipo_Formula_Tipo_Garantia.php');
			$lobj_Entidad = new cls_M01_Tipo_Formula_Tipo_Garantia;
			break;


		default:
			$lobj_Entidad = false;
			break;
	}
	return $lobj_Entidad;
};
?>
