var arreglo={};
var factor_agronomia=0;

function construirUI(){
  var ventana = UI.agregarVentana({
      nombre: 'titulo',
      titulo:{
        html: 'Credito Cañicultor por Garantia<br>'+
        '<small class="smalll">si no encuentra el tipo de garantia dirijase a <a href="vis_M01_Tipo_Formula.html">Tipo de Formula</a> y lo asigna</small><br>'+
        '<small class="smalll">luego dirijase a <a href="vis_M01_Formula.html">Formula</a> y crea una nueva</small>',
        tipo: 'inverso'// liso o basico
      },
    sectores:[
    {
        nombre:'formulario',//el nombre que desees
        formulario: UI.buscarConstructor('creCanicultorGarantia'),
        //tambien se le puede agregar el founcionamiento
        //de modificar con las 2 condiciones
        tipo:'nuevo'
    },{
      nombre: 'grid', //puede ser lo que sea
      html:''
    },{
      nombre:'carga',
      html:''
    },{
      nombre:'botonera',
      html:'<button type="button" nombre = "exportar" class="botonCreCanicultor" onclick="check();">Exportar SQLFigo</button>'
    }]
  },document.querySelector('div[contenedor]'));

  var grid = new Grid();
  //UI.servidor = new Servidor();
  ventana.buscarSector('grid').nodo.appendChild(grid.nodo);
  ventana.buscarSector('grid').grid = grid;
}


function actualizar(campo){
  var peticion={
    modulo: "agronomia",
    entidad: "crecanicultor",
    operacion: "buscar_organizaciones",
    id_bus: campo.captarValor()
  }
  var cuadro = {
      contenedor: UI.buscarVentana('titulo').buscarSector('carga').nodo,
      cuadro : {
        nombre: 'actualizando',
        mensaje: 'Cargando Registros'
      }
    };
  UI.buscarVentana('titulo').buscarSector('grid').grid.removerHijos();
  torque.manejarOperacion(peticion,cuadro)
    .then(function(registro){
      console.log(registro);
      var ventana = UI.buscarVentana('titulo');
      ventana.buscarSector('grid').grid.agregarHijos(registro.registros);
      arreglo=registro.registros;
  });
}

var Grid = function(atributos){
  var yo = this;
  yo.atributos = atributos || {};

  yo.cabecera =  '<tr>'+
      '<td>#</td>'+
      '<td>Rif</td>'+
      '<td>Organizacion</td>'+
      '<td>Peso(FIGO)</td>'+
      '<td>Factor(FIGO)</td>'+
      '<td>Peso(Agr)</td>'+
      '<td>Factor(Agr)</td>'+
      '<td>Ton(Est)</td>'+
      '<td align="right">Mn Generado</td>'+
      '<td align="right">Mn Deducible</td>'+
      '<td align="right">Mn Limite</td>'+
      '<td align="center">Seleccione</td>'+
    '</tr>';
  yo.hijos = [];
  yo.nodo=null;

  yo.construir();
}

Grid.prototype.construir = function(){
  var nodo = document.createElement('table');
  nodo.innerHTML= this.cabecera;
  this.nodo=nodo;
  if(this.atributos.hijos){
    this.agregarHijos(this.atributos.hijos);
  }
};

Grid.prototype.agregarHijos = function(hijos){
  var yo = this;
  if (hijos){
    hijos.forEach(function(hijo){
      var newHijo = new Hijo(hijo);
      yo.nodo.appendChild(newHijo.nodo);
      yo.hijos.push(newHijo);
    });
  }
};

Grid.prototype.buscarHijo = function(propiedad,valor){
  var yo = this;
  var resultado = false;
  yo.hijos.forEach(function(hijo){
    if(!hijo.atributos.hasOwnProperty(propiedad)){
      console.error('no existe la propiedad '+propiedad+' para los hijos');
    }else{
      if(hijo.atributos[propiedad] == valor){
        resultado = hijo;
      }
    }
  });
  return resultado;
}

Grid.prototype.removerHijos = function(){
  var yo = this;
  yo.hijos.forEach(function(hijo){
    hijo.nodo.parentNode.removeChild(hijo.nodo);
  });
  yo.hijos = [];
}

Grid.prototype.removerHijo = function(propiedad,valor){
  var yo = this;
  var hijo = yo.buscarHijo(propiedad,valor);
  if(hijo){
    hijo.nodo.parentNode.removeChild(hijo.nodo);
    yo.hijos.splice(yo.hijos.indexOf(hijo),1);
  }
}

var Hijo = function(objeto){
  var yo = this;
  yo.nodo = null;
  yo.atributos = objeto;

  yo.crear();
}

Hijo.prototype.crear = function(){
  var yo = this;
  var tr = document.createElement('tr');

  //asigno el valor correspondiente a la variable caña de la formula
  var CAÑA =yo.atributos.peso_agro;
  //convierto la formula en una cadena en estring
  var factor=String(yo.atributos.des_formula);

  //-------------------------------------------
  //verifico que tenga una formula asignada
  if (yo.atributos.formula!=null) {
    var res_gen=eval(factor);
    var resultado_calculado= (CAÑA*res_gen).toFixed(2);
  }else{
    var res_gen='';
    yo.atributos.formula="Formula no asignada";
  }
  //--------------------------------------------

  //si no tiene un monto deducible o es null le digo que monto deducible sera 0
  //--------------------------------------------
  if (yo.atributos.monto_deducible==null) {
    yo.atributos.monto_deducible=0;
  }
  //--------------------------------------------

  factor_agronomia=res_gen;
  tr.innerHTML = '<td class="checkbien">'+yo.atributos.numero+'</td>'+
  '<td class="checkbien">'+yo.atributos.rif+'</td>'+
  '<td class="checkbien">'+yo.atributos.nombre+'</td>'+
  '<td class="checkbien">'+yo.atributos.peso_figo+'</td>'+
  '<td class="checkbien" align="right">'+yo.atributos.factor_figo+'</td>'+
  '<td class="checkbien">'+yo.atributos.peso_agro+'</td>'+
  '<td class="checkbien">'+res_gen+'</td>'+//factor (resultado de la formula asignada)
  '<td class="checkbien">'+yo.atributos.ton_est+'</td>'+
  '<td class="checkbien" align="right">'+resultado_calculado+'</td>'+/*resultado caña agronomia por factor*/
  '<td class="checkbien" align="right">'+yo.atributos.monto_deducible+'</td>'+/*monto deducible biene de sqlFIGO*/
  '<td class="checkbien" align="right">'+(resultado_calculado-yo.atributos.monto_deducible)+'</td>'+/*monto limite para guardar en sqlFIGO*/
  '<td align="center"><input type="checkbox" class="girar" value="'+(yo.atributos.numero-1)+'" id="pasar'+(yo.atributos.numero-1)+'" name="pasar'+(yo.atributos.numero-1)+'" ></td>';

  yo.nodo = tr;
  // Creamos la variable CELDAS y le asignamos la consulta de cada campo con la clase CHECKBIEN
  var celdas = yo.nodo.querySelectorAll('.checkbien');

  // Creamos unavariable y la igualamos a la funcion que levanta la modal con los regisstros para modificar
  var levantarmodal = function(){
    yo.atributos.factor = eval(String(yo.atributos.des_formula));
    torque
      .Operacion({
        modulo: "agronomia",
        entidad: "crecanicultor",
        operacion: "buscarFactoresDetalle",
        fec_fin: yo.atributos.fec_fin_periodo,
        fec_ini: yo.atributos.fec_ini_periodo,
      })
      .then(function(valores){
        if (valores.success == 0){
          yo.atributos.fac_com_peso = 0;
        }else {
          yo.atributos.fac_com_peso = valores.fac_com_peso;
        }
        console.log(yo.atributos);
        var modal = UI.crearVentanaModal({
            contenido:"ancho",
            cabecera:{
              html: UI.buscarConstructor('m01DetalleCreditoCanicultor').titulo
            },
            cuerpo:{
              formulario: UI.buscarConstructor('m01DetalleCreditoCanicultor'),
              tipo:'modificar',
              registro: yo.atributos,
            },
            pie:{
              /*
                 clases:['botonera'],
                html:   '<section modalButtons>'+
                        '<button type="button" class="icon icon-guardar-indigo-32 mat-blue500"> </button>'+
                        '<button type="button" class="icon icon-editar-blanco-32 mat-green500"> </button>'+
                        '</section>'
              */
            }
          });
          //Detectar capas de una modal tambien se puede usar en la copnsola del navegador
          //UI.elementos.modalWindow.capas[0].nodo.click();
          /*----------------------------------------------------*/
          //Seleccionamos el objeto de la modal tal y como vemos en la linea 206
          //Seleccionamos el boton que está en el pie de pagina de la modal y le asignamos un CLICK

          /*
          modal.partes.pie.nodo.querySelector('.mat-green500').onclick = function(){
            //habilitamos todo el formulario
            modal.partes.cuerpo.formulario.habilitar();
            // Deshabilitamos los campos NOMBRE Y RIF
            modal.partes.cuerpo.formulario.buscarCampo('nombre').deshabilitar();
            modal.partes.cuerpo.formulario.buscarCampo('rif').deshabilitar();
          }
          // Le asignamos al otro boton de la modal un onclick para que deshabilite todo el formulario
          modal.partes.pie.nodo.querySelector('.mat-blue500').onclick = function(){
            modal.partes.cuerpo.formulario.deshabilitar();
            console.log(modal.partes.cuerpo.formulario.captarValores());

          }

          */
      });
  }

  //hacemnos un foreach para recorrer todo el arreglo CELDAS y lo metemos en CELDA QUE ES CUANDO LE DAMOS CLICK A CADA CELDA QUE TENGA
  celdas.forEach( function(celda) {
    celda.onclick = levantarmodal;
  });


}

var arreglo_enviartotal={};

function check(){
  //console.log(arreglo);
  var arreglo_enviar={};
  var cont_check=0;

  for (var i = 0; i < arreglo.length; i++) {
    if(document.getElementById('pasar'+i).checked){
      if (arreglo[i].formula!='Formula no asignada') {
        arreglo_enviar[cont_check]=arreglo[i];
        arreglo_enviar[cont_check]['monto_limite']=document.getElementById('pasar'+i).parentNode.previousSibling.textContent;
        arreglo_enviar[cont_check]['factor_agronomia']=factor_agronomia;
        arreglo_enviar[cont_check]['peso_agro']=(arreglo_enviar[cont_check]['peso_agro']>0)?arreglo_enviar[cont_check]['peso_agro']:arreglo_enviar[cont_check]['ton_est'];
        cont_check++;
      }else{
        alert("Debe asignar una formula antes de exportar");
      }
    }
  }

  if (cont_check!=0) {
    arreglo_enviartotal=arreglo_enviar;
    exportar();
  }
}

function exportar(){

  peticion={
    modulo: "agronomia",
    entidad: "crecanicultor",
    operacion: "insertar_registros_figo",
    datos_campos: JSON.stringify(arreglo_enviartotal) //envio todos los datos selecionados convertido en una cadena de texto
  }
  console.log(peticion);

  torque.Operacion(peticion)
    .then(function(registro){
      console.log(registro);
      if (registro['success']==1) {
        UI.agregarToasts({
          texto: 'Operacion realizada Exitosamente',
          tipo: 'web-arriba-derecha-alto'
        });
        actualizar(UI.buscarVentana('titulo').buscarSector('formulario').formulario.campos[0]);
      }else{
        UI.agregarToasts({
          texto: 'No se pudo realizar la operacion intente nuevamente',
          tipo: 'web-arriba-derecha-alto'
        });
      }

      /*var ventana = UI.buscarVentana('titulo');
      ventana.buscarSector('grid').grid.removerHijos();
      ventana.buscarSector('grid').grid.agregarHijos(registro.registros);
      arreglo=registro.registros;*/
  });
};
