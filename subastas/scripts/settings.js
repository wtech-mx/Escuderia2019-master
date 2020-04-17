function cargaCatalogosEstados(estado) {

	$.ajax({
		dataType : "json",
		url : "data/settings.json",
		data : "data",
		success : function(data) {

			$.each(data["estados"], function(i, item) {

				//$("#cmbEstado").append('<option value="'+item.id+'">'+item.estado +'</option>');
				if (item.id == estado) {
					$("#cmbEstado").append('<option value="' + item.id + '" selected="selected">' + item.estado + '</option>');
				} else {
					$("#cmbEstado").append('<option value="' + item.id + '">' + item.estado + '</option>');
				}

			});

		}
	});
}

function cargaCatalogosEmpresas(empresa) {

	$.ajax({
		dataType : "json",
		url : "data/settings.json",
		data : "data",
		success : function(data) {

			$.each(data["empresas"], function(i, item) {

				if (item.id == empresa) {
					$("#cmbEmpresa").append('<option value="' + item.id + '" selected="selected">' + item.empresa + '</option>');
				} else {
					$("#cmbEmpresa").append('<option value="' + item.id + '">' + item.empresa + '</option>');
				}

			});
		}
	});
}

function cargaVehiculos(idSubasta) {
	$.ajax({
		dataType : "json",
		url : "data/vehiculos-subasta.json",
		data : "data",
		success : function(data) {
			var total = 0;
			$.each(data["vehiculos"], function(i, item) {

				if (item.idSubasta == idSubasta) {
					total++;
					var renglon = "<div>";
					renglon += '<div><img alt="' + item.vehiculo + '" src="' + item.foto + '"  /></div>';
					renglon += "<div>";
					renglon += "<div>" + item.vehiculo + "</div>";
					renglon += "<div>" + item.descripcion + "</div>";
					renglon += "</div>";

					renglon += "<div>" + item.ubicacion + "</div>";
					renglon += "</div>";

					$("#grdVehiculos").append(renglon);

				}

			});
			if (total > 0) {
				$("#grdVehiculos").show();

			}
		}
	});

}

function buscaSubastas() {
	$.ajax({
		dataType : "json",
		url : "data/subastas.json",
		data : "data",
		success : function(data) {

			$.each(data["datos"], function(i, item) {

				if (item.idEstado == $("#cmbEstado").val() && (item.idEmpresa == $("#cmbEmpresa").val() || $("#cmbEmpresa").val() == -1)) {

					var renglon = "<div>";
					renglon += "<div>" + (i + 1) + "</div>";
					renglon += "<div>" + item.empresa + "</div>";
					renglon += "<div>" + item.estatus + "</div>";
					renglon += "<div>" + item.fechaInicio + "</div>";
					renglon += "<div>" + item.fechaFin + "</div>";
					renglon += '<img src="images/icoAuto.svg" alt="AUTOS" onclick="cargaVehiculos(' + item.id + ');" />';
					renglon += "</div>";

					$("#grdResultados").append(renglon);

				}

			});
		}
	});
}


$(document).ready(function() {

	$(".mainBody").load("views/settings.html", function() {

		cargaCatalogosEstados(9);
		cargaCatalogosEmpresas(-1);
		$("#btnBuscar").click(function() {

			buscaSubastas();
		});
		$("#grdVehiculos").hide();
		$("#btnOcultarGrdVehiculos").click(function() {
			$("#grdVehiculos").hide();
		});
		
		$('.dateTimeHeader').hide();
	});

});
