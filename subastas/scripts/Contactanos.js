var contactanosTemp;

function cargaFuncionesContactanos(){

		$("#divcontactanos").load("views/Contactanos.html?rand="+Math.random(), function() {
			
			
			$("#EstatusContactanos").material_select("destroy");
			$("#EstatusContactanos").material_select();

			$("#txtFechaInicio").datepicker();
			$("#txtFechaFin").datepicker();
			cargaSolicitudesContactanos();
			

			$("#btnFiltrar").click(function(){
				debugger;

					cargaSolicitudesContactanos();

			});

			$("#detalleCotizaciones").modal({
				dismissible : true, // Modal can be dismissed by clicking outside of the modal
				opacity : .5, // Opacity of modal background
				inDuration : 300, // Transition in duration
				outDuration : 200, // Transition out duration
			});
			

		});

		function cargaSolicitudesContactanos(){

			$(".rows").remove();
			$("#grdContactanos").html("");

				postrequest("contactanos/listar",{ "estatus":-1, "page":1 } ,function(data){

					contactanosTemp = data;
					if (data) {

						$.each(data,function(i,item){

							

							var renglon = "<tr>";
							renglon += "<td>"+ item.folio+"</td>";
							renglon += "<td>"+ item.nombre+"</td>";
							renglon += "<td>"+ item.correo+"</td>";	
							renglon += "<td>"+item.telefono+"</td>";
							renglon += "<td>"+item.mensaje+"</td>";
							renglon += "<td>"+item.fecha+"</td>";
							renglon += "<td>"+ ((item.estatus == 0) ?"PENDIENTE" : "ATENDIDA") +"</td>";
							renglon += "<td><div class='btn waves-effect light-blue lighten-1'  cotizacion='"+item.idCotizacion+"' onclick='muestraDetalle(this)'><i class='material-icons'>assignment</i></div></td>";
							renglon +="</tr>";




							$("#grdContactanos").append(renglon);

						});

					}

				});
		}
		

}



function muestraDetalle(obj){
	
 	var idCotizacion = $(obj).attr("cotizacion");
 	var oCotizacion = 1;
	for(i in cotizacionesTemp){
		debugger;
		if(cotizacionesTemp[i].idCotizacion ==idCotizacion){

			oCotizacion = cotizacionesTemp[i];
			break;
		}

	}


	$("#idCotizacion").val(idCotizacion );

	$("#marcaModeloTipo").val(oCotizacion["Marca"] + " "+ oCotizacion["Modelo"] + " - " + oCotizacion["Tipo"]);

	$("#fechaCotizacion").html(oCotizacion["fecha"] + " ["+ ((oCotizacion["Estatus"] == 1)? "PENDIENTE" : "ATENDIDA" ) +"] ");
	$("#cotizacionNombre").val(oCotizacion["Nombre"]);
	$("#cotizacionCorreo").val(oCotizacion["Correo"]);
	$("#cotizaDetalle").val(oCotizacion["subservicios"]);
	$("#cotizaComentario").val(oCotizacion["comentario"]);
	$("#cotizacionTelefono").val(oCotizacion["Telefono"])
	Materialize.updateTextFields();
  	$('.materialize-textarea').trigger('autoresize');
	$("#detalleCotizaciones").modal("open");
	


	// debugger;
	// var mAuto = new miAuto();

	// mAuto.correoUsua = sessionStorage.getItem('correo');
	
	// mAuto.idMarca = $("#cbMarcaAuto").val();
	// mAuto.idModelo = $("#cbModeloAuto").val();
	// mAuto.numPlaca = $("#numdePlaca").val();
	// mAuto.estatus = 1;
	// if($(obj).attr("marca") != undefined){

	// 	mAuto.idMarca = $(obj).attr("marca");
	// 	mAuto.idModelo = $(obj).attr("modelo");
	// 	mAuto.numPlaca = $(obj).attr("placa");
	// 	mAuto.estatus = 0;


	
	// 	postrequest("cotizacion/detalle",{},function(data){
	
	// 			if (data) {
	// 				alert(data);
	// 					if (data.indexOf("eliminado") ==-1)
	// 						$("#altaMisAutos").dialog("close");
			

	// 				cargaMisVehiculos();

	// 			}

	// 	});
	// }
	
}



