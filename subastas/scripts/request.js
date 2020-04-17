var siteurl = "http://localhost/Escuderia2019-master/q-subastas/";
 
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


var fooUrl = document.URL;
if(fooUrl.indexOf("www.") > -1){
	window.location.href = fooUrl.replace("www.","");
}
