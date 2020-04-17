var typingTimer;
var doneTypingIntervalo = 1000;

function LimpiarSubasta() {

	$("#txtNombreSubasta").val("");
	$("#txtIncremento").val("");
	$("#txtNumPujas").val("");
	$("#lstEmpresa").html("");
	$("#cmbEmpresas").val(-1);
	$("#btnGuardarSubasta").attr("attr-idsubasta", "0");
	CargaSubastas(-1, -1);
}

//Administrador Subastas

function AgregaEmpresa(idEmpresa, nombreEmpresa) {

	$("#lstEmpresa").append("<li attr-idx='" + idEmpresa + "' class='empresasSeleccionadas divRegistro'>" + nombreEmpresa + "</li>")
}

function CargaFuncionesAdminSubastas() {
	//debugger;
	$("#modalEmpresa").hide();
	$('.divHeaderContenido').hide();
	$("#txtFechaInicio").datepicker();
	$("#txtFechaFin").datepicker();
	$("#txtFechaInicio").datepicker("option", "dateFormat", "dd/mm/yy" );
	$("#txtFechaFin").datepicker("option", "dateFormat", "dd/mm/yy" );
	$("#divRegistroAutos").hide();

	$("#dpFechaInicio").html(CargaSelectTP());
	$("#dpFechaFin").html(CargaSelectTP());

	$("#divAdministraUsuarios").hide();

	$("#btnNuevaEmpresa").click(function() {
		$('#labelMsjEmpresa').text('');
		$('#txtNombreEmpresa').val('');
		$('#btnGuardaEmpresa').attr('idEmpresa', '0');
		$('#btnEliminaEmpresa').attr('idEmpresa', '0');
		$('#btnEliminaEmpresa').attr('estatus', '1');
		$('#btnEliminaEmpresa').hide();


		$("#modalEmpresa").modal({
			dismissible : true, // Modal can be dismissed by clicking outside of the modal
			opacity : .5, // Opacity of modal background
			inDuration : 300, // Transition in duration
			outDuration : 200, // Transition out duration
			startingTop : '4%', // Starting top style attribute
		});

		$("#modalEmpresa").modal("open");

	});
	$("#btnAgregaEmpresa").on('click', function() {
		if ($("#cmbEmpresas option:selected").val() > 0)
			AgregaEmpresa($("#cmbEmpresas option:selected").val(), $("#cmbEmpresas option:selected").text());

	});
	$("#btnGuardaEmpresa").add("#btnEliminaEmpresa").click(function() {
		if ($("#txtNombreEmpresa").val().length <= 0) {
			//alert("Se requiere introducir un nombre para poder dar de alta una Empresa.");
			Materialize.toast('Se requiere introducir un nombre para poder dar de alta una empresa.', 4000);
			return;
		}
		var vEstatus = 1;
		var vIdempresa = 0;
		if (($(this).attr("idEmpresa")) > 0) {
			vIdempresa = ($(this).attr("idEmpresa"));
			if ($(this).attr("estatus") == "0") {
				vEstatus = 0;
			}
		}

		postrequest("empresas/guardar", {
			"idEmpresa" : vIdempresa,
			"nombreEmpresa" : $("#txtNombreEmpresa").val(),
			"estatus" : vEstatus
		}, function(data) {
			//debugger;
			if (data > 0) {
				//alert("La empresa se agregó correctamente");
				Materialize.toast('La empresa se agreg&oacute; correctamente.', 4000);
				CargaEmpresas(data);
				$("#modalEmpresa").modal("close");

			} else {
				//alert("Ocurrió un error al agregar la empresa");
				Materialize.toast('Ocurri&oacute; un error al agregar la empresa.', 4000);

			}
		});




	});

	$("#btnFiltrar").click(function() {
		CargaSubastas($("#cmbPublicada option:selected").val(), $("#cmbEmpresasFiltro option:selected").val());
	});

	$("#btnGuardarSubasta").click(function() {

		guardarSubasta(this);
		if((window.fechaCambio || window.fechaCambio2 ) && !window.nuevaSubasta ){

			Materialize.toast("Se debe cambiar la fecha de los autos o bien aceptar la sugerencia", 8000);
		}
		window.nuevaSubasta = false;

	});

	$("#btnAgregarSubasta").click(function() {
		debugger;
		window.nuevaSubasta= true;
		$('#txtNombreSubasta').val('');
		$('#txtFechaInicio').val('');
		$('#txtFechaFin').val('');
		$("#lstEmpresa").html("");
		$("#cmbEmpresas").material_select("destroy");
		$("#cmbEmpresas").val(-1);
		$("#cmbEmpresas").material_select();
		$("#dpFechaInicio").material_select("destroy");
		$("#dpFechaInicio").val("00:00:00");
		$("#dpFechaInicio").material_select();
		$("#dpFechaFin").material_select("destroy");
		$("#dpFechaFin").val("00:00:00");
		$("#dpFechaFin").material_select();

		$("#txtIncremento").val("");
		$("#txtNumPujas").val("");
		$("#txtAutosGanados").val("");
		$("input[name=tiposubastas]").prop("checked",false);

		$('.divHeaderContenido').modal("open");

	});



	// $("#btnAgregaAuto").click(function() {
	// 	$("#divRegistroAutos").modal("open");
	// });
	//debugger;
	CargaEmpresas(0);
	CargaTipoSubastas();
	CargaSubastas(-1, -1);
	SoloNumericos("#txtIncremento");
	SoloNumericos("#txtNumPujas");

	eventoFinalizaEscritura('#txtNombreEmpresa', buscaEmpresa, typingTimer, doneTypingIntervalo);



}

function guardarSubasta(obj) {

	oSubastas = new Subastas();
	oSubastas.nombreSubasta = $("#txtNombreSubasta").val();
	oSubastas.IdTipoSubasta = $('input[name=tiposubastas]:checked').val();
	oSubastas.fechaInicio = (!$("#txtFechaInicio").datepicker('getDate')) ? "" : $("#txtFechaInicio").datepicker('getDate').getFullYear() + "-" + ($("#txtFechaInicio").datepicker('getDate').getMonth() + 1) + "-" + $("#txtFechaInicio").datepicker('getDate').getDate()+ " "+$("#dpFechaInicio").val();
	oSubastas.fechaFin = (!$("#txtFechaFin").datepicker('getDate')) ? "" : $("#txtFechaFin").datepicker('getDate').getFullYear() + "-" + ($("#txtFechaFin").datepicker('getDate').getMonth() + 1) + "-" + $("#txtFechaFin").datepicker('getDate').getDate()+ " "+$("#dpFechaFin").val();

	oSubastas.empresas = [];
	oSubastas.incremento = $("#txtIncremento").val();
	oSubastas.ofertas_x_usuarios = $("#txtNumPujas").val();
	oSubastas.autos_x_usuario = $("#txtAutosGanados").val();


	if($("#txtFechaFin").datepicker('getDate') < $("#txtFechaInicio").datepicker('getDate')){

		Materialize.toast('La fecha final no puede ser menor que la de inicio de la subasta.', 4000);
		return false;
	}


	if ($("#btnGuardarSubasta").attr("attr-idsubasta") == "0" || $("#btnGuardarSubasta").attr("attr-idsubasta") == undefined) {
		oSubastas.idSubasta = 0;
	} else {

		oSubastas.idSubasta = $("#btnGuardarSubasta").attr("attr-idsubasta");
	}
	$.each($(".empresasSeleccionadas"), function(key, value) {
		oSubastas.empresas.push($(value).attr("attr-idx"));
	});

	//console.log(JSON.stringify(oSubastas));
	if (ValidaCamposSubasta(oSubastas)) {
		//$(".divHeaderContenido").modal(close);
		postrequest("subastas/guardar", oSubastas, function(data) {

			debugger;
			if (data > 0) {
				$('.divHeaderContenido').modal("close");
				Materialize.toast("La subasta fue guardada con éxito", 5000);
				 $("#btnGuardarSubasta").attr("attr-idsubasta","0");
				CargaSubastas(-1, -1);


			}else{
				Materialize.toast("Ocurrió un error al crear la subasta", 5000);
			}
		}, function(data){
			Materialize.toast("Ocurrió un error al realizar la acción", 5000);
		});
	}

}

function CargaEmpresas(idx) {
	//debugger;
	$("#cmbEmpresas").html("");
	$("#cmbEmpresasFiltro").html("");

	$("#cmbEmpresas").append('<option value="-1"> == Seleccione ==</div>');
	$("#cmbEmpresasFiltro").append('<option value="-1"> == TODAS ==</div>');



	postrequest("empresas/listar", {
		"estatus" : "1"
	}, function(data) {
		var selected = "";
		for (i in data) {
			selected = (data[i].idEmpresa == idx) ? 'selected="selected"' : "";
			$("#cmbEmpresas").append('<option value="' + data[i].idEmpresa + '" ' + selected + '>' + data[i].nombreEmpresa + "</option>");
			$("#cmbEmpresasFiltro").append('<option value="' + data[i].idEmpresa + '">' + data[i].nombreEmpresa + ' </option>');
		}
		$("#cmbEmpresas").material_select();
		$("#cmbEmpresasFiltro").material_select();
	});

}

function CargaTipoSubastas(estatus) {
	postrequest("tiposubastas/listar", {
		"estatus" : "1"
	}, function(data) {
		for (i in data) {
			$("#divTipoSubastas").append('<p><input name="tiposubastas" type="radio" id="tiposubastas' + data[i].idTipo + '" value="' + data[i].idTipo + '"/><label for="tiposubastas' + data[i].idTipo + '">' + data[i].tipoSubasta + '</label></p>');

		}
	});

}
var total_autos_subasta = 0;

function CargaSubastas(estatus, empresa) {
	postrequest("subastas/listar", {
		"estatus" : estatus,
		"empresa" : empresa,
		"subastaId" : -1
	}, function(data) {

		$("#divListaContenido").html("");
		$("#divListaContenido2").html("");
		$("#divListaContenido3").html("");
		$("#divListaContenido4").html("");
		$("#tab-terminadas").html("Terminadas");
		$("#tab-canceladas").html("Canceladas");
		$("#tab-agendadas").html("Agendadas");
		$("#tab-activas").html("Activas");
		for (i in data) {
			var clase_extra = "";
			if(data[i].publicada == "PUBLICADA" && data[i].estatus == "ACTIVA"){
				clase_extra = "tema_publicada";
			}
			var div = '';
			div += '	<div class="divRenglonTabla col s12 m6 l4 '+clase_extra+'">';
			div += '		<div class="card">';
			div += '            <span class="card-title">' + data[i].nombreSubasta + '</span>';
			div += '            <div class="divider"></div>';
			div += '			<div class="fixed-action-btn toolbar" style="position: relative; display: inline-block; float:right;">';
			div += '				<a class="btn-floating">';
			div += '					<i class="large material-icons">menu</i>';
			div += '				</a>';
			div += '				<ul>';
			div += '					<li><a class="btn-floating btnEditarSubasta" attr-id="' + data[i].idSubasta + '" title="Editar Subasta"><i class="material-icons">create</i></a></li>';
			div += '					<li><a class="btn-floating btnAdministraAutos" attr-id="' + data[i].idSubasta + '" attr-nombresubasta="' + data[i].nombreSubasta + '" title="Agregar Autos"><i class="material-icons addCar"></i></a></li>';
			div += '					<li><a class="btn-floating btnAgendarAutos" attr-id="' + data[i].idSubasta + '" attr-nombresubasta="' + data[i].nombreSubasta + '" attr-fini="'+data[i].fechaInicio+'" attr-ffin="'+data[i].fechaFin+'" attr-diff="'+data[i].diff+'" title="Ver horarios de autos"><i class="material-icons">query_builder</i></a></li>';
			div += '					<li><a class="btn-floating btnAgregarUsuariosAutos" attr-id="' + data[i].idSubasta + '" attr-nombresubasta="' + data[i].nombreSubasta + '" title="Agregar Usuarios"><i class="material-icons">group_add</i></a></li>';
			div += '					<li><a class="btn-floating btnVerAutos" attr-id="' + data[i].idSubasta + '" attr-nombresubasta="' + data[i].nombreSubasta + '" title="Ver Autos"><i class="material-icons">drive_eta</i></a></li>';
			div += '					<li><a class="btn-floating btnListaUsuarios" attr-id="' + data[i].idSubasta + '" attr-nombresubasta="' + data[i].nombreSubasta + '" title="Ver Usuarios"><i class="material-icons">group</i></a></li>';
			div += '				</ul>';
			div += '			</div>';
			div += '			<div class="card-content">';
			div += '				<label>Tipo de subasta: </label><label>' + data[i].tipoSubasta + '</label>';
			div += '				<label>Vigencia: </label><label>' + data[i].fechaInicio + ' - ' + data[i].fechaFin + '</label>';
			div += '				<label>Estatus: </label><label>' + data[i].estatus + '</label>';
			div += '				<label>Empresas:</label><label>' + data[i].empresas + '</label>';
			div += '				<label>Publicada:</label><label>' + data[i].publicada + '</label>';
			div += '			</div>';
			div += '			<div class="card-action">';
			div += '				<div class="switch"><label>No Publicar<input type="checkbox" attr-id="' + data[i].idSubasta + '" class="btnPublicar" ' + ((data[i].visible == 0) ? "" : "checked" ) + '/><span class="lever"></span>Publicar</label></div>';
			//div += '				<label class="switch"><input type="checkbox" attr-id="' + data[i].idSubasta + '" class="btnPublicar" ' + ((data[i].visible == 0) ? "" : "checked" ) + ' /><div class="slider round"></div></label>';
			div += '			</div>';
			div += '		</div>';
			div += '	</div>';
			if(data[i].estatus == "TERMINADA" || data[i].estatus == "CERRADA"){
				 $("#divListaContenido2").append(div);
				 if($("#divListaContenido2 > .divRenglonTabla").length > 0)
				 	$("#tab-terminadas").html("Terminadas ("+ $("#divListaContenido2 > .divRenglonTabla").length +")")
				 else
				 	$("#tab-terminadas").html("Terminadas");
			}else if(data[i].estatus == "CANCELADA"){
				$("#divListaContenido3").append(div);
				 if($("#divListaContenido3 > .divRenglonTabla").length > 0)
				 	$("#tab-canceladas").html("Canceladas ("+ $("#divListaContenido3 > .divRenglonTabla").length +")")
				 else
				 	$("#tab-canceladas").html("Canceladas");
			}else if(data[i].estatus == "AGENDADA"){
				$("#divListaContenido4").append(div);
				if($("#divListaContenido4 > .divRenglonTabla").length > 0)
				 	$("#tab-agendadas").html("Agendadas ("+ $("#divListaContenido4 > .divRenglonTabla").length +")")
				 else
				 	$("#tab-agendadas").html("Agendadas");
			}else{
				$("#divListaContenido").append(div);
				if($("#divListaContenido > .divRenglonTabla").length > 0)
				 	$("#tab-activas").html("Activas ("+ $("#divListaContenido > .divRenglonTabla").length +")")
				 else
				 	$("#tab-activas").html("Activas");
			}
		}
		$('ul.tabs').tabs();

		$(".btnAdministraAutos").click(function() {
			debugger;
			var nombreSubasta = $(this).attr("attr-nombresubasta");
			var idSubasta = $(this).attr("attr-id");
			$("#divListaAutos").html("");
			if (idSubasta > 0) {
				$("#divListaAutos").load("views/main/admin/altaautos.html?rand=" + Math.random(), function() {

					$("#divSubastaNombre").html("Subasta: "+nombreSubasta);

					$("#divSubastaNombre").css("display", "");
					$("#btnGuardaAuto").attr("attr-nombresubasta", nombreSubasta);
					$("#btnGuardaAuto").attr("attr-subastaid", idSubasta);
					debugger;
					CargaFuncionesRegistroAuto(Number(idSubasta));
					$("#btnGuardaAuto").show();

				});
			}

			//$("#divRegistroAutos").modal("open");
			$("#divRegistroAutos").show();
			$("#divListaBotonera").hide();
			$("#divAdminSubastas").hide();
			$(".adminSubastasTitle").hide();
			$("#divListaAutos").show();
			$("#divListaAutos2").hide();
			$("#divListaAutosTitulo").hide();
			$("#divDetalleAuto").hide();

		});

		$("#btnListaAutos").click(function(){
			debugger;
			/*

			if($('#divDetalleAuto').css('display') == 'block'){
				$('#divListaAutos').show();
				$('#divDetalleAuto').hide();
			} else {
				$('#divRegistroAutos').modal('close');
				$("#divAdminSubastas").show();

			}
			*/

			if($("#btnListaAutos").attr("attr-origen") == "editarauto"){
				$("#divListaAutos2").show();
				$("#divListaAutos").hide();
				$("#btnListaAutos").attr("attr-origen","listaautos")

			}else{
				$("#divRegistroAutos").hide();
				$("#divListaBotonera").show();
				$("#divAdminSubastas").show();
				$(".adminSubastasTitle").show();


			}



			$('.mainContainer').css('margin-bottom','90px');
		});



		$(".btnVerAutos").click(function() {


			debugger;
			var nombreSubasta = $(this).attr("attr-nombresubasta");
			$("#divListaAutosTitulo").html("Subasta: "+nombreSubasta);
			var idSubasta = $(this).attr("attr-id");
			$("#btnListaAutos").attr("attr-origen","listaautos");
			cargaAutosPorSubasta(idSubasta, "#divListaAutos2");


			if(total_autos_subasta > 0){

				//$('.mainContainer').css('margin-bottom','0');
				//$('.searchItem > .card > .card-content').css('height', '220px');
				$("#divRegistroAutos").show();
				$("#divListaBotonera").hide();
				$("#divAdminSubastas").hide();
				$(".adminSubastasTitle").hide();
				$("#divListaAutos").hide();
				$("#divListaAutos2").show();





				//$("#divRegistroAutos").modal('open');
			}else{
				$("#divRegistroAutos").hide();
				$("#divListaBotonera").show();
				$("#divAdminSubastas").show();
				$(".adminSubastasTitle").show();
				//$("#divRegistroAutos").modal('close');
			}


		});

		$(".btnAgendarAutos").click(function() {

			sessionStorage.setItem('datediff',  $(this).attr("attr-diff"));
			sessionStorage.setItem('nombreSubasta',  $(this).attr("attr-nombresubasta"));
			sessionStorage.setItem('hora_inicio',  $(this).attr("attr-fini"));
			sessionStorage.setItem('hora_fin',  $(this).attr("attr-ffin"));
			sessionStorage.setItem('attr-id',  $(this).attr("attr-id"));

			window.location.href = "main.php?accion=ajusteautos?r="+Math.random();

			/*
			var datediff = $(this).attr("attr-diff");
			var nombreSubasta = $(this).attr("attr-nombresubasta");
			var hora_inicio = $(this).attr("attr-fini");
			var hora_fin = $(this).attr("attr-ffin");
			cargaListaProgramcionAutos( $(this).attr("attr-id"), "#divProgramadorAutosContenido", $(this).attr("attr-fini"), datediff, nombreSubasta, hora_inicio, hora_fin);
			*/

		});



		$("#btnCerrarListaAuto").click(function() {
			$("#divAutos").hide();

		});

		$(".btnAgregarUsuariosAutos").click(function() {

			debugger;
			var nombreSubasta = $(this).attr("attr-nombresubasta");
			var idSubasta = $(this).attr("attr-id");
			$("#btnUploadUserList").attr("idSubasta", idSubasta);

			postrequest('subastas/info',{'id': idSubasta},function(data){
				if(data.length != 0 && data[0].revisada == "1" && data[0].visible == "1"){
					$("#divAdministraUsuarios").modal("open");
				}else{
					Materialize.toast("La subasta debe estar publicada y los horarios de subasta de autos revisados antes de poder invitar usuarios", 5000);
				}


			},function(){
				Materialize.toast("Ocurrió un error al cargar la información de la subasta");
			} );



		});

		$(".btnListaUsuarios").click(function(){
			debugger;
			var nombreSubasta = $(this).attr("attr-nombresubasta");
			var idSubasta = $(this).attr("attr-id");
			cargar_participantes(idSubasta, nombreSubasta);


		});



		$("#btnUploadUserList").click(function() {

			if($('#listausuarios').prop('files').length == 0){
				Materialize.toast("Debe seleccionar un archivo.", 4000)

			}else{
				$("#btnUploadUserList").hide();
				$("#spinUploadFile").show();
				var file_data = $('#listausuarios').prop('files')[0];
				var form_data = new FormData();
				form_data.append('file', file_data);
				form_data.append('accion', 'listausuarios');
				form_data.append('idsubasta', $(this).attr("idSubasta"));
				$.ajax({
					url : 'upload.php', // point to server-side PHP script
					dataType : 'text', // what to expect back from the PHP script, if anything
					cache : false,
					contentType : false,
					processData : false,
					data : form_data,
					type : 'post',
					success : function(php_script_response) {
						$("#btnUploadUserList").show();
						$("#spinUploadFile").hide();


						if (php_script_response.trim().substring(0, 3).toUpperCase() == "ERR") {
							//alert(php_script_response);
							Materialize.toast(php_script_response, 4000);

							$("#divAdministraUsuarios").modal("close");
						} else {
							var respuesta = String(php_script_response).split(".");
							if(respuesta[2] != respuesta[1])
							Materialize.toast("Se procesaron correctamente: "+ respuesta[2] +" de "+ respuesta[0],4000);

							Materialize.toast("Se agregaron correctamente: "+ respuesta[1] +" de "+ respuesta[0],4000);
							var filename = $('input[type=file]').val().replace(/C:\\fakepath\\/i, '');
							$('input[type=file]').val("");

						}

					}
				});

			}



		});

		$(".btnEditarSubasta").click(function() {
			window.fechaCambio  =false;
			window.fechaCambio2 = false;
			CargaInfoSubasta($(this).attr("attr-id"));

		});

		function CargaInfoSubasta(idSubasta) {

			postrequest("subastas/listar", {
				"estatus" : -1,
				"empresa" : -1,
				"subastaId" : idSubasta
			}, function(data) {

				if(data[0].publicada == "PUBLICADA" && data[0].estatus == "ACTIVA"){
						$("#divEditSubasta").html("");
						$("#btnGuardarSubasta").hide();
						$(".divHeaderContenido").css("height","40%");
				}else {
						$("#divEditSubasta").show();
						$("#btnGuardarSubasta").show();
						$(".divHeaderContenido").css("height","70%");
				}

				$("#txtNombreSubasta").val(data[0].nombreSubasta);
				var f1 = data[0].fechaInicio.split(' ');
				var ffecha1 = f1[0].split('-');

				var f2 = data[0].fechaFin.split(' ');
				var ffecha2 = f2[0].split('-');

				debugger;
				$("#txtFechaInicio").datepicker("option", "dateFormat", "dd/mm/yy" );
				$("#txtFechaFin").datepicker("option", "dateFormat", "dd/mm/yy" );

				$("#txtFechaInicio").on("change",function(e){

					window.fechaCambio = true;
				});

				$("#txtFechaFin").on ("change",function(e){

					window.fechaCambio2 = true;
				});


				$("input[name=tiposubastas][value=" + data[0].idTipoSubasta + "]").attr('checked', 'checked');

				$('#txtFechaInicio').datepicker("setDate", ffecha1[2] + "/" + ffecha1[1] + "/" + ffecha1[0]);
				$('#txtFechaFin').datepicker("setDate", ffecha2[2] + "/" + ffecha2[1] + "/" + ffecha2[0]);
				$('#dpFechaInicio').material_select("destroy");
				$('#dpFechaFin').material_select("destroy");
				$('#dpFechaInicio').val(f1[1]);
				$('#dpFechaFin').val(f2[1]);
				$('#dpFechaInicio').material_select();
				$('#dpFechaFin').material_select();
				$("#txtIncremento").val(data[0].incremento);
				$("#txtNumPujas").val(data[0].ofertas_x_usuarios);

				$("#txtAutosGanados").val(data[0].autos_x_usuario);
				$("#btnGuardarSubasta").attr("attr-idsubasta", data[0].idSubasta);
				$("#lstEmpresa").html("");
				var empresas = data[0].empresas.split(',');
				var empresasIds = data[0].empresasId.split(',');
				for (var i = 0; i < empresas.length; i++) {
					AgregaEmpresa(empresasIds[i], empresas[i]);

				}
				$("#btnCancelaSubasta2").attr("attr-subasta",data[0].idSubasta);


				Materialize.updateTextFields();
				$("#btnCancelaSubasta").show();


				$("#btnCancelaSubasta").click(function(){
					$("#divEditSubasta").hide();
					$("#modal-confirm-cancel").show()
					$("#btnCancelaSubasta").hide();
					$("#btnGuardarSubasta").hide();
					$("#closeAltaSubasta").hide();


				});
				$("#btnCancelaCancelaSubasta").click(function(){

					$("#divEditSubasta").show();
					$("#modal-confirm-cancel").hide()
					$("#btnCancelaSubasta").show();
					$("#btnGuardarSubasta").show();
					$("#closeAltaSubasta").show();
				});
				$("#btnCancelaSubasta2").click(function () {

					if( String($("#txtCancelaSubasta").val()).trim() == ""){
						Materialize.toast("Por favor llene el motivo de cancelación", 4000);
						return false;
					}
					postrequest("subastas/cancelar",{"id_subasta":$("#btnCancelaSubasta2").attr("attr-subasta"), "motivo": $("#txtCancelaSubasta").val()}, function(data){
						$("#btnCancelaCancelaSubasta").click();
						CargaSubastas(-1, -1);
						Materialize.toast("La acción se realizó con éxito",5000);
						$(".divHeaderContenido").modal("close");
					},function(e){
						Materialize.toast(e.message,5000);
					})

				})
			});

			$(".divHeaderContenido").modal("open");
		}


		$(".btnPublicar").click(function() {

			var visible = ($(this).prop("checked")) ? 1 : 0;
			var idSubasta = $(this).attr("attr-id");
			var chk = $(this);

			postrequest('subastas/estatus',{'id':idSubasta}, function(data){
				debugger;

				oSubastas = new Subastas();
				oSubastas.idSubasta = idSubasta;

				if(Number(data[0].revisada) == 0){

					Materialize.toast("La subasta no puede ser publicada hasta que se revisen los horarios publicación de autos",5000);
					visible = 0;
					$(".btnPublicar").click();
					$(".btnPublicar").attr("checked",false)

				}else{
					oSubastas.visible = visible;
					postrequest("subastas/publicar", oSubastas, function(data) {

						if (data > 0) {
							Materialize.toast("La operación se realizó exitosamente", 5000);
										CargaSubastas($("#cmbPublicada option:selected").val(), $("#cmbEmpresasFiltro option:selected").val());
						}
					},function(){

						Materialize.toast("Ocurrió un error al ejecutar la operación",4000);
					});
				}



			}, function(error){
				Materialize.toast("Ocurrió un error al consultar la información de la subasta",4000);

			});
		});
	});


	//end postrequest
	$(function() {
		// $('#divAutos').dialog({
		// autoOpen : false,
		// modal : true,
		// height: 520,
		// width : 640,
		// resizable : false,
		// dialogClass: 'noTitleStuff',
		// buttons : [{
		// text : "Cancelar",
		// click : function() {
		// $(this).dialog("close");
		// }
		// }],
		// show : {
		// effect : "blind",
		// duration : 500
		// },
		// hide : {
		// effect : "blind",
		// duration : 500
		// }
		// })

		$('#divAutos').modal({
			dismissible : false, // Modal can be dismissed by clicking outside of the modal
			opacity : .5, // Opacity of modal background
			inDuration : 300, // Transition in duration
			outDuration : 200, // Transition out duration
		});

	});

	$(function() {
		// $(".divHeaderContenido").dialog({
		// //title: "Nueva Subasta",
		// autoOpen : false,
		// modal : true,
		// width : 500,
		// resizable : false,
		// dialogClass: 'noTitleStuff',
		// buttons : [{
		// text : "Cancelar",
		// click : function() {
		// $(this).dialog("close");
		// }
		// }, {
		// text : "Guardar Subasta",
		// "id" : 'btnGuardarSubasta',
		// click: function(){guardarSubasta(this);}
		// }],
		// show : {
		// effect : "blind",
		// duration : 500
		// },
		// hide : {
		// effect : "blind",
		// duration : 500
		// }
		// });
		// $(".ui-dialog-titlebar").hide();
		$(".divHeaderContenido").modal({
			dismissible : false, // Modal can be dismissed by clicking outside of the modal
			opacity : .5, // Opacity of modal background
			inDuration : 300, // Transition in duration
			outDuration : 200, // Transition out duration
		});
	});
/*
	$(function() {
		$("#divRegistroAutos").modal({
			dismissible : false, // Modal can be dismissed by clicking outside of the modal
			opacity : .5, // Opacity of modal background
			startingTop : 0,
			inDuration : 300, // Transition in duration
			outDuration : 200 // Transition out duration
		});

	});*/
	$(function() {
		$("#divProgramadorAutos").modal({
			dismissible : true, // Modal can be dismissed by clicking outside of the modal
			opacity : .5, // Opacity of modal background
			startingTop : 0,
			inDuration : 300, // Transition in duration
			outDuration : 200 // Transition out duration
		});

	});

	$(function() {

		$("#divAdministraUsuarios").modal({
			dismissible : false, // Modal can be dismissed by clicking outside of the modal
			opacity : .5, // Opacity of modal background
			inDuration : 300, // Transition in duration
			outDuration : 200, // Transition out duration
			startingTop : '4%', // Starting top style attribute
			buttons: {
		        "Close": function () {
		            $(this). modal("close")
		        }
	    	}

		});
		$("#divListaUsuariosModal").modal({
			dismissible : false, // Modal can be dismissed by clicking outside of the modal
			opacity : .5, // Opacity of modal background
			inDuration : 300, // Transition in duration
			outDuration : 200, // Transition out duration
			startingTop : '4%', // Starting top style attribute
			buttons: {
		        "Close": function () {
		            $(this). modal("close")
		        }
    		}
		});


	});

	$('.tooltipped').tooltip({delay: 50});
}



function ValidaCamposSubasta(objItem) {
	//debugger;
	var validado = true;
	var campos = "";
	if (objItem.nombreSubasta == '' || objItem.nombreSubasta == undefined) {
		validado = false;
		campos += "Nombre de la subasta";
	}

	if (objItem.IdTipoSubasta == '' || objItem.IdTipoSubasta == undefined) {
		validado = false;
		campos += ((campos != "")? ", ":"") + ", Tipo de subasta";
	}
	if (objItem.fechaInicio == '' || objItem.fechaInicio == undefined) {
		validado = false;
	}
	if (objItem.fechaFin == '' || objItem.fechaFin == undefined) {
		validado = false;
	}

	if (objItem.incremento == '' || objItem.incremento == undefined) {
		validado = false;
	}

	if (objItem.empresas.length <= 0) {
		validado = false;
	}

	if (objItem.ofertas_x_usuarios == '' || objItem.ofertas_x_usuarios == undefined) {
		validado = false;
	}else{

		try{
			objItem.ofertas_x_usuarios = Number(objItem.ofertas_x_usuarios);
		}catch(err){
			validado = false;
		}
	}

	if (objItem.autos_x_usuario == '' || objItem.autos_x_usuario == undefined) {
		validado = false;
	}else{

		try{
			objItem.autos_x_usuario = Number(objItem.autos_x_usuario);
		}catch(err){
			validado = false;
		}
	}

	if (!validado) {

		//alert("Favor de llenar todos los campos requeridos");
		Materialize.toast('Favor de llenar todos los campos requeridos.', 4000);

	}
	return validado;
}

function buscaEmpresa() {

	var txtDesc = $("#txtNombreEmpresa").val().toUpperCase();
	var tipomsj = "una Empresa";
	var elemento = $('#cmbEmpresas').find("option:icontains('" + txtDesc + "')");

	if ($(elemento[0]).text().toUpperCase() == txtDesc) {
		$("#labelMsjEmpresa").text('Ya existe ' + tipomsj + ' con este nombre');

		$("#btnGuardaEmpresa").attr('idEmpresa', $(elemento[0]).val());

		$('#btnEliminaEmpresa').attr('idEmpresa', $(elemento[0]).val());
		$('#btnEliminaEmpresa').attr('estatus', '0');
		$("#btnEliminaEmpresa").show();

	} else {
		$('#labelMsjEmpresa').text('');
		$('#btnGuardaEmpresa').attr('idEmpresa', '0');

		$('#btnEliminaEmpresa').attr('idEmpresa', '0');
		$('#btnEliminaEmpresa').attr('estatus', '1');
		$('#btnEliminaEmpresa').hide();

	}

}
