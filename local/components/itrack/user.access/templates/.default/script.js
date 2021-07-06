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
			if ($this.data("enable") === "n") {
				// these two lines only disable UI switches, but do not send any requests
				$row.find(".js-access-switch:checked[data-switch-type=acceptance]").trigger("click");
				$row.find(".js-access-switch:checked[data-switch-type=notification]").trigger("click");

				sendRequest($row, { action: "switch", switchType: "notification", enable: "n" });
				sendRequest($row, { action: "switch", switchType: "acceptance",   enable: "n" });
			}
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
				var err = answer.error === "There must be at least one curator from company."
					? "У договора должен быть как минимум один куратор"
					: "В процессе выполнения запроса возникла ошибка";

				alert(err);
				document.location.reload();
				return;
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
