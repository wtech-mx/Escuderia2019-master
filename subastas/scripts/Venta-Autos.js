
function InicializaVentaAutos(){
	cargaCatalogos(-1);

	$('#divDetalleAuto').modal({
			dismissible : true, // Modal can be dismissed by clicking outside of the modal
			opacity : .5, // Opacity of modal background
			inDuration : 300, // Transition in duration
			outDuration : 200, // Transition out duration
		});
}
function cargaCatalogos(idDef) {
	 
	CargaSelectEstados("#cmbEstados");
	//CargaSelectMunicipios("#cbCiudadAuto", $("#cbEstadoAuto").val());
	CargaSelectMarcas("#cmbMarcas", 0,1);
	CargaSelectModelos("#cmbModelos", "#cmbMarcas", 0, 1);
	//CargaSelectTipoTransmision("#cbTipoTransmisionAuto", 0, 1);
 	//CargaSelectFeatures("#cbFeaturesAutos","",1);
 	$("#cmbAnio").html(CargaAnioAutos(0));
	
	$("#cmbMarcas").change(function(){
		CargaSelectModelos("#cmbModelos", "#cmbMarcas", 0, 1);
		
	});
	
}

function MuestraDetalle(o) {

	VerDetalleAuto(o);
	$('#divDetalleAuto').modal("open");
	
}

function seleccionaImagen(obj) {
	if ($($(obj).parent()).attr("class") == "galleryunselected") {
		$($(obj).parent()).attr("class", "galleryselected");
	} else {

		$($(obj).parent()).attr("class", "galleryunselected");
	}
}

function buscarAutos(){


	$(".rows").remove();

	busAutos = new busquedaAuto();
	busAutos.descripcion = $("#desc").val();
	busAutos.precioIni = ($("#txtPrecioIni").val() == undefined || $("#txtPrecioIni").val() == "" ) ? "0" : $("#txtPrecioIni").val();
	busAutos.precioFin = ($("#txtPrecioFin").val() ==undefined || $("#txtPrecioFin").val() == "" ) ? "0":$("#txtPrecioFin").val();
	busAutos.kmIni = ($("#txtKmIni").val() == undefined || $("#txtKmIni").val() == "") ? "0": $("#txtKmIni").val();
	busAutos.kmFin = ($("#txtKmFin").val() ==undefined || $("#txtKmFin").val() == "") ? "0":$("#txtKmFin").val();
	busAutos.estadoId = ($("#cmbEstados option:selected").val() == undefined) ? "0" : $("#cmbEstados option:selected").val();
	busAutos.marcaId = ($("#cmbMarcas option:selected").val() == undefined) ? "0" : $("#cmbMarcas option:selected").val();
	busAutos.modeloId = ($("#cmbModelos option:selected").val() == undefined) ? "0" : $("#cmbModelos option:selected").val();
	busAutos.anio = ($("#cmbAnio option:selected").val() == undefined) ? "0" : $("#cmbAnio option:selected").val();



	postrequest("autos/busqueda",busAutos,function(data){
	
		var total = 0;
		
			if (data){
				$("#grdVehiculos").html("");
			$.each(data, function(i, item) {
					
				var renglon = "<tr>";
					renglon += '<td><img width="150px;" alt="' + item.idAuto + '" src="'+siteurl +'uploads/' + item.foto + '" onerror=\'imgError(this)\'; class="materialboxed" /></td>';
					renglon += "<td>" + item.marca + "</td>";					
					renglon += "<td>" + item.modelo + "</td>";
					renglon += "<td>" + item.km + "</td>";
					renglon += "<td><b>$</b>" + item.precio + "</td>";
					renglon += "<td><div class='btn waves-effect light-blue lighten-1'  onclick='VerDetalleAuto(this);' attr-id='"+item.idAuto+"' attr-subastaid='0'><i class='material-icons'>photo_camera</i></div></td>";
					//renglon += "<td class='center-btn'><div class='btn waves-effect waves-light light-blue disabled'><i class='material-icons'>add</i></div></td>";
					//renglon += "<div style='display:none;' id='gallery" + item.idAuto + "'>";
					renglon += "<div id='gallery"+ item.idAuto +"'  class='modal modal-fixed-footer'>";
				    renglon += "	<div class='modal-content'>";
			        renglon += "	<h4>Subasta:"+ item.idAuto +"</h4>";
			        if(item.fotos != undefined){
						$.each(item.fotos.split(","), function(j, item2) {
							renglon += "<div class='galleryunselected'><img src='fotos/" + item2 + "' onclick='seleccionaImagen(this);' onerror='imgError(this)';/></div>"
						});

					}
					renglon += "	</div>";
					renglon += " </div>";
					renglon += "</div>";
					renglon += "</tr>";
					
					$("#grdVehiculos").append(renglon);
					$('.modal').modal();

			});
			$('.materialboxed').materialbox();

		}

	});

}


function cargaVehiculos() {
	cargaCatalogos(-1);
	$(".toggles").controlgroup({
		direction : "vertical"
	});
	SoloNumericos("[name='txtKmIni']");
		SoloNumericos("#txtKmFin");

		SoloNumericos("#txtPrecioIni");

		SoloNumericos("#txtPrecioFin");
	buscarAutos();


};

function imgError(image) {
    image.onerror = "";
    $(image).attr('src','images/imagenNoDisponible.svg');
    return true;
}

function FiltrarAutos(item) {
	 var precioMax = $('#cmbPrecio').attr('max');
	 var precioMin = $('#cmbPrecio').attr('min');
 
	 var kmMax = $('#cmbKilometros').attr('max');
	 var kmMin = $('#cmbKilometros').attr('min');
	 var valida = true;
	if ($('#cmbPrecio').val() != -1) {
		if (((precioMin < item.precioint && item.precioint < precioMax) || (precioMax == 0 && precioMin > 0 && item.precioint > precioMin))) {

			valida = true;

		} else {

			valida = false;
		}

	}

	if ($('#cmbKilometros').val() != -1) {

		if ((kmMin < item.kmsint && item.kmsint < kmMax) || (kmMax == 0 && kmMin > 0 && item.kmsint > kmMin)) {

			valida = true;

		} else {
			return false;

		}
	}

	if ($('#cmbEstados').val() != -1) {
		if ($('#cmbEstados').val() == item.estadocve) {
			valida = true;
		} else {
			return false;
		}

	}

	if ($('#cmbModelos').val() != -1) {
		if ($('#cmbModelos').val() == item.modelocve) {

			valida = true;
		} else {
			return false;
		}
	}

	if ($('#cmbMarcas').val() != -1) {

		if ($('#cmbMarcas').val() == item.marcacve) {
			valida = true;
		} else {
			return false;
		}
	}

	if ($('#cmbAnio').val() != -1) {

		if ($('#cmbAnio').val() == item.anio) {
			valida = true;
		} else {
			return false;
		}
	}
	
	return valida;

}
	

