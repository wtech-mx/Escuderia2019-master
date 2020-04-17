/* var siteurl = "http://escuderiaservicios.com/subastas/";
 */ 
var siteurl = "http://localhost/escuderia-update/subastas/";
function postrequest(url, data, complete, fnerror){


	$.ajax({
      dataType: "json",
      url: siteurl+url,
      data: data,
      type: "POST",
      async:false, 
      success:complete,
      error:fnerror
       	
    });

}

///Omite "www." que sea escrita en la URL
var fooUrl = document.URL;
if(fooUrl.indexOf("www.") > -1){
	window.location.href = fooUrl.replace("www.","");
}
