var typingTimer;
var doneTypingIntervalo = 1000;


function CargaFuncionesRegistroAuto(idSubasta){
	$("#dialog").modal({
		inDuration : 300, // Transition in duration
		outDuration : 200, // Transition out duration
		startingTop : '4%', // Starting top style attribute
	});



	debugger;
	oAuto = new Autos();
	oAuto.idAuto = 0;
	oAuto.marca = "";
	oAuto.modelo = "";

	if(idSubasta > 0){
		postrequest("subastas/info",{"id":idSubasta},function(data){

			debugger;
            var fTIni = data[0].fecha_tentativa.fecha();
            var fTFin = data[0].fecha_tentativa.fecha().addMinutes(5);
            var fFin = data[0].fechaFin.fecha();
            if(fTIni > fFin || fTFin > fFin){
                fTFin = fFin;
                fTIni = data[0].fechaFin.fecha().addMinutes(-5);
            }
			var renglon = regresaRenglonProgramador(oAuto, idSubasta, fTIni, 0, 0,fTFin, 1);
			$("#hora_inicio").val(data[0].fecha_tentativa);
			$("#divProgramadorAutosContenido").html(renglon);
			$("#hora_fin").val(data[0].fechaFin);


		});
	}


	$('#btnActualizaCatalogo').hide();
	$('#btnEliminaCatalogo').hide();
	$('#labelMensaje').hide();
	$("#cbAnioAuto").html(CargaAnioAutos(0));
	$("#cbAnioAuto").material_select();
	$("#btnAddModelo").hide();
	$("#btnAddFeature").click(function(){

		$("#divFeatureContainer").append(
			"<div class='feature btn light-blue lighten-2' attr-featureid='" + $("#cbFeaturesAutos").val() + "' attr-id='feature-"+$("#cbFeaturesAutos").val()+"'>"+
			"<i class='material-icons orange-text darken-2' onclick='removeFeature(this);' attr-id='" +$("#cbFeaturesAutos").val()+ "' attr-text='"+ $("#cbFeaturesAutos option[value='"+$("#cbFeaturesAutos").val()+"']").text()+"'>cancel</i>"+
			"<span>"+
			$("#cbFeaturesAutos option[value='"+$("#cbFeaturesAutos").val()+"']").text() +
			"</span></div>"
		);

		$("#cbFeaturesAutos option[value='"+$("#cbFeaturesAutos").val()+"']").remove();
		$('.cbFeaturesAutos').find('.select-dropdown').val('');
		$('select').material_select();
	});


	CargaSelectEstados("#cbEstadoAuto");
	CargaSelectMunicipios("#cbCiudadAuto", $("#cbEstadoAuto").val());
	CargaSelectMarcas("#cbMarcaAuto", 0,1);
	CargaSelectModelos("#cbModeloAuto", "#cbMarcaAuto", 0, 1);
	CargaSelectTipoTransmision("#cbTipoTransmisionAuto", 0, 1);
 	CargaSelectFeatures("#cbFeaturesAutos","",1);
 	CargaSelectMotivoPrecio("#cbMotivoPrecio");
 	$("#cbCiudadAuto").material_select();



	$("#cbCiudadAuto").append("<opcion value='0'>== Seleccione == </option>");
	$("#cbEstadoAuto").change(function() {
		CargaSelectMunicipios("#cbCiudadAuto", $("#cbEstadoAuto").val());
	});
	$("#cbFeaturesAutos").change(function(){
		if($(this).val() > 0){
			$("#btnAddFeature").removeClass('disabled');

		}
	});
	$("#cbMarcaAuto").change(function(){
		if($(this).val() >0){
			$("#btnAddModelo").show();
		}else{

			$("#btnAddModelo").hide();

		}
		$("#btnAddMarca").attr('idItem',$(this).val());
		$("#btnAddMarca").attr('Nombre',$(this).val());
		CargaSelectModelos("#cbModeloAuto", "#cbMarcaAuto", 0, 1);
	});

	$('.fotoAuto').find('.file-path').change(function(){
		$("#btnUpload").addClass('pulse');
	});

	$("#btnUpload").click(function() {
		    debugger;
		   	var bUpload = true;

		   	for(var i in $('#fotoAuto').prop('files')){



	   		 	if(i < $('#fotoAuto').prop('files').length ){
						if( $('#fotoAuto').prop('files')[i].size <= 500000){
		   		 	if($('#fotoAuto').prop('files')[i]["type"] == "image/png" || $('#fotoAuto').prop('files')[i]["type"] == "image/jpg" || $('#fotoAuto').prop('files')[i]["type"] == "image/jpeg"){
				    	var form_data = new FormData();
					    form_data.append('file', $('#fotoAuto').prop('files')[i]);


					    $.ajax({
			                url: 'upload.php', // point to server-side PHP script
			                dataType: 'text',  // what to expect back from the PHP script, if anything
			                cache: false,
			                contentType: false,
			                processData: false,
			                data: form_data,
			                type: 'post',
			                success: function(php_script_response){


			                	if(php_script_response.trim().substring(0, 3).toUpperCase() == "ERR"){
									//alert(php_script_response);
									Materialize.toast(php_script_response, 4000);


			                	}else{
			                		//debugger;
			                		var filename = $("#fotoAuto").val().replace(/C:\\fakepath\\/i, '');
									$("#fotosSubidas").append("<div class='fotosAuto' attr-id='"+php_script_response.trim()+"'><i class='material-icons btnCierraImagen orange-text' onclick='$(this).parent().remove()' >remove_circle</i><img  class='materialboxed' data-caption='"+filename+"' width='100px' src='" + siteurl +  "uploads/" + php_script_response.trim() + "' /><span>"+filename+"</span></div>");
									clearFileInput('fotoAuto');
			                		$('.materialboxed').materialbox();
			                	}

			                }
			     		});

				    }else{
		    			Materialize.toast("Imposible subir el archivo "+ $('#fotoAuto').prop('files')[i]["name"]+". Sólo se admitenarchivos .jpg, .jpeg o .png", 4000);


		    		}
					}else{
							Materialize.toast("Imposible subir el archivo el tamaño máximo es de 500Kb", 4000);
					}


				}

		   	}

		    clearFileInput('fotoAuto');
			$("#btnUpload").removeClass('pulse');
		    $('.fotoAuto').find('.file-path').val('');

		});


    soloNumeros("#precioAuto");
    onBlurComas("#precioAuto");
    soloNumeros("#cbKMAuto");
    onBlurComas("#cbKMAuto");

		mayusculas("#txtPlaca");
    mayusculas("#txtNoSerie");

	  soloNumerosOLetras("#txtPlaca");
    soloNumeros("#telefonoContacto");
    soloNumeros("#celularContacto");

	$("#divRegistroAutos #btnListaAutos").click(function(){
		$("#divListaAutos").html("");
	});
	$("#btnGuardaAuto").click(function (){

		GuardaDetalleAuto("guardar");
	});

	CargaSelectColores("#cbColorAuto", 0, 1);


   	$("#btnAddMarca").add("#btnAddModelo").add("#btnAddColor").add("#btnAddCaracteristicas").add("#btnAddPrecio").click(function(){

   		$('#labelMensaje').hide();
   		$('#txtDescripcion').val("");
   		$('#labelMensaje').text("");
   		$('#btnActualizaCatalogo').hide();
		$('#btnEliminaCatalogo').hide();
		$('#dialog').attr("title", $(this).attr("title"));
		$('#dialog').attr("operacion", $(this).attr("operacion") );
   		$('#labelTxtDescripcion').html($(this).attr("desc"));
   		$('#dialog').modal('open');




   });

   $("#btnCierraCatalogo").click(function(){

   		$('#dialog').modal('close');
   });



    $("#btnGuardarCatalogo").add("#btnActualizaCatalogo").add('#btnEliminaCatalogo').click(function(){

   		if($("#txtDescripcion").val().trim()  == ""){

   			Materialize.toast("El texto no puede estár vació", 5000)
   		}else{
	   		oObj = null;
	   		var operacion = $( "#dialog" ).attr("operacion");

	   		switch(operacion){
	   			case "marcas":
	   				oObj = new Marca();
					break;
				case "modelos":
					oObj = new Modelo();
					oObj.idMarca = $("#cbMarcaAuto").val();
					if ($("#cbMarcaAuto").val() == 0 ){
						//alert("Seleccione una marca para poder agregar el modelo.");
						Materialize.toast('Seleccione una marca para poder agregar el modelo.', 4000);
						return;
					}
	   			break;
	   			case "colores":
	   				oObj = new Colores();
	   				break;
	   			case "features":
	   				oObj = new Caracteristicas();
	   				break;
	   			case "precio":
	   				oObj = new Precio();
	   				break;
	   			default:
	   			 //alert("Operación no valida");
	   			 Materialize.toast('Operación inválida.', 4000);
	   			 break;

	   		}


	   		oObj.estatus = 1;
	   		if($(this).attr('idMarca') >0){
	   			oObj.id = $(this).attr('idMarca');
	   			if($(this).attr('estatus')==0)
	   				oObj.estatus = 0;
	   		}
	   		else{
	   			oObj.id = 0;
	   		}

	   		oObj.descripcion = $("#txtDescripcion").val();



	   		postrequest($( "#dialog" ).attr("operacion")+"/guardar", oObj, function(data){
	   			if(data > 0){
					//alert("La operación se realizó con éxito");
					Materialize.toast('La operaci&oacute;n se realiz&oacute; con &eacute;xito.', 4000);
					if($( "#dialog" ).attr("operacion") == "marcas"){
						$("#cbMarcaAuto").html("");
						CargaSelectMarcas("#cbMarcaAuto", 0,1);
					}
					if($( "#dialog" ).attr("operacion") == "modelos"){
						$("#cbModeloAuto").html("");
						CargaSelectModelos("#cbModeloAuto", "#cbMarcaAuto", 0, 1);
					}
					if($( "#dialog" ).attr("operacion") == "colores"){
						$("#cbColorAuto").html("");
						CargaSelectColores("#cbColorAuto", 0, 1);
					}
					if($( "#dialog" ).attr("operacion") == "features"){
						$('#cbFeaturesAutos').material_select("destroy");
						$("#cbFeaturesAutos").html("");
						CargaSelectFeatures("#cbFeaturesAutos","",1);
						$('#cbFeaturesAutos').material_select();
						$('#cbFeaturesAutos').val();
					}
					if($( "#dialog" ).attr("operacion") == "precio"){
						$("#cbMotivoPrecio").html("");
						CargaSelectMotivoPrecio("#cbMotivoPrecio");
					}



				}
				$('#dialog').modal('close');
	   		});
   		}

   });

	eventoFinalizaEscritura('#txtDescripcion',buscaexistente,typingTimer,doneTypingIntervalo);



}
function buscaexistente(){

	var txtDesc=$("#txtDescripcion").val().toUpperCase();
	var tipomsj = "";
	var elemento;
	switch($( "#dialog" ).attr("operacion") ){
		case "marcas":
			tipomsj = "una Marca";
			elemento = $('#cbMarcaAuto').find("option:icontains('"+txtDesc+"')");
		break;
		case "modelos":
			tipomsj = "un Modelo";
			elemento = $('#cbModeloAuto').find("option:icontains('"+txtDesc+"')");
		break;

		case "colores":
			tipomsj = "un Color";
			elemento = $('#cbColorAuto').find("option:icontains('"+txtDesc+"')");
		break;

		case "features":
			tipomsj = "una Característica";
			elemento = $('#cbFeaturesAutos').find("option:icontains('"+txtDesc+"')");
		break;
	}

	if (elemento.text().toUpperCase() == txtDesc ){
		$('#labelMensaje').append('<i class="material-icons red-text">warning</i><span>Ya existe '+tipomsj+' con este nombre</span>');
		$('#labelMensaje').show();
		$('#btnActualizaCatalogo').attr('idMarca',elemento.val());
		$("#btnActualizaCatalogo").show();

		$('#btnEliminaCatalogo').attr('idMarca',elemento.val());
		$('#btnEliminaCatalogo').attr('estatus','0');
		$("#btnEliminaCatalogo").show();


	}else{
		$('#labelMensaje').text('');
		$('#btnActualizaCatalogo').attr('idMarca','0');
		$('#btnActualizaCatalogo').hide();

		$('#btnEliminaCatalogo').attr('idMarca','0');
		$('#btnEliminaCatalogo').attr('estatus','1');
		$('#btnEliminaCatalogo').hide();



	}


}

function removeFeature(o){
	$("#cbFeaturesAutos").append('<option value="'+$(o).attr("attr-id")+'" >' + $(o).attr("attr-text") + '</option>' );
	$(o).parent().remove();
	$('select').material_select();
}
function clearFileInput(id)
{
    var oldInput = document.getElementById(id);

    var newInput = document.createElement("input");

    newInput.type = "file";
    newInput.id = oldInput.id;


    //var newInput = $("<input id=\"fotoAuto\" type=\"file\" accept=\".jpg,.png,.jpeg,image/png,image/jpg,image/jpeg\" multiple />");

    oldInput.parentNode.replaceChild(newInput, oldInput);
    $("#"+oldInput.id).attr("multiple","")
  	$("#"+oldInput.id).attr("name", oldInput.name);

    $("#"+oldInput.id).attr("accept",".jpg,.png,.jpeg,image/png,image/jpg,image/jpeg");
}

function validatextoVacio(inputElement,elementobloq){

  $(inputElement).blur(function(){

      if($(this).length ==0){
      		$(elementobloq).prop("disabled",true);
      }else{
      	$(elementobloq).prop("disabled", false);
      }

  });

}


function validaCamposAltaAutos(item){
	debugger;
	var validado = true;
	var msj  = "";
	var msjSoloNumeros = "Sólo se permiten números en "

	if (item.precio == '' || item.precio == undefined){
		msj += (msj =="") ? "Precio" : ", Precio";
		validado = false;
	}else{
		if(/\D/.test(item.precio)){

			msj += (msj =="") ? msjSoloNumeros+" el precio" : ", "+msjSoloNumeros+" el precio";
			validado = false;
		}
	}
	if (item.marca == '' || item.marca == undefined || item.marca == '0'){
		msj += (msj =="") ? "Marca" : ", Marca";
		validado = false;
	}
	if (item.modelo == '' || item.modelo == undefined || item.modelo == '0'){
		msj += (msj =="") ? "Modelo" : ", Modelo";
		validado = false;
	}
	if (item.color == '' || item.color == undefined || item.color == '0'){
		msj += (msj =="") ? "Color" : ", Color";
		validado = false;
	}
	if (item.anio == '' || item.anio == undefined){
		msj += (msj =="") ? "Año" : ", Año";
		validado = false;
	}else{
		if(/\D/.test(item.anio)){

			msj += (msj =="") ? msjSoloNumeros+" el año" : ", "+msjSoloNumeros+" el año";
			validado = false;
		}
	}
	if (item.km == '' || item.km == undefined){
		msj += (msj =="") ? "KM" : ", KM";
		validado = false;
	}else{
		if(/\D/.test(item.km)){

			msj += (msj =="") ? msjSoloNumeros+" el kilometraje" : ", "+msjSoloNumeros+" el kilometraje";
			validado = false;
		}
	}
	if (item.transmision == '' || item.transmision == undefined || item.transmision == '0'){
		msj += (msj =="") ? "Transmisión" : ", Transmisión";
		validado = false;
	}
	if (item.estado == '' || item.estado == undefined || item.estado == '0'){
		msj += (msj =="") ? "Estado" : ", Estado";
		validado = false;
	}
	if (item.ciudad == '' || item.ciudad == undefined || item.ciudad == '0'){
		msj += (msj =="") ? "Ciudad" : ", Ciudad";
		validado = false;
	}
	if (item.placa == '' ){
		msj += (msj =="") ? "Placa" : ", Placa";
		validado = false;
	}
	if (item.ciudad == '' || item.ciudad == undefined || item.ciudad == '0'){
		msj += (msj =="") ? "Ciudad" : ", Ciudad";
		validado = false;
	}
	if(item.descripcion == '' || item.descripcion == undefined){
		msj += (msj =="") ? "Descripción" : ", Descripción";
		validado = false;
	}


	if(item.telefonoContacto != undefined && item.telefonoContacto.trim() != ''){

		if(/\D/.test(item.telefonoContacto)){

			msj += (msj =="") ? msjSoloNumeros+" el Teléfono de contacto" : ". "+msjSoloNumeros+" el Teléfono de contacto";
			validado = false;
		}

	}else{
		item.telefonoContacto = "";
	}

	if(item.celularContacto != undefined && item.celularContacto.trim() != ''){
		if(/\D/.test(item.celularContacto)){

			msj += (msj =="") ? msjSoloNumeros+" el Celular de contacto" : ". "+msjSoloNumeros+" el Celular de contacto";
			validado = false;
		}

	}else{
		item.celularContacto = "";
	}

	if(item.correoContacto != undefined && item.correoContacto.trim() != ""){
		if(!ValidaEmail(item.correoContacto)){
			msj += (msj =="") ? " el correo de contacto no tiene el formato correcto" : ". el correo de contacto no tiene el formato correcto";
		}
	}else{
		item.correoContacto = "";
	}


	if($(".fotosAuto").length == 0){
		msj += (msj =="") ? "Agregar al menos una foto" : ", Agregar al menos una foto";
		validado = false;
	}


	if (!validado){
		//alert("Favor de llenar todos los campos requeridos");
		Materialize.toast('Algunos de los campos están vacíos: ' + msj , 4000);

	}
	return validado;

}
