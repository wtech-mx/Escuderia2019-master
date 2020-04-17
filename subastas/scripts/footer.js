
$( document ).ready(function() {
   $(".mainFooter").load("views/footer.html", function() {
		console.log("Load was performed for footer.");
		var fHeight = $('footer').outerHeight(true);
		$('.mainContainer').css('margin-bottom', fHeight);
	}); 
});
