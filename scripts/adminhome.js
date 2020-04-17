function CargaFuncionesAdminHome(){
	debugger;
    $(".divTxtUrl").hide();
    $("#divLink").hide();
    $("#cmbTipoContenido").prop( "disabled", true );
    CargaSeccionesHome();
    

    function CargaSeccionesHome()
    {

        postrequest("seccioneshome/listar?rand="+Math.random(), {"esheader":0}, function(data){
            $("#cmbSeccionesHome").material_select("destroy");
            $("#cmbSeccionesHome").html("");
            $("#cmbSeccionesHome").append("<option value='0' selected='selected'>== Seleccione ==</option>");
            $("#divAdminHomeContenido").html("");  
            var html = "";
            for (i in data){
                html = "";
                html = "<div>";
                html += "       <label>"+data[i].descripcion+"</label>";            
                if(data[i].esimg == 1)
                { 
                    html += "<img style=\"width:300px;\" src=\""+data[i].url+ "\" />";
                
                }else{

                    html += "<iframe id=\"videoTips\" width=\"300px\"  src=\"" +data[i].url+"\" frameborder=\"0\" allowfullscreen style=\"overflow: hidden; \" height=\"100%\" width=\"100%\"></iframe>";
                }
                html += "<div class=\"waves-effect waves-light btn btnEditarSeccionHome\" attr-id=\"" + data[i].id+ "\" attr-esimg=\""+data[i].esimg +"\" attr-ubicacion=\""+data[i].ubicacion +"\" attr-ancho=\""+data[i].ancho +"\" attr-alto=\""+data[i].alto +"\" attr-tag=\""+data[i].tag +"\" attr-url=\""+data[i].url+"\" attr-eslink=\""+ data[i].eslink +"\" attr-link=\""+ data[i].link +"\" aria-hidden=\"true\"><i class='material-icons'>edit</i></div>";
                html += "</div>";
                $("#cmbSeccionesHome").append("<option value=\"" +data[i].id+ "\" attr-id=\"" + data[i].id+ "\" attr-esimg=\""+data[i].esimg +"\" attr-ubicacion=\""+data[i].ubicacion +"\" attr-ancho=\""+data[i].ancho +"\" attr-alto=\""+data[i].alto +"\" attr-tag=\""+data[i].tag +"\" attr-url=\""+data[i].url+"\" attr-eslink=\""+ data[i].eslink +"\" attr-link=\""+ data[i].link +"\">" + data[i].descripcion + "</option>");
                $("#divAdminHomeContenido").append(html);
            }
            $("#cmbSeccionesHome").material_select();


        });
    }

    $("#cmbSeccionesHome").change(function(){ 
        
        fncEditaControles($("#cmbSeccionesHome option:selected"));

    });

    $("#cmbTipoContenido").change(function(){ 
        
        HabilitaControles($(this).val());
    });

    $(".btnEditarSeccionHome").click(function(){
		debugger;
        fncEditaControles($(this));
        
    });
    $("#chkeslink").change(function(){
        if($(this).is(':checked')){
            $("#divLink").show();
        }else{
            $("#divLink").hide();
        }
    });

    
    function fncEditaControles(o){
        $("#cmbSeccionesHome").val($(o).attr("attr-id"));
        $("#cmbTipoContenido").val($(o).attr("attr-esimg"));
        $("#txtTag").val($(o).attr("attr-tag"));
        $("#txtUrl").val($(o).attr("attr-url"));
        $("#btnUploadHome").attr("attr-id", $(o).attr("attr-id"));
        
        $("#cmbUbicacion").material_select("destroy");
        $("#cmbUbicacion").val($(o).attr("attr-ubicacion"));
        $("#cmbUbicacion").material_select();

        $("#txtAncho").val($(o).attr("attr-ancho"));
        $("#txtAlto").val($(o).attr("attr-alto"));

        HabilitaLink($(o).attr("attr-eslink"), o);
        HabilitaControles($(o).attr("attr-esimg"));
        
        Materialize.updateTextFields();
    }

    function HabilitaLink(esLink, o){
        if(esLink == 1){
            $('#chkeslink').prop('checked', true);
            $("#divLink").show();
            $("#txtlink").val($(o).attr("attr-link"));
        }else{
            $('#chkeslink').prop('checked', false);
            $("#divLink").show();
            $("#txtlink").val();
        }

    }

    function HabilitaControles(tipoContenido){
                
        $("#cmbTipoContenido").material_select("destroy");
        $("#cmbTipoContenido").val(tipoContenido);
        $("#cmbTipoContenido").material_select();
        $("#cmbTipoContenido").prop( "disabled", true );

        if(tipoContenido == 0){
            $(".divTxtUrl").show();
            $(".divUploadFile").hide();
             $("#divLink").hide();
             $("#chkeslink").prop( "disabled", true );

        }else{
            $(".divTxtUrl").hide();
            $(".divUploadFile").show();
             $("#divLink").show();
             $("#chkeslink").prop( "disabled", false );
        }
		$('select').material_select();
		Materialize.updateTextFields();
    }

     $('#chkeslink').click(function(){
       if($('#chkeslink').prop('checked')){
            $("#divLink").show();
        }else{
            $("#divLink").hide();
        }
       

     });

    $("#bntActualizar").click(function(){
        debugger;
         postrequest("seccioneshome/updatejson?rand="+Math.random(), {}, function(data){
            if(data == "OK"){
                //alert("Se actualizó la información del home");
                
            }else{
                //alert("Error al actualizar la información del home");
                Materialize.toast('I am a toast!', 4000);
            }
        });

    });
    

    $("#btnUploadHome").click(function() {
            
            var file_data = $('#uploadFile').prop('files')[0];   
            var form_data = new FormData();                  
            form_data.append('file', file_data);
            form_data.append('accion', 'home');
            form_data.append('id', $(this).attr("attr-id"));
            $.ajax({
                        url: 'upload.php', // point to server-side PHP script 
                        dataType: 'text',  // what to expect back from the PHP script, if anything
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,                         
                        type: 'post',
                        success: function(php_script_response){
                            //alert("entro"   );
                            //Materialize.toast('Entr&oacute;', 4000);
                            if(php_script_response.substring(0, 2) == "ERR"){
                                ///alert(php_script_response);
                                Materialize.toast('php_script_response', 4000);
                                
                                
                            }else{
                                //alert("La operación se realizó correctamente");
                                Materialize.toast('La operaci&oacute;n se realiz&oacute; correctamente.', 4000);
                                var filename = $('input[type=file]').val().replace(/C:\\fakepath\\/i, '');
                                $("#uploadedFile").val(filename);
                                
                            }
                            
                        }
             });
            
        });

    $("#btnGuardar").click(function(){
        GuardarSeccion();
      
    });

    function GuardarSeccion(){
          var o = new AdminHome();
            o.id = $("#cmbSeccionesHome option:selected").attr("attr-id");
            o.esimg = $("#cmbSeccionesHome option:selected").attr("attr-esimg");
            o.ubicacion = $("#cmbUbicacion").val();
            o.ancho = $("#txtAncho").val();;
            o.alto = $("#txtAlto").val();;;
            o.tag = $("#cmbSeccionesHome option:selected").attr("attr-tag");
            o.url = $("#txtUrl").val();
            o.eslink = $("#chkeslink").is(':checked');
            o.link = $("#txtlink").val();

            postrequest("seccioneshome/update?rand="+Math.random(), o, function(data){
                if(data == "OK"){
                    //alert("La información se actualizó correctamente");
                    Materialize.toast('La informaci&oacute;n se actualiz&oacute; correctamente.', 4000);
                    CargaSeccionesHome();

                }else{
                    //alert("Ocurrió un error al actualizar la información");
                    Materialize.toast('Ocurri&oacute; un error al actualizar la informaci&oacute;n', 4000);
                }
            });
            $('select').material_select();
            Materialize.updateTextFields();
    }
}

