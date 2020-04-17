function CargaTipoSubastas(estatus){
	postrequest("tiposubastas/listar", {"estatus":"1"}, function(data){
		for (i in data){
			$("#divTipoSubastas").append('<div class="divRegistro"><input type="radio" name="tiposubastas" id="tiposubastas" value="'+data[i].idTipo+'" >'+data[i].tipoSubasta+'</input></div>');
		}
	});

}

function CargaSubastas(estatus, empresa){
	postrequest("subastas/listar", {"estatus":estatus, "empresa":  empresa }, function(data){
		$("#divListaContenido").html("");	
		for (i in data){
			var div = '';
			div += '<div class="divRenglonTabla">';
			div += '	<div><label>Subata: </label>'+data[i].nombreSubasta+ '<input type="button" attr-id="' + data[i].idSubasta +'" class="btnEditar" text="editar" /><input type="button" attr-id="' + data[i].idSubasta +'" class="btnAgregar" text="administrar autos" /></div>';
			div += '	<div><label>Tipo de subasta: </label>'+data[i].tipoSubasta+'</div>';
			div += '	<div><label>Vigencia: </label>'+data[i].fechaInicio+' - ' + data[i].fechaFin +'</div>';
			div += '	<div><label>Estatus: </label>'+data[i].estatus + '</div>';
			div += '	<div><label>Empresas:</label>'+data[i].empresas + '</div>';
			div += '	<div><label>Publicada:</label>'+data[i].publicada + '<input type="checkbox" attr-id="' + data[i].idSubasta +'" class="btnPublicar" '+ ((data[i].visible == 0) ? "" : "checked" )+ ' /></div>';
			div += '	<div></div>';
			div += '</div>';

			$("#divListaContenido").append(div);
		}
	});

}
