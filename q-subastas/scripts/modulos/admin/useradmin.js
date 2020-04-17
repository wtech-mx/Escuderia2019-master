function cargaFuncionesUserAdmin(){
    CargaFuncionesRegistroComun();
    cargaFuncionesRegistro();

    postrequest("usuarios/listar","",function(data){

        if (data) {

            $.each(data, function( index, usuario ) {

                   $("#listaUsuarios tbody").append("<tr> <th scope='row'>"+usuario.idUsuario+"</th> <td>"+usuario.nombre+"</td> <td>"+usuario.correo+"</td> <td>"+usuario.telefono+"</td> <td></td> </tr>");

                   /*
Funciones editar borrar
 <div class='waves-effect waves-light btn teal lighten-3 tooltipped' data-delay='50' data-position='top' data-tooltip='Resultados de la subasta' attr-id='24' attr-nombre='123' onclick='verResultadoSubasta(this, 1);' data-tooltip-id='cd17cfa7-85f7-1005-0747-843e5bf6de92'> <i class='material-icons' data-tooltip='Resultados de la subasta'>edit</i></div> <div class='waves-effect waves-light btn teal lighten-3 tooltipped' data-delay='50' data-position='top' data-tooltip='Resultados de la subasta' attr-id='24' attr-nombre='123' onclick='verResultadoSubasta(this, 1);' data-tooltip-id='cd17cfa7-85f7-1005-0747-843e5bf6de92'> <i class='material-icons' data-tooltip='Resultados de la subasta'>delete_forever</i></div> 
                   **/
            });



        }

    });


    $("#btnAgregarUsuario").click(function(){
        	window.location = "?accion=useradd";
    });
/*

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
    */
}
