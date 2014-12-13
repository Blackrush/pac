(function(document) {
	var elements = document.querySelectorAll('.row_del');

	var i;
	var it;
	for (i = 0; i < elements.length; i++) {
		it = elements[i];
		console.log("intercepted!");

		it.onclick = function() {
			return confirm("Êtes-vous vraiment sûr?");
		};
	}
})(document);
