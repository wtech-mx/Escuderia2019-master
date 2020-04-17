function cargaResultadosSubastas() {

	debugger;



	$("#divTotalOfertas").modal({
		dismissible: true, // Modal can be dismissed by clicking outside of the modal
		opacity: .5, // Opacity of modal background
		inDuration: 300, // Transition in duration
		outDuration: 200, // Transition out duration
		startingTop: '4%', // Starting top style attribute

	});


	$("#tblGanadores").hide();
	$("#contenedor_tabla_ganadores").hide();
	//$("#divTotalOfertas").hide();
	//$("#tblTotalOfertas").hide();

	$("#btnResultados").click(function () {
		$("#tblResultadosSubastas").show();
		$("#contenedor_tabla").show();
		$("#tblGanadores").hide();
		$("#contenedor_tabla_ganadores").hide();
	});

	postrequest("subastas/listar?rand=" + Math.random(),
		{ "estatus": -1, "empresa": -1, "subastaId": -1 },
		function (data) {

			sessionStorage["resultados"] = data;
			for (i in data) {
				var attrID = data[i].idSubasta;
				console.log(data[i]);
				var row = "<tr>";
				row += "<td>" + data[i].nombreSubasta + "</td>";
				row += "<td class='center-align'>" + data[i].fechaInicio + "</td>";
				row += "<td class='center-align'>" + data[i].fechaFin + "</td>";
				row += "<td class='center-align'>" + data[i].estatus + "</td>";
				row += "<td class='center-align'>" + data[i].tipoSubasta + "</td>";
				row += "<td class='center-align'>" + data[i].empresas + "</td>";
				row += "<td class='center-align'>" + data[i].total_participantes + "</td>";
				row += "<td class='center-align'>" + data[i].total_autos + "</td>";
				row += "<td class='center-align'>" + data[i].total_ofertas + "</td>";
				row += '<td><div class="waves-effect waves-light btn teal lighten-3 tooltipped" data-delay="50" data-position="top" data-tooltip="Resultados de la subasta" attr-id="' + data[i].idSubasta + '" attr-nombre="' + data[i].nombreSubasta + '" onclick="verResultadoSubasta(this, 1);" > <i class="material-icons" data-tooltip="Resultados de la subasta">assessment</i></div></td>';
				row += '<td><div class="waves-effect waves-light btn red darken-4 tooltipped" data-delay="50" data-position="top" data-tooltip="Generar PDF" attr-id="' + data[i].idSubasta + '" attr-nombre="' + data[i].nombreSubasta + '" onclick="generarPDF(this, 1);" > <i class="material-icons" data-tooltip="Descargar PDF">save</i></div></td>';
				//row += '<td><div class="waves-effect waves-light btn teal lighten-3 tooltipped" data-delay="50" data-position="top" data-tooltip="Resultados de la subasta" attr-id="'+data[i].idSubasta+'" attr-nombre="'+data[i].nombreSubasta+'" onclick="verResultadoSubasta(this, 0);" > <i class="material-icons" data-tooltip="Resultados de la subasta">assessment</i></div></td>';
				/*
				if(data[i].revisada == 1){
					row += '<td>&nbsp;</td>';
					row += '<td><div class="waves-effect waves-light btn teal lighten-3 tooltipped" data-delay="50" data-position="top" data-tooltip="Resultados de la subasta" attr-id="'+data[i].idSubasta+'" attr-nombre="'+data[i].nombreSubasta+'" onclick="verResultadoSubasta(this);" > <i class="material-icons" data-tooltip="Resultados de la subasta">assignment_turned_in</i></div></td>';
				}else{
					row += '<td><div class="waves-effect waves-light btn teal lighten-3 tooltipped" data-delay="50" data-position="top" data-tooltip="Resultados de la subasta" attr-id="'+data[i].idSubasta+'" attr-nombre="'+data[i].nombreSubasta+'" onclick="verResultadoSubasta(this);" > <i class="material-icons" data-tooltip="Resultados de la subasta">assessment</i></div></td>';
					row += '<td>&nbsp;</td>';
				}
				*/
				//row += '<td><div class="waves-effect waves-light btn teal lighten-3 tooltipped" data-delay="50" data-position="top" data-tooltip="Resultados de la subasta" attr-id="'+data[i].idSubasta+'" attr-nombre="'+data[i].nombreSubasta+'" onclick="verResultadoSubastaMax(this);" > <i class="material-icons" data-tooltip="Resultados de la subasta">assignment_turned_in</i></div></td>';

				//row += '<td><div class="waves-effect waves-light btn blue darken-3" onclick="verResultadoSubasta(this);" attr-id="'+data[i].idSubasta+'"></div></td>';
				row += "</tr>";
				$("#tblResultadosSubastas > tbody").append(row);
			}
			$('.tooltipped').tooltip({ delay: 50 });
		},
		function () {
			//alert("Ocurrió un error al realizar la consulta");
			Materialize.toast('Ocurri&oacute; un error al realizar la consulta.', 4000);
		});




}

var ResultadosSubasta;
function verResultadoSubasta(o, sort) {

	$("#tblTotalOfertasBody").html("");
	var toastContent = $(o).attr("attr-id");
	$("#tblOfertas > table > tbody").html("");
	postrequest("subastas/revisarresultados?r=" + Math.random(), { "estatus": -1, "empresa": -1, "subastaId": toastContent, "idsubasta": toastContent, "sort": sort }, function (data) {


		ResultadosSubasta = data;
		$("#tblGanadores > tbody").html("");
		$("#tblTotalOfertasBody > tbody").html("");
		$("#tblResultadosSubastas").hide();
		$("#contenedor_tabla").hide();
		$("#tblGanadores").show();
		$("#contenedor_tabla_ganadores").show();
		$("#thNombreSubasta").html("<h3>" + $(o).attr("attr-nombre") + "</h3>");
		var totalganancia = 0;
		var totalprecio = 0;
		var totalsubasta = 0;
		for (i in data) {
			console.log(data[i]);
			var gananciaxauto = Number(data[i].oferta) - Number(data[i].precio);
			totalganancia += gananciaxauto;
			totalprecio += Number(data[i].precio);
			totalsubasta += Number(data[i].oferta);
			var row = "<tr>";
			row += "<td class='center-align'><img src='" + siteurl + "uploads/" + data[i].foto + "' width='50px' /></td>";
			row += "<td class='center-align'>" + data[i].marca + "</td>";
			row += "<td class='center-align'>" + data[i].modelo + "</td>";
			row += "<td class='center-align'>" + data[i].anio + "</td>";
			row += "<td class='center-align'>$ " + Number(data[i].precio).formatMoney(2, '.', ',') + "</td>";
			row += "<td class='center-align'>" + data[i].usuario + "</td>";
			row += "<td class='center-align'>$ " + Number(data[i].oferta).formatMoney(2, '.', ',') + "</td>";
			row += "<td class='center-align'>" + data[i].hora_puja + "</td>";
			row += "<td class='center-align'>$ " + gananciaxauto.formatMoney(2, '.', ',') + "</td>";
			row += "<td class='center-align'><div class='waves-effect waves-light btn teal lighten-3 tooltipped' data-delay='50' data-position='top' data-tooltip='Detalle de ofertas' attr-id='" + data[i].autoid + "' onclick='verOfertasPorSubasta(this);' > <i class='material-icons'  data-tooltip='Detalle de ofertas'>assessment</i></div></td>";
			row += "</tr>";
			$("#tblGanadores > tbody").append(row);


			fooOfertas = data[i].ofertas;
			for (z = 0; z < fooOfertas.length; z++) {

				var row2 = "<tr class='ofertas detalleOfertas" + fooOfertas[z].idAuto + "' >";
				row2 += "<td class='center-align'  >" + fooOfertas[z].nombre_usuario + "</td>";
				row2 += "<td class='center-align'>$" + Number(fooOfertas[z].oferta).formatMoney(2, '.', ',') + "</td>";
				row2 += "<td class='center-align'>" + fooOfertas[z].hora_puja + "</td>";
				if (fooOfertas[z].oferta == 286900) {
					debugger;
				}
				if (fooOfertas[z].oferta == data[i].oferta && fooOfertas[z].hora_puja == data[i].hora_puja && fooOfertas[z].nombre_usuario == data[i].usuario) {

					row2 += "<td class='center-align'>GANADORA</td>";
				} else {
					row2 += "<td class='center-align'>&nbsp;</td>";
				}
				row2 += "<td class='center-align'>" + fooOfertas[z].estatus + "</td>";
				row2 += "<td class='center-align'>" + fooOfertas[z].motivo + "</td>";
				row2 += "</tr>";

				$("#tblTotalOfertasBody").append(row2);
			}
		}
		var row = "<tr id='rowTotales'>";
		row += "<td class='center-align'>Total</td>";
		row += "<td class='center-align'>&nbsp;</td>";
		row += "<td class='center-align'>&nbsp;</td>";
		row += "<td class='center-align'>&nbsp;</td>";
		row += "<td class='center-align'>$ " + Number(totalprecio).formatMoney(2, '.', ',') + "</td>";
		row += "<td class='center-align'>&nbsp;</td>";
		row += "<td class='center-align'>$ 	" + Number(totalsubasta).formatMoney(2, '.', ',') + "</td>";
		row += "<td class='center-align'>&nbsp;</td>";
		row += "<td class='center-align'>$ " + totalganancia.formatMoney(2, '.', ',') + "</td>";
		row += "<td class='center-align'>&nbsp;</td>";
		row += "</tr>";
		$("#tblGanadores > tbody").append(row);

	});
}

function verResultadoSubastaMax(o) {
	$("#tblTotalOfertasBody").html("");

	var toastContent = $(o).attr("attr-id");
	$("#tblOfertas > table > tbody").html("");
	postrequest("subastas/revisarresultadosmax?r=" + Math.random(), { "estatus": -1, "empresa": -1, "subastaId": toastContent, "idsubasta": toastContent }, function (data) {

		ResultadosSubasta = data;
		$("#tblGanadores > tbody").html("");
		$("#tblTotalOfertasBody > tbody").html("");
		$("#tblResultadosSubastas").hide();
		$("#contenedor_tabla").hide();

		$("#tblGanadores").show();
		$("#contenedor_tabla_ganadores").show();
		$("#thNombreSubasta").html("<h3>" + $(o).attr("attr-nombre") + "</h3>");
		var totalganancia = 0;
		var totalprecio = 0;
		var totalsubasta = 0;
		for (i in data) {
			var gananciaxauto = Number(data[i].oferta) - Number(data[i].precio);
			totalganancia += gananciaxauto;
			totalprecio += Number(data[i].precio);
			totalsubasta += Number(data[i].oferta);
			var row = "<tr>";
			row += "<td class='center-align'><img src='" + siteurl + "uploads/" + data[i].foto + "' width='50px' /></td>";
			row += "<td class='center-align'>" + data[i].marca + "</td>";
			row += "<td class='center-align'>" + data[i].modelo + "</td>";
			row += "<td class='center-align'>" + data[i].anio + "</td>";
			row += "<td class='center-align'>$" + Number(data[i].precio).formatMoney(2, '.', ',') + "</td>";
			row += "<td class='center-align'>" + data[i].usuario + "</td>";
			row += "<td class='center-align'>$" + Number(data[i].oferta).formatMoney(2, '.', ',') + "</td>";
			row += "<td class='center-align'>" + data[i].hora_puja + "</td>";
			row += "<td class='center-align'>" + gananciaxauto.formatMoney(2, '.', ',') + "</td>";
			row += "<td class='center-align'><div class='waves-effect waves-light btn teal lighten-3 tooltipped' data-delay='50' data-position='top' data-tooltip='Detalle de ofertas' attr-id='" + data[i].autoid + "' onclick='verOfertasPorSubasta(this);' > <i class='material-icons'  data-tooltip='Detalle de ofertas'>assessment</i></div></td>";
			row += "</tr>";
			$("#tblGanadores > tbody").append(row);


			fooOfertas = data[i].ofertas;
			for (z = 0; z < fooOfertas.length; z++) {

				var row2 = "<tr class='ofertas detalleOfertas" + fooOfertas[z].idAuto + "' >";
				row2 += "<td class='center-align'  >" + fooOfertas[z].nombre_usuario + "</td>";
				row2 += "<td class='center-align'>$" + Number(fooOfertas[z].oferta).formatMoney(2, '.', ',') + "</td>";
				row2 += "<td class='center-align'>" + fooOfertas[z].hora_puja + "</td>";
				if (fooOfertas[z].oferta == 286900) {
					debugger;
				}
				if (fooOfertas[z].oferta == data[i].oferta && fooOfertas[z].hora_puja == data[i].hora_puja && fooOfertas[z].nombre_usuario == data[i].usuario) {

					row2 += "<td class='center-align'>GANADORA</td>";
				} else {
					row2 += "<td class='center-align'>&nbsp;</td>";
				}
				row2 += "<td class='center-align'>" + fooOfertas[z].estatus + "</td>";
				row2 += "<td class='center-align'>" + fooOfertas[z].motivo + "</td>";
				row2 += "</tr>";
				$("#tblTotalOfertasBody").append(row2);
			}
		}
		var row = "<tr id='rowTotales'>";
		row += "<td class='center-align'>Total</td>";
		row += "<td class='center-align'>&nbsp;</td>";
		row += "<td class='center-align'>&nbsp;</td>";
		row += "<td class='center-align'>&nbsp;</td>";
		row += "<td class='center-align'>$" + Number(totalprecio).formatMoney(2, '.', ',') + "</td>";
		row += "<td class='center-align'>&nbsp;</td>";
		row += "<td class='center-align'>$" + Number(totalsubasta).formatMoney(2, '.', ',') + "</td>";
		row += "<td class='center-align'>&nbsp;</td>";
		row += "<td class='center-align'>" + totalganancia.formatMoney(2, '.', ',') + "</td>";
		row += "<td class='center-align'>&nbsp;</td>";
		row += "</tr>";
		$("#tblGanadores > tbody").append(row);

	});
}


function verOfertasPorSubasta(o) {

	//$("#tblTotalOfertasBody > tr").hide();
	$(".ofertas").hide();
	$(".detalleOfertas" + $(o).attr("attr-id")).show();
	//$("#tblTotalOfertas").show();
	$("#divTotalOfertas").modal("open");

}

function generarPDF(o, sort) {

	var toastContent = $(o).attr("attr-id");
	var nombreSubasta = $(o).attr("attr-nombre");
	$.ajax({
		url: "http://eago.com.mx/q-subastas/excel/generarPDF",
		method: "POST",
		data: { "estatus": -1, "empresa": -1, "subastaId": toastContent, "idsubasta": toastContent, "sort": sort },
		xhrFields: {
			responseType: "blob"
		},
		success: function (data) {
			var a = document.createElement("a");
			var url = window.URL.createObjectURL(data);
			a.href = url;
			a.download = "Reporte-" + nombreSubasta + ".xlsx";
			document.body.append(a);
			a.click();
			a.remove();
			window.URL.revokeObjectURL(url);
		}
	});

	/* postrequest("excel/generarPDF", { "estatus": -1, "empresa": -1, "subastaId": toastContent, "idsubasta": toastContent, "sort": sort }, function (data) {
		debugger;

		var a = document.createElement("a");
		var url = window.URL.createObjectURL(data);
		a.href = url;
		a.download = "ReporteSubasta.xlsx";
		document.body.append(a);
		a.click();
		a.remove();
		window.URL.revokeObjectURL(url); */
		/* if (data.length != 0) {
			console.log("descargando xlsx");
			var url = window.location.origin;
			window.open(url, "_blank");
		}
		else {
			console.log("no se recibio respuesta");
		} */


	/* }); */
}