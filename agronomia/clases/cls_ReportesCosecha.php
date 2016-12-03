<?php
include_once('../../nucleo/clases/cls_Conexion.php');
include_once('../../nucleo/clases/cls_Mensaje_Sistema.php');
class cls_ReportesCosecha extends cls_Conexion{

 protected $aa_Atributos = array();
 private $aa_Campos = array('id_acceso_zona','codigo_zona','codigo_usuario');

 public function setPeticion($pa_Peticion){
   $this->aa_Atributos=$pa_Peticion;
   $this->setDatosConexion($_SESSION['Con']['Nombre'],$_SESSION['Con']['Pass']);
 }
 public function getAtributos(){
   return $this->aa_Atributos;
 }
 public function gestionar(){
   $lobj_Mensaje = new cls_Mensaje_Sistema;
   switch ($this->aa_Atributos['reporte']) {
      case 'resumenFinca':
        switch ($this->aa_Atributos['tipo']) {
          case 'T':
            $registros=$this->mostrarResumenFinca();
            break;

          case 'R':
            $registros=$this->mostrarResumenFinca();
            break;

          case 'D':
            $registros=$this->mostrarResumenFincaPorTablon();
            break;
        }

       if(count($registros["registros"])!=0){
         $success=1;
         $respuesta['registros']=$registros['registros'];
         $respuesta['zafra'] = $registros['zafra'];
       }else{
         $respuesta['success'] = 0;
         $respuesta['mensaje'] = $lobj_Mensaje->buscarMensaje(8);
       }
       break;
    case 'ministerio':
      $registros = $this->mostrarMinisterio();
      if(count($registros['registros'])>0){
        $respuesta['success'] = 1;
        $respuesta['registros'] = $registros['registros'];
        $respuesta['zafra'] = $registros['zafra'];
      }else{
          $respuesta['success'] = 0;
      }
      break;

     default:
       $valores = array('{OPERACION}' => strtoupper($this->aa_Atributos['operacion']), '{ENTIDAD}' => strtoupper($this->aa_Atributos['entidad']));
       $respuesta['mensaje'] = $lobj_Mensaje->completarMensaje(11,$valores);
       $success = 0;
       break;
   }
   if(!isset($respuesta['success'])){
     $respuesta['success']=$success;
   }
   return $respuesta;
 }
 function mostrarResumenFinca(){
   $x=0;
   //Busco El rol
   $this->aa_Atributos['finca']=($this->aa_Atributos['finca']=='null')?'':$this->aa_Atributos['finca'];
   $this->aa_Atributos['zona']=($this->aa_Atributos['zona']=='null')?'':$this->aa_Atributos['zona'];
   $ls_Sql="SELECT * FROM agronomia.spcon_resumenFinca('".$this->aa_Atributos['zafra']."','','".$this->aa_Atributos['finca']."','".$this->aa_Atributos['zona']."','')";
   $this->f_Con();
   $lr_tabla=$this->f_Filtro($ls_Sql);
   while($la_registros=$this->f_Arreglo($lr_tabla)){
     $la_zafra['fechainicio'] = $la_registros['fechainicio'];
     $la_zafra['fechafinal'] = $la_registros['fechafinal'];
     $la_zafra['feczafra'] = $la_registros['feczafra'];
     $la_respuesta[$x] = $this->recolectarDatos('finca',$la_registros);
     $x++;
   }
   $this->f_Cierra($lr_tabla);
   $this->f_Des();

   $la_data["zafra"] = $la_zafra;
   $la_data['registros'] = $la_respuesta;
   return $la_data;
 }
 function mostrarResumenFincaPorTablon(){
   $x=0;
   //Busco El rol
   $this->aa_Atributos['finca']=($this->aa_Atributos['finca']=='null')?'':$this->aa_Atributos['finca'];
   $this->aa_Atributos['zona']=($this->aa_Atributos['zona']=='null')?'':$this->aa_Atributos['zona'];
   $ls_Sql="SELECT * FROM agronomia.spcon_resumenFincaPorTablon('".$this->aa_Atributos['zafra']."','','".$this->aa_Atributos['finca']."','".$this->aa_Atributos['zona']."','')";
   $this->f_Con();
   $lr_tabla=$this->f_Filtro($ls_Sql);
   while($la_registros=$this->f_Arreglo($lr_tabla)){
     $la_zafra['fechainicio'] = $la_registros['fechainicio'];
     $la_zafra['fechafinal'] = $la_registros['fechafinal'];
     $la_zafra['feczafra'] = $la_registros['feczafra'];
     $la_respuesta[$x] = $this->recolectarDatos('tablon',$la_registros);
     $x++;
   }
   $this->f_Cierra($lr_tabla);
   $this->f_Des();

   $la_data["zafra"] = $la_zafra;
   $la_data['registros'] = $la_respuesta;
   return $la_data;
 }
 function recolectarDatos($tipo,$datos){

   $la_respuesta['codmunicipio'] = $datos['codmunicipio'];
   $la_respuesta['nommunicipio'] = $datos['nommunicipio'];
   $la_respuesta['tacortada'] = $datos['tacortada'];
   $la_respuesta['tgzarea'] = $datos['tgzarea'];
   $la_respuesta['tcanacosrealtotaltn'] = $datos['tcañacosrealtotaltn'];
   $la_respuesta['tartotalton'] = $datos['tartotalton'];
   $la_respuesta['rdto'] = $datos['rdto'];
   $la_respuesta['tcanacosrealtotaltnha'] = $datos['tcañacosrealtotaltnha'];
   $la_respuesta['nomestado'] = $datos['nomestado'];
   $la_respuesta['codestado'] = $datos['codestado'];
   if(($tipo == 'finca')||($tipo == 'tablon')){
     $la_respuesta['codfinca'] = $datos['codfinca'];
     $la_respuesta['letfinca'] = $datos['letfinca'];
     $la_respuesta['fincalet'] = $datos['fincalet'];
     $la_respuesta['nomfinca'] = $datos['nomfinca'];
     $la_respuesta['codzona'] = $datos['codzona'];
     $la_respuesta['nomzona'] = $datos['nomzona'];
     $la_respuesta['tgzareacana'] = $datos['tgzareacaña'];
     $la_respuesta['tgzarea_sem'] = $datos['tgzarea_sem'];
     $la_respuesta['tgztonestha'] = $datos['tgztonestha'];
     $la_respuesta['tgztotalest'] = $datos['tgztotalest'];
     $la_respuesta['taporcortar'] = $datos['taporcortar'];
     $la_respuesta['ceporcosechar'] = $datos['ceporcosechar'];
     $la_respuesta['tcanacosesttotaltn'] = $datos['tcañacosesttotaltn'];
     $la_respuesta['tcanacosesttotaltnha'] = $datos['tcañacosesttotaltnha'];
     $la_respuesta['porcentajedesv'] = $datos['porcentajedesv'];
     $la_respuesta['edadcorte'] = $datos['edadcorte'];
     $la_respuesta['tartotaltonha'] = $datos['tartotaltonha'];
     $la_respuesta['pnomfinca'] = $datos['pnomfinca'];
     $la_respuesta['pnommunicipio'] = $datos['pnommunicipio'];
     if($tipo == 'tablon'){
       $la_respuesta['codtablon'] = $datos['codtablon'];
       $la_respuesta['id_finca'] = $datos['id_finca'];
     }
   }
   return $la_respuesta;
 }
 private function mostrarMinisterio(){
   $x=0;
   $this->aa_Atributos['municipio']=($this->aa_Atributos['municipio']=='null')?'':$this->aa_Atributos['municipio'];
   $this->aa_Atributos['zafra']=($this->aa_Atributos['zafra']=='null')?'':$this->aa_Atributos['zafra'];
   $ls_Sql="SELECT * FROM  agronomia.spcon_resumenFinca('".$this->aa_Atributos['zafra']."','".$this->aa_Atributos['municipio']."','','','')";
   $this->f_Con();
   $lr_tabla=$this->f_Filtro($ls_Sql);
   while($la_registros=$this->f_Arreglo($lr_tabla)){
     $la_zafra['fechainicio'] = $this->fFechaBD($la_registros['fechainicio']);
     $la_zafra['fechafinal'] = $this->fFechaBD($la_registros['fechafinal']);
     $la_zafra['feczafra'] = $this->fFechaBD($la_registros['feczafra']);
     $la_zafra['nombrezafra'] = $la_registros['nombrezafra'];
     $la_respuesta[$x] = $this->recolectarDatos('ministerio',$la_registros);
     $x++;
   }
   $this->f_Cierra($lr_tabla);
   $this->f_Des();
   $respuesta["zafra"] = $la_zafra;
   $respuesta['registros'] = $la_respuesta;
   return $respuesta;
 }

}
?>
