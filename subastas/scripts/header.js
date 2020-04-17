var fnToLoad= "";


$( document ).ready(function() {

	

	cargaHTML(".mainHeader", "views/header.html", "header", function() {

    	
    	
  		
  		if(sessionStorage["nombre"] != undefined){
  			

			$.each( $(".menuitemlogin"), function( key, value ) {
			  
			  $(value).hide();
			});


  			$(".menuitemwelcome").html("<label>Bienvenido: " +sessionStorage["nombre"]+"</label><i class='material-icons'>assignment_ind</i>");
  			$(".menuitemwelcome").prepend("<i id='btnClose' class='material-icons'>exit_to_app</i>");
  			$(".menuitemwelcome").show();


  			$("#divMenuPrincipal > ul").html("");
  			$("#divMenuPrincipal > ul").append('<li><a class="menuitemindex" name="dashboard">SUBASTAS</a></li>');
  			$("#divMenuPrincipal > ul").append('<li><a class="menuitemindex" name="ventaautos">VENTA DE AUTOS</a></li>');
  			$("#divMenuPrincipal > ul").append('<li><a class="menuitemindex" name="MisAutos">MIS AUTOS</a></li>');
	  		if (esAdmin()){
	  			$(".mainHeader").append('');
				$("#divMenuPrincipal > ul").append('<li><a class="dropdown-button" href="#!" data-activates="dropdownAdmn">ADMINISTRAR<i class="material-icons right">arrow_drop_down</i></a></li>');
			} 

  		}else{

  			$.each( $(".menuitemlogin"), function( key, value ) {
			  
			  $(value).show();
			});
  			$(".menuitemwelcome").hide();
  			$(".button-collapse").sideNav();
  		}
		
		jssor_1_slider_init();
		try{
			$('.jssorContainer').find('video').get(0).play();
			$('.jssorContainer').find('video').get(1).play();
			$('.jssorContainer').find('video').get(1).play();
		}catch(ex){
			
		}
		$('.dropdown-button').dropdown({
			inDuration : 300,
			outDuration : 225,
			constrainWidth : false, // Does not change width of dropdown to that of the activator
			hover : true, // Activate on hover
			gutter : 0, // Spacing from edge
			belowOrigin : true, // Displays dropdown below the button
			alignment : 'right', // Displays dropdown with edge aligned to the left of button
			stopPropagation : false // Stops event propagation
		}); 

		$(".menuitemindex").click(function(){
			debugger;
			CargaContenidoAdmin(this);
	      
	  	});
	  	$("#btnClose").click(function(){
	  		postrequest("usuarios/logout",{"claveapi":sessionStorage["claveApi"]}, 
	  			function(data){
	  				if(Number(data) == 0){
	  					alert("Ocurrió un error al cerrar la sesión");
	  				}
	  				sessionStorage = null;
	  				sessionStorage.clear();
	  				window.location.href = "home.php";
	  				CargaContenidoMain();
		  		},
		  		function(data){
		  			alert("Ocurrió un error al cerrar la sesión");
		  		});
	  		
	  	});

	});

 
    function CargaContenidoAdmin(o){
    	
 		fnToLoad = $(o).attr("name");
     	window.location.href = "main.php?accion="+$(o).attr("name");
     	

	}

	
});