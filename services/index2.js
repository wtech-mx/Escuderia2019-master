var calendars = {};
$(document).ready(function() {
	//carga el contenido del index

	checkCookie();
	$(".logoHeader").click(function(){

		window.location = siteurl+"home.php";
	});


	cargaHTML(".mainBody", "views/index.html", "", function() {

		CargaJsonHome();

	});

	cargaHTML(".mainHeader", "views/header.html", "", function() {
		//debugger;

		asignaFunciones();
		jssor_1_slider_init();

		$(".button-collapse").sideNav();

		try{
			$('.jssorContainer').find('video').get(0).play();
			$('.jssorContainer').find('video').get(1).play();
			$('.jssorContainer').find('video').get(2).play();
		}catch(ex){

		}
		$(".logoHeader").click(function() {
			cargaHTML(".mainBody", "views/index.html", "", function() {
				console.log("carga home");

			});

		});




		var urlvars = getUrlVars();
		if(urlvars["s"] != undefined){

				cargaHTMLLogin(urlvars["s"]);
		}



	});

	cargaHTML(".mainFooter", "views/footer.html", "", function() {
		console.log("Cargo footer");
		console.log("Load was performed for footer.");
		var fHeight = $('footer').outerHeight(true);
		$('.mainContainer').css('margin-bottom', fHeight);
	});

	function cargaHTMLLogin(seccion){
		//obtiene la propiedad name del elemento del menu y busca la vista con el mismo nombre y lo carga con ajax
		var s = seccion;
		if(seccion == "invitacion"){
			s= "registro";
		}

		cargaHTML(".mainBody", "views/main/" + s+ ".html", $(this).attr("name"), function() {

			if (seccion == "registro") {
				CargaFuncionesRegistroComun();
				cargaFuncionesRegistro();
			} else if (seccion == "login") {
				cargaFuncionesLogin();
				$('.mainHeader').hide();
				$('.mainFooter').hide();
				$('#loginMail').focus();
			}else if (seccion == "valida"){
				$('.mainHeader').hide();
				$('.mainFooter').hide();
				CargaFuncionesValidaUsuario();

			}else if (seccion == "servicios"){
				initCargaServicios();
			}else if(seccion == "recuperar"){
				$('.mainHeader').hide();
				$('.mainFooter').hide();
				CargaFuncionesRecuperar();
			}else if(seccion == "nuevacontrasena"){
				CargaFuncionesNuevaContrasena();
			}else if(seccion == "invitacion"){
				CargaFuncionesRegistroComun();
				CargaFuncionesRegistroInvitacion();

			}


		});


	}

	function asignaFunciones() {

		$(".menuitem").click(function() {
			window.location = "?s="+$(this).attr("name");
		});

		$(".menuitemindex").click(function() {
			debugger;
			window.location = "?s="+$(this).attr("name");
		});

		$(".submenuitem").click(function() {
				window.location = "?s="+$(this).attr("name");
		});
	}


	function CargaFuncionesRegistroComun(){

		$(".divError").hide();
		$(".divErrorPassword").hide();
		$.dobPicker({
			// Selectopr IDs
			daySelector : '#dobday',
			monthSelector : '#dobmonth',
			yearSelector : '#dobyear',
			// Default option values
			dayDefault : 'Día',
			monthDefault : 'Mes',
			yearDefault : 'Año',
			// Minimum age
			minimumAge : 12,
			// Maximum age
			maximumAge : 100 // OPTIONS
		});

		$('select').material_select();

		postrequest("categorias/listar", {
			"estatus" : "1"
		}, function(data) {

			for (cat in data) {

				// $("#divPreferencias").append('<div class="divRegistro"><input type="checkbox" attr-data="' + data[cat].id + '" class="chkPref" />' + data[cat].descripcion + "</div>");
				$("#divPreferencias").append('<p><input type="checkbox" id="' + data[cat].id + '" attr-data="' + data[cat].id + '" class="chkPref" /><label for="' + data[cat].id + '">' + data[cat].descripcion + '</label></p>')
			}

		});

		$("#btnEula").click(function() {

			cargaHTML("#dialogEula", "views/eula.html", "eula", function(){
				console.log("EULA cargado");
			});



			$(function() {
				$("#dialogEula").dialog({
					height : 400,
					width : 500,
					modal : true,
					title : "Terminos y Condiciones",
					buttons : {
						Ok : function() {
							$(this).dialog("close");
						}
					}
				});
			});

		});
		/*
		$("#registroRepetirPass").focusout(function() {
			if ($("#registroRepetirPass").val() != $("#registroPassword").val().trim()) {

				$(".divErrorPassword").show();
			} else {
				$(".divErrorPassword").hide();
			}
		});
		*/

	}

	function cargaFuncionesRegistro() {

		/*
		 *
		 *Registro
		 *
		 */
		$("#btnGuardar").click(function() {

			debugger;
			var oUsuario = new Usuario();
			oUsuario.nombre = $("#registroNombre").val();
			oUsuario.appaterno = $("#registroApPaterno").val();
			oUsuario.apmaterno = $("#registroApMaterno").val();
			oUsuario.email = $("#registroMail").val();
			oUsuario.password = $("#registroPassword").val();
			oUsuario.verificapassword = $("#registroRepetirPass").val();
			oUsuario.dd = $("#dobday").val();
			oUsuario.mm = $("#dobmonth").val();
			oUsuario.yyyy = $("#dobyear").val();
			oUsuario.placa = $("#registroPlaca").val();
			oUsuario.categorias = [];
			oUsuario.telefono = $("#registroTelefono").val();

			console.log(JSON.stringify(oUsuario))	;
			var categorias = [];

			$(".chkPref:checked").each(function() {

				var foo = new UsuarioCategorias(-1, $(this).attr("attr-data"));
				oUsuario.categorias.push(foo);
			});

			if (!ValidaRegistro(oUsuario)) {
				return false;
			} else {
				oUsuario.fecha_nacimiento = new Date(oUsuario.yyyy, oUsuario.mm - 1, oUsuario.dd);

			}

			postrequest("usuarios/registro", oUsuario, function(data) {
				debugger;
				if (data > 1) {



					sessionStorage.setItem('nombre', $("#registroNombre").val() + " " + $("#registroApPaterno").val() + " " + $("#registroApMaterno").val());
					sessionStorage.setItem('correo', $("#registroMail").val());
					sessionStorage.setItem('idUsuario', data);
					alert("Ya estas registrado, Le hemos enviado un correo de verificación");
					window.location.href = "home.php?s=login";

				}else{

					Materialize.toast("Ocurrió un error al realizar el registro" , 4000);
				}

			},function(data){
				Materialize.toast("Ocurrió un error en el servidor" , 4000);
			});

		});

	};

	function CargaFuncionesRegistroInvitacion(){

		var vars = getUrlVars();
		postrequest("usuarios/info", {"idUsuario":vars["idusuario"] }, function(data) {
			try{


				if(Number(data["verificado"]) == 1){
					window.location.href = "home.php?s=login";
				}
				if(data["claveApi"] != vars["claveapi"]){
					//alert("La información de la invitación es incorrecta");
					Materialize.toast('La informaci&oacute;n de la invitaci&oacute;n es incorrecta.', 4000);
					window.location.href = "home.php";
				}
				$("#registroNombre").val(data["nombre"]);
				$("#registroApPaterno").val(data["appaterno"]);
				$("#registroApMaterno").val(data["apmaterno"]);
				$("#registroMail").val(data["correo"]);
				$("#registroTelefono").val(data["telefono"]);
				Materialize.updateTextFields();
				$("#registroPassword").val("");
				$("#registroRepetirPass").val("");

				$("#btnGuardar").click(function() {


					var oUsuario = new Usuario();
					oUsuario.idUsuario = vars["idusuario"];
					oUsuario.nombre = $("#registroNombre").val();
					oUsuario.appaterno = $("#registroApPaterno").val();
					oUsuario.apmaterno = $("#registroApMaterno").val();
					oUsuario.email = $("#registroMail").val();
					oUsuario.password = $("#registroPassword").val();
					oUsuario.verificapassword = $("#registroRepetirPass").val();
					oUsuario.dd = $("#dobday").val();
					oUsuario.mm = $("#dobmonth").val();
					oUsuario.yyyy = $("#dobyear").val();
					oUsuario.placa = $("#registroPlaca").val();
					oUsuario.categorias = [];
					oUsuario.telefono = $("#registroTelefono").val();
					oUsuario.idSubasta = vars["subasta"];

					console.log(JSON.stringify(oUsuario))	;
					var categorias = [];

					$(".chkPref:checked").each(function() {

						var foo = new UsuarioCategorias(-1, $(this).attr("attr-data"));
						oUsuario.categorias.push(foo);
					});

					if (!ValidaRegistro(oUsuario)) {
						return false;
					} else {
						oUsuario.fecha_nacimiento = new Date(oUsuario.yyyy, oUsuario.mm - 1, oUsuario.dd);
					}
					postrequest("usuarios/preregistro", oUsuario,
						function(data) {
							//debugger;
							if (data > 1) {

								window.location.href = "home.php?s=login";

							}else{

								Materialize.toast("Ocurrió un error al realizar el registro" , 4000);
							}

						},
						function(data){
							Materialize.toast("Ocurrió un error al realizar el registro" , 4000);
						});


				});


			}catch(ex){
				Materialize.toast("Ocurrió un error al cargar la información del registro" , 10000);
			}
		});

	}



	/*
	 *
	 *Login
	 *
	 */
	function cargaFuncionesLogin() {
		$('.logoCar').on('click', function() {
			cargaHTML(".mainBody", "views/index.html", "", function() {
				$('.mainHeader').show();
			});
		});



		$('.logoLogin').on('click', function() {
			cargaHTML(".mainBody", "views/index.html", "", function() {
				$('.mainHeader').show();
			});
		});
		$("#btnLogin").click(function() {

			doLogin();
		});

		$("#loginPassword").keydown(function( event ) {
		  if ( event.which == 13 ) {
		    doLogin();
		  }
		});

		function doLogin(){

			oLogin = new Login();
			oLogin.email = $("#loginMail").val();
			oLogin.password = $("#loginPassword").val();

			postrequest("usuarios/login", oLogin, function(data) {

				if (data.valido) {
					sessionStorage.setItem('nombre', data["nombre"] + " " + data["appaterno"] + " " + data["apmaterno"]);
					sessionStorage.setItem('correo', data["correo"]);
					sessionStorage.setItem('publico', data["publico"]);
					sessionStorage.setItem('es_admin', data["esadmin"]);
					sessionStorage.setItem('claveapi', data["claveApi"]);
					sessionStorage.setItem('idUsuario', data["idUsuario"]);
					sessionStorage.setItem('valido', data.valido);

					if(data.verificado != Number(1)){
						window.location.href = "home.php?s=valida";

					}else{


						if($("#rememberme").is(':checked')){

							setCookie("escuderia-rememberme", data["claveApi"], true);
						}
						window.location.href = "main.php";
					}

				} else {

					Materialize.toast("Error de usuario o contraseña" , 4000);
				}

			});
		}

	}


	/********* Valida usuario **********/
	function CargaFuncionesValidaUsuario(){

			debugger;
			vars = getUrlVars();
			if(vars["claveapi"] != undefined && vars["idusuario"] != undefined && vars["correo"] != undefined){
				postrequest("usuarios/validarcorreo", {"correo":getUrlVars()["correo"], "idusuario":getUrlVars()["idusuario"], "apikey":getUrlVars()["claveapi"]},
					function(data) {

						if(Number(data["idUsuario"]) > 0)
						{
							sessionStorage.setItem('valido', 1);
							sessionStorage.setItem('nombre', data["nombre"] + " " + data["appaterno"] + " " + data["apmaterno"]);
							sessionStorage.setItem('correo', data["correo"]);
							sessionStorage.setItem('publico', data["publico"]);
							sessionStorage.setItem('es_admin', data["esadmin"]);
							sessionStorage.setItem('claveapi', data["claveApi"]);
							sessionStorage.setItem('idUsuario', data["idUsuario"]);
							sessionStorage.setItem('valido', data.valido);
							window.location.href = "main.php";
						}

					}, function(data){
						//alert("Ocurrió un error al validar el correo")
						Materialize.toast('Ocurri&oacute; un error al validar el correo.', 4000);

					});

			}else{


				debugger;

				$("#lblNombre").val(sessionStorage.getItem("nombre"));
				$("#lblCorreo").val(sessionStorage.getItem("correo"));

				$("#lblNombre").focus();
				$("#lblCorreo").focus();
				$("#lblClaveApi").focus();
				$("#lblNombre").attr("disabled", "disabled");
				$("#lblCorreo").attr("disabled", "disabled");
				$("#lblCorreo").attr("style", "color:white");
				$("#lblNombre").attr("style", "color:white");
			}

			$("#reenviarCorreo").click(function(){


				postrequest("usuarios/reenviarcorreo", {"correo":sessionStorage.getItem("correo"), "idusuario":sessionStorage.getItem("idUsuario")}, function(data) {
					if(data == 0){
						//alert("Los datos no corresponden");
						Materialize.toast('Los datos no corresponden.', 4000);
					}else if(data == 1){
						//alert("El correo de verificación fue enviado correctamente");
						Materialize.toast('El correo de verificaci&oacute;n fue enviado correctamente.', 4000);
					}else
					{
						//alert("Ocurrió un error al enviar el correo");
						Materialize.toast('Ocurri&oacute; un error al enviar el correo.', 4000);
					}

				},function(data){
					//alert("Ocurrió un error al enviar el correo");
					Materialize.toast('Ocurri&oacute; un error al enviar el correo.', 4000);
					//console.log(data);
				});

			});
			$("#btnValidaCorreo").click(function(){


				postrequest("usuarios/validarcorreo", {"correo":sessionStorage.getItem("correo"), "idusuario":sessionStorage.getItem("idUsuario"), "apikey":$("#lblClaveApi").val()},
					function(data) {
						console.log(JSON.stringify(data));
						if(Number(data["idUsuario"]) > 0)
						{
							sessionStorage.setItem('valido', 1);
							sessionStorage.setItem('claveApi', data["claveApi"]);

							window.location.href = "main.php";
						}
						else{

							//alert("Error al validar el correo");
							Materialize.toast('Error al validar el correo', 4000);
						}

					}, function(data){
						//alert("Ocurrió un error al validar el correo ");
						Materialize.toast('Ocurri&oacute; un error al validar el correo.', 4000);
					}
				);

			})

	}

	function CargaFuncionesRecuperar(){

		function validaRecuperaContrasena(){

			if($("#mail").val().trim() == ""){
				Materialize.toast("Por favor proporcioné el correo de la cuenta que desea recuperar", 4000);
				return false;

			}else if (!ValidaEmail($("#mail").val().trim())){
				Materialize.toast("Escriba un correo válido", 4000);
				return false;
			}else{
				return true;
			}
		}
		$("#mail").keydown(function (e) {
			debugger;
	        if (e.which == 13) {
	            e.preventDefault();
	            $("#btnRecuperar").click();
	         }
        });

		$("#btnRecuperar").click(function(){
			$("#spinBtnRecuperar").show();
			if(validaRecuperaContrasena()){
				postrequest("usuarios/recuperar",{"mail":$("#mail").val()}, function(data){
					if(data == -1){
						//alert("No se encontro ninguna cuenta con estos datos");
						Materialize.toast('No se encontro ninguna cuenta con estos datos.', 4000);
					}else{
						$("#spinBtnRecuperar").hide();
						//alert("Le envíamos un correo, por favor revise su bandeja de entrada. Recuerde que el correo podría llegar a la bandeja de spam o correo no deseado");
						Materialize.toast('Le env&iacute;amos un correo, por favor revise su bandeja de entrada. Recuerde que el correo podr&iacute;a llegar a sla bandeja de spam o correo no deseado.', 4000);
						window.location.href="?s=nuevacontrasena&correo="+$("#mail").val();
					}
				},
				function(data){
					//alert("Ocurrió un error al recuperar la contraseña");
					Materialize.toast('Ocurri&oacute; un error al recuperar la contrase&ntilde;a', 4000);
				} );
			}
		});
	}

	function CargaFuncionesNuevaContrasena(){

		$("#rowContrasena").hide();
		$("#rowRepetirContrasena").hide();
		$("#divValidaRepetirPassword").hide();
		vars = getUrlVars();
		$("#registroMail").val("");
		

		$("#claveApi").val("");
		$("#registroMail").val(vars["correo"]);
		if(vars["claveapi"] != undefined){
			$("#claveApi").val(vars["claveapi"]);
			validaCodigoVericacion("#claveApi");
		}
		$("#btnNuevaContrasena").click(function(){
			postrequest("usuarios/cambiarcontasena",{"mail": $("#registroMail").val() ,"claveapi":$("#claveApi").val(),"password":$("#registroPassword").val()},
				function(data){
					if(data == 0){
						//alert("Ocurrió un error al guardar la contraseña");
						Materialize.toast('Ocurri&oacute; un error al guardar la contrase&ntilde;a', 4000);
					}else{
						window.location.href = "?s=login";
					}
				},
				function(data){
					//alert("Ocurrió un error al guardar la contraseña");
					Materialize.toast('Ocurri&oacute; un error al guardar la contrase&ntilde;a', 4000);
				}
			);
		});
	}

});
