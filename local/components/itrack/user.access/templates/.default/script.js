$(function () {
	$("[data-filter-url]").on("click", function () {
		window.location.href = $(this).data("filter-url");
	});


	var requestIsSent = false;


	$(".js-bind-curator, .js-unbind-curator").on("click", function (e) {
		e.preventDefault();
		if (requestIsSent) return;

		var $this = $(this);
		var $row = $this.closest("[data-target-id]");

		$this.parent().find("*").toggle();

		sendRequest($row, {
			action: "binding",
			enable: $this.data("enable"),
		}, function () {

		})
	});

	$(".js-access-switch").on("click", function () {
		if (requestIsSent) return;

		var $this = $(this);
		var $row = $this.closest("[data-target-id]");

		sendRequest($row, {
			action: "switch",
			switchType: $this.data("switch-type"),
			enable: $this.is(":checked") ? "y" : "n",
		})
	});

	function sendRequest($row, data, callback) {
		requestIsSent = true;

		$.extend(data, {
			sessid: $("[data-sessid]").data("sessid"),
			targetId: $row.data("target-id"),
			targetType: $row.data("target-type"),
		});

		$.ajax({
			method: "POST",
			data: data,
			dataType: "json",
		}).done(function (answer) {
			if (answer.success !== "y") {
				alert("В процессе выполнения запроса возникла ошибка");
				document.location.reload();
			}

			if (callback)
				callback();
		}).fail(function () {
			alert("В процессе выполнения запроса возникла ошибка");
			document.location.reload();
		}).always(function () {
			requestIsSent = false;
		});
	}
});
