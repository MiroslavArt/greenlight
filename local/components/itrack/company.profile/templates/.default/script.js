$(function () {
	$("[name=LOGO]").on("change", function(e) {
		var file = e.target.files[0];

		if (!file.type.match('image.*')) {
			alert("Загрузите, пожалуйста, картинку");
			this.value = "";
			return;
		}

		var reader = new FileReader();
		reader.readAsDataURL(file);
		reader.onload = function(e) {
			$(".logo-preview").attr("src", e.target.result);
		};
	})
});
