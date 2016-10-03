var form = {
  altura: 60,
  campos:[
    {
      tipo : 'campoDeTexto',
      parametros : {
        titulo:'Registros por Pagina',nombre:'cantReg',tipo:'simple',eslabon:'simple',max: 3,valor: 40,requerido:true
      }
    }
  ]
};
function construirUI(){
  var formRep = UI.agregarVentana({
    nombre:'formRep',
    tipo: 'ventana',
    clases: ['form-rep'],
    titulo: {
      tipo:'inverso',
      html:'Parametros'
    },
    sectores:[
      {
        nombre:'form',
        tipo: 'nuevo',
        formulario: form
      },{
				nombre:'dias'
			}
    ]
  },document.body.querySelector('div[contenedor]'));
  var dias = UI.agregarLista({
    nombre: 'listDias',
    titulo: 'Dias',
    clases: ['embebida'],
    campo_nombre: 'nombre',
    carga: {
      uso:true,
      peticion:{
         modulo: "agronomia",
         entidad: "validarCorreo",
         operacion: "buscarDia"
      },
      espera:{
        cuadro:{nombre: 'listaDias',mensaje: 'Cargando Dias'}
      },
      respuesta: function(lista){
        lista.Slots.forEach(function(slot){
          slot.nodo.setAttribute('estado',slot.atributos.estado);
        });
      }
    },
    paginacion: {
      uso:false
    },
    onclickSlot: function(slot){
      //PRIVILEGIO: consultar
      if(sesion.privilegioActivo.buscarOperacion('consultar')){
        if(UI.buscarVentana('formRep').buscarSector('form').formulario.validar()){
          ejecutar(slot);
        }else{
          UI.agregarToasts({
            texto: 'Debe llenar los campos antes de generar el listado',
            tipo: 'web-arriba-derecha-alto'
          });
        }
      }
    }
  },formRep.buscarSector('dias').nodo);
}
function ejecutar(slot){
  var form = UI.buscarVentana('formRep').buscarSector('form').formulario;
  var operacion;
  var columnas;
  operacion = "mostrarDatosDia";
  columnas = '18';
  if(!UI.buscarVentana('listado')){
    crearListado(operacion,columnas,form,slot.atributos.codigo);
  }else{
    var listado = UI.buscarVentana('listado');
    listado.atributos.carga.peticion.operacion = operacion;
    listado.atributos.carga.peticion.fechadia = slot.atributos.codigo;
    listado.paginaActual = 1;
    listado.registrosPorPagina = parseInt(form.buscarCampo('cantReg').captarValor()) || 40;
    listado.recargar();
  }
  gestionarbotonera(slot.atributos.estado);
}

function crearListado(operacion,columnas,form,fechadia){
  var lista = UI.agregarLista({
    titulo: 'Datos Validacion',
    nombre : 'listado',
    selector: 'apagado',
    clases: ['ventana','inversa','not-first','last','completo'],
    columnas: columnas,
    registrosPorPagina: parseInt(form.buscarCampo('cantReg').captarValor()) || 40,
    carga:{
      uso:true,
      peticion: {
         modulo: "agronomia",
         entidad: "validarCorreo",
         operacion: operacion,
         fechadia: fechadia
      }
    },
    paginacion: {
      uso:false
    }
  },document.body.querySelector('div[contenedor]'));
}

function cerrarListado(){
  UI.quitarVentana('listado');
}

function gestionarbotonera(estado){
var informacion ={
  tipo:'validado',
  click: function(){
      UI.agregarToasts({
        texto: 'Datos ya fueron validados',
        tipo: 'web-arriba-derecha-alto'
      });
    }
  };
  var aceptar ={
    tipo:'aceptar',
    click: function(){
      //PRIVILEGIO: autorizar
      if(sesion.privilegioActivo.buscarOperacion('autorizar')){
        aprobarValidacion();
      }else{
        UI.agregarToasts({
          texto: 'Ustede no posee privilegios para esta operacion',
          tipo: 'web-arriba-derecha-alto'
        });
      }
    }
  };


  var agregar;
  var quitar;
  if(estado==='R'){
    agregar = [aceptar];
    quitar = [informacion];
  }else{
    agregar = [informacion];
    quitar = [aceptar];
  }
  if(UI.elementos.botonera === 'noPosee'){
    UI.elementos.botonera = new Botonera({
        botones : agregar,
        contenedor:document.querySelector('div[contenedor]')
    });
  }else{
    UI.elementos.botonera.gestionarBotones({
      quitar : quitar,
      agregar : agregar
    });
  }
}

function aprobarValidacion(){
  var fechadia = UI.buscarVentana('listDias').obtenerSeleccionado().atributos.nombre;
  var mensaje = {
    titulo: 'Validar Datos',
    cuerpo: '¿Realmente desea validar los datos del dia '+fechadia+'?'+
            '<br>Estos datos seran importados a la base datos y estaran disponibles para su utilizacion'
  };
  UI.crearVerificacion(mensaje,function(){
    var peticion = {
       modulo: "agronomia",
       entidad: "validarCorreo",
       operacion: "validarDatos",
       fechadia: UI.buscarVentana('listDias').obtenerSeleccionado().atributos.codigo,
       UID: UI.buscarVentana('listDias').obtenerSeleccionado().atributos.UID
    };
    var cuadro = {
      contenedor: UI.elementos.modalWindow.buscarUltimaCapaContenido().partes.cuerpo.nodo,
      cuadro:{
        nombre: 'cambios',
        mensaje: 'Realizando Cambios'
      }
    };
    torque.manejarOperacion(peticion,cuadro,function(respuesta){
        UI.elementos.modalWindow.buscarUltimaCapaContenido().convertirEnMensaje(respuesta.mensaje);
        if(respuesta.success){
          UI.elementos.botonera.gestionarBotones({
            quitar:['aceptar'],
            agregar:[{
              tipo:'validado',
            click: function(){
                UI.agregarToasts({
                  texto: 'Datos ya fueron validados',
                  tipo: 'web-arriba-derecha-alto'
                });
              }
            }]
          });
          UI.buscarVentana('listDias').recargar();
        }
    });
  });
}