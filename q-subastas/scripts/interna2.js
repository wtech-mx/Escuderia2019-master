$(document).ready(function() {

	function Siguiente() {
		var idx = parseInt($("#modalImgGaleria").attr("attr-idx"));
		if (idx >= $("#modalSnapshots > div > img").length) {
			$("#modalImgGaleria").attr("src", $($("#modalSnapshots > div > img")[0]).attr("src"));
			$("#modalImgGaleria").attr("alt", $($("#modalSnapshots > div > img")[0]).attr("alt"));
			$("#modalImgGaleria").attr("attr-idx", $($("#modalSnapshots > div > img")[0]).attr("attr-idx"));
		} else {
			$("#modalImgGaleria").attr("src", $($("#modalSnapshots > div > img")[idx]).attr("src"));
			$("#modalImgGaleria").attr("alt", $($("#modalSnapshots > div > img")[idx]).attr("alt"));
			$("#modalImgGaleria").attr("attr-idx", $($("#modalSnapshots > div > img")[idx]).attr("attr-idx"));

		}

	}

	function Anterior() {
		var idx = parseInt($("#modalImgGaleria").attr("attr-idx")) - 2;
		if (idx < 0) {
			//alert("if");
			$("#modalImgGaleria").attr("src", $($("#modalSnapshots > div > img")[$("#modalSnapshots > div > img").length]).attr("src"));
			$("#modalImgGaleria").attr("alt", $($("#modalSnapshots > div > img")[$("#modalSnapshots > div > img").length]).attr("alt"));
			$("#modalImgGaleria").attr("attr-idx", $($("#modalSnapshots > div > img")[$("#modalSnapshots > div > img").length]).attr("attr-idx"));

			$("#modalImgGaleria").attr("src", $($("#modalSnapshots > div > img")[$("#modalSnapshots > div > img").length - 1]).attr("src"));
			$("#modalImgGaleria").attr("alt", $($("#modalSnapshots > div > img")[$("#modalSnapshots > div > img").length - 1]).attr("alt"));
			$("#modalImgGaleria").attr("attr-idx", $($("#modalSnapshots > div > img")[$("#modalSnapshots > div > img").length - 1]).attr("attr-idx"));
		} else {

			$("#modalImgGaleria").attr("src", $($("#modalSnapshots > div > img")[idx]).attr("src"));
			$("#modalImgGaleria").attr("alt", $($("#modalSnapshots > div > img")[idx]).attr("alt"));
			$("#modalImgGaleria").attr("attr-idx", $($("#modalSnapshots > div > img")[idx]).attr("attr-idx"));

		}

	}


	$(".mainBody").load("views/interna2.html", function() {

		var dialog = $("#modal").dialog({
			autoOpen : false,
			height : 400,
			width : 400,
			modal : true,
			buttons : {
				"Anteror" : Anterior,
				"Siguiente" : Siguiente

			},
			close : function() {
				$("#modalImgGaleria").attr("src", "");
				$("#modalSnapshots").html("");
			}
		});
		
		console.log("Load was performed.");

		$("#imgPrincipal").click(function() {

			$("#modalSnapshots").html("");
			$("#modalSnapshots").html($("#imgSnapshots").html());
			$("#modalImgGaleria").attr("src", $("#imgPrincipal").attr("src"));
			$("#modalImgGaleria").attr("alt", $("#imgPrincipal").attr("alt"));
			$("#modalImgGaleria").attr("attr-idx", $("#imgPrincipal").attr("attr-idx"));
			$("#modalSnapshots > div > img").click(function() {

				$("#modalImgGaleria").attr("src", $(this).attr("src"));
				$("#modalImgGaleria").attr("alt", $(this).attr("alt"));
				$("#modalImgGaleria").attr("attr-idx", $(this).attr("attr-idx"));

			});

			dialog.dialog("open");
			

		});

		$('.dateTimeHeader').hide();

		$('.btnPujar').on('click', function() {
			$('.wndwPujarAhora').show();
		});
		$('.wndwPujarAhora > .btnClose').on('click', function() {
			$('.wndwPujarAhora').hide();
			$('#montoPujarAhora').val('');
			$('.msgConf').hide();
		})
		$('#btnSubastar').on('click', function() {
			$('.msgConf').show();
		});
	});

});
