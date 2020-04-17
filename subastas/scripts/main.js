$(document).ready(function(){


	ValidaSession();
	var urlvars = getUrlVars();

	var accion = "";
	if(urlvars["accion"]){
		accion = urlvars["accion"].replace("#!","");
		accion = urlvars["accion"].replace("?r","");
	}


	if(accion == undefined || accion == "dashboard" || accion == ""){

		$(".mainBody").load("views/main/dashboard.html", function() {
			CargaFunciones("dashboard");

		});
	}else{

 		cargaHTML(".mainBody","views/main/admin/"+ accion +".html", accion,function() {

   				CargaFunciones(accion);

      	});
	}

  });

function CargaFunciones(o){
	debugger;
	switch(o){
		case "altaautos":
			CargaFuncionesRegistroAuto();
			break;
		case "subastasadmin":
			CargaFuncionesAdminSubastas();
			break;
		case "homeadmin":
			CargaFuncionesAdminHome();
			break;
		case "ventaautos":
			InicializaVentaAutos();
			cargaVehiculos();
			break;
		case "subasta":
			CargaInfoSubasta();
			break;
		case "MisAutos":
			cargaFuncionesMisAutos();
			break;
		case "resultados":
			cargaResultadosSubastas();
			break;
		case "cotizaciones":
			cargaFuncionesMisCotizaciones();
			break;
		case "contactanos":
			cargaFuncionesContactanos();
			break;
		case "ajusteautos":
			debugger;
			cargaListaProgramcionAutos( sessionStorage.getItem('attr-id'), "#divProgramadorAutosContenido", sessionStorage.getItem('hora_inicio'), sessionStorage.getItem('datediff'), sessionStorage.getItem('nombreSubasta'), sessionStorage.getItem('hora_inicio'), sessionStorage.getItem('hora_fin'));
			if(typeof SortHorarios !== 'undefined'  )
			SortHorarios();
			break;
		case "useradmin":
			cargaFuncionesUserAdmin();
			break;
		case "useradd":
			cargaFuncionesUserAdmin();
			break;
		case "dashboard":
		default:
			CargaJsonHome();
			CargaContenidoMain();
			break;

	}
    CargaMaterial ();
	//debugger;
	$(window).trigger('resize');

}

function CargaMaterial (){

	$(".toggles").controlgroup({
		direction : "vertical"
	});

	$('.dateTimeHeader').hide();
	$('select').material_select();

}


function CargaContenidoMain() {


//	$(".divMisSubastas").hide();



	$.get("views/main/missubastas.html?rand="+Math.floor((Math.random() * 10000000) + 1), function(data){
	debugger;
			var misubastahtml = data;
			postrequest("subastas/xusuario", {"idusuario":sessionStorage.claveapi },function(response){
				//console.log(JSON.stringify(response));
				//console.log(JSON.stringify(data.length));
				if(data.length > 0 )
					 $(".divMisSubastas").hide().show();
				var cuentaActivas = 0;
				for(var o in response){


					if(response[o].visible == 1){
						subasta = misubastahtml;

						subasta = subasta.replace("#NOMBRESUBASTA#", response[o].nombreSubasta);
						subasta = subasta.replace("#OFERTAMINIMA#", Number(response[o].incremento).formatMoney());
						subasta = subasta.replace("#TIPOSUBASTAS#", response[o].tipoSubasta);
						subasta = subasta.replace("#ESTATUSSUBASTA#", response[o].estatus);
						subasta = subasta.replace("#SUBASTAID#", response[o].idSubasta);
						subasta = subasta.replace("#FECHA_INISUBASTA#", response[o].fechaInicio.fecha().esMXFormatLarge());
						subasta = subasta.replace("#FECHA_SUBASTA#", response[o].fechaFin.fecha().esMXFormatLarge());
						subasta = subasta.replace("#ATTRFECHAINI#", response[o].fechaInicio);
						subasta = subasta.replace("#ATTRFECHA#", response[o].fechaFin);
						subasta = subasta.replace("#TIPOSUBASTA#", response[o].tipoSubasta);
						subasta = subasta.replace("#SUBASTAID#", response[o].idSubasta);
						subasta = subasta.replace("#SUBASTAID#", response[o].idSubasta);
						if(response[o].estatus == "TERMINADA"){
							subasta = subasta.replace("##CLASS##", "");
						} else{
							subasta = subasta.replace("##CLASS##", "display:none; ");
						}


						if(response[o].estatus == "TERMINADA" || response[o].estatus == "CERRADA"){
							$("#divListaSubastas2").append(subasta);
						}else if(response[o].estatus == "CANCELADA"){
							$("#divListaSubastas3").append(subasta);
						}else if(response[o].estatus == "AGENDADA"){
							$("#divListaSubastas4").append(subasta);
						}else{
							$("#divListaSubastas").append(subasta);
                            cuentaActivas++;
						}


						//$("#ulMisSubastas2").append(subasta);




					}
				}

				if(cuentaActivas>0){
					$("#linkSubastasActivasUsuario").html("Activas ("+cuentaActivas+")");
				}

				$('ul.tabs').tabs();


				 setInterval(function(){

				 	$.each($(".restantePorSubasta"), function(index,value){

				 			var _second = 1000;
						    var _minute = _second * 60;
						    var _hour = _minute * 60;
						    var _day = _hour * 24;
						    var timer;
						    var ini = new $(value).attr("attr-fechaini").fecha();
				 			var end = new $(value).attr("attr-fecha").fecha();
				 			var now = new Date();
				 			var distance = end - now;

					        var salida = "";

					        if (distance > 0 && now > ini) {
					        	var days = Math.floor(distance / _day);
						        var hours = Math.floor((distance % _day) / _hour);
						        var minutes = Math.floor((distance % _hour) / _minute);
						        var seconds = Math.floor((distance % _minute) / _second);

						        var salida =  days + ' días ' +  hours + ' hrs '+ minutes + ' mins ' + seconds + ' segs';

						      	$("#contador"+$(value).attr("attr-id")).html(salida);
					        }

				 	});
				 }, 1000);



			});
	});




};


function CargaDatosPublico(){
	postrequest("data/venta-autos.json", '', function(data){

			$.each(data.vehiculos,function(i,item){


				// if($("#searchBox").val()== "" || item.marca.toLowerCase().indexOf($("#searchBox").val().toLowerCase())>=0 || item.modelo.toLowerCase().indexOf($("#searchBox").val().toLowerCase())>=0){
				// 	$("#searchBody").append(regresaRenglonVenta(item))
				// }



			});
	});

}

function CargaDatosPrivado(){

	postrequest("data/subastas.json", '', function(data){

			$.each(data["datos"],function(i,item){


				if($("#searchBox").val()== "" || item.empresa.toLowerCase().indexOf($("#searchBox").val().toLowerCase())>=0){
					$("#searchBody").append(regresaRenglonSubasta(item))
				}



			});
	});


}
function VerSubasta(o){
	CargaSubasta($(o).attr("attr-id"));


}


function regresaRenglonSubasta(item){

	var renglon = '<div class="searchItem">';
	renglon += '			<div class="searchItemHead" attr-id="'+item.id+'" onclick="VerSubasta(this);"><h3>'+item.empresa+'</h3></div>';

	renglon += '		<div class="searchItemBody">';
	renglon += '			<div>';
	renglon += '				<h4>Subasta </h4>';
	renglon += '				<label>['+item.estatus+']</label>';
	renglon += '				</div>';
	renglon += '				<div>';
	renglon += '					<h4>Inicia:</h4>';
	renglon += '					<label>'+item.fechaInicio+'</label>';
	renglon += '				</div>';
	renglon += '				<div>';
	renglon += '					<h4>Finaliza:</h4>';
	renglon += '					<label>'+item.fechaFin+'</label>';
	renglon += '				</div>';
	renglon += '				<div>';
	renglon += '					<h4>Tipo de oferta:</h4>';
	renglon += '					<label>'+item.tipo+'</label>';
	renglon += '				</div>';
	renglon += '			</div>';
	renglon += '</div>';
	return renglon;


}

function CargaSubasta(subasta) {

	$(".mainBody").load("views/settings3.html", function() {

		cargaCatalogosEmpresas(subasta);

		$("#dp1").datepicker({
			showOn : "button",
			buttonImage : "images/time.svg",
			buttonImageOnly : false,
			buttonText : "Select date"
		});

		$("#dp2").datepicker({
			showOn : "button",
			buttonImage : "images/time.svg",
			buttonImageOnly : false,
			buttonText : "Select date"
		});
		$(".toggles").controlgroup({
			direction : "vertical"
		});

		cargaVehiculos();
		$('.dateTimeHeader').hide();

	});
}

// function VerDetalleAuto(o) {
// 	$(".mainBody").load("views/interna2.html", function() {
// 	});

// }

//***********************
//***********************
//Carga Subastas
//***********************
//***********************
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

				var toogleItem = '<input class="empresaCB" type="checkbox" name="empresa' + i + '" id="empresa' + i + '">';
				toogleItem += '<label for="empresa' + i + '">' + item.empresa + '</label>';

				$(".toggles").append(toogleItem);
			});
		}
	});
}

function muestraGaleria(idx) {

	// var dialog = $("#gallery" + idx).dialog({
		// autoOpen : false,
		// height : 200,
		// width : 380,
		// modal : true,
		// dialogClass : 'no-titlebar'
	// });
//
	// $("#gallery" + idx).addClass('muestraGaleria');
//
	// dialog.dialog("open");

	var dialog = $("#gallery" + idx).modal({
		dismissible : true, // Modal can be dismissed by clicking outside of the modal
		opacity : .5, // Opacity of modal background
		inDuration : 300, // Transition in duration
		outDuration : 200, // Transition out duration
	});

	$("#gallery" + idx).addClass('muestraGaleria');

	dialog.modal("open");

}

function seleccionaImagen(obj) {
	if ($($(obj).parent()).attr("class") == "galleryunselected") {
		$($(obj).parent()).attr("class", "galleryselected");
	} else {

		$($(obj).parent()).attr("class", "galleryunselected");
	}
}


function checkCB() {

	$('#chkMulti').change(function() {
		if ($(this).is(":checked")) {
			$('.toggles ').show();
		}
	});

	$('.closeMulti').on('click', function() {
		$('.toggles ').hide();
	});

	$('.toggles > input[type="checkbox"]:first-of-type').change(function() {
		if ($(this).is(":checked")) {
			$('.toggles').find('input[type="checkbox"]').addClass('cunts');
		}
	});
}

function verDetalle(idVehiculo) {

	$(".mainBody").load("views/interna2.html", function() {

	});

}
