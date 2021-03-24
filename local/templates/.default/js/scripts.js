$(function(){
	$('.js_open_dropdown').click(function(){
		if ($(this).hasClass('active')) {
			// если кликаем по кнопке с классом active (то есть блок уже открыт)
			$('.company_card_dropdown').fadeOut();
        	$('.company_card_list li').removeClass('active');
		}else{
			//сначала прячем все открытые блоки
			$('.company_card_dropdown').fadeOut();
			$('.company_card_list li').removeClass('active');
			//показываем нужный относительно родительского
			$(this).closest('li').find('.company_card_dropdown').fadeToggle();
			$(this).closest('li').toggleClass('active');
		}
	});
	//закрываем всё при нажатии на любую свободную область
	$(document).click(function(event){
		if (!$(event.target).closest('.company_card_dropdown,.js_open_dropdown').length){
			$('body').find('.company_card_dropdown').fadeOut();
			$('.company_card_list li').removeClass('active');
		}
	});
	//закрываем всё при нажатии на esc
	$(document).keyup(function(event){
		if (event.keyCode == 27){
			$('.company_card_dropdown').fadeOut();
			$('.company_card_list li').removeClass('active');
		}
	});
	$('.js_open_menu').click(function(){
		$('.main_menu').toggleClass('open');
		var menuHide = $('.user_content,.menu_btn_container a span');
		if(menuHide.is(':visible')){
			menuHide.fadeOut(0);
		}else{
			menuHide.delay(300).fadeIn();
		}
	});
	$(document).on('click', '.js_checkbox input', function(e){
		//console.log(e)
		if(BX.hasClass(e.target.parentElement, 'leader') && !BX.hasClass(e.target.parentElement, 'active')) {
            var cardcont = BX.findParent(e.target.parentElement, {"class" : "company_card_container"})
            var leaders = BX.findChild(cardcont, {"class" : "leader"}, true, true)
            leaders.forEach(function(element){
                if(element != e.target.parentElement && BX.hasClass(element, 'active')) {
                    $(element).toggleClass('active')
                } else if(element == e.target.parentElement ) {
                    $(element).toggleClass('active')
                }
            })
		} else if(BX.hasClass(e.target.parentElement, 'leader') && BX.hasClass(e.target.parentElement, 'active')) {
            $(this).parent().toggleClass('active')
        }
		if(BX.hasClass(e.target.parentElement, 'flag') && !BX.hasClass(e.target.parentElement, 'active')) {
			var cardcont = BX.findParent(e.target.parentElement, {"class" : "gray_blocks"})
			var leaders = BX.findChild(cardcont, {"class" : "flag"}, true, true)
			leaders.forEach(function(element){
				if(element != e.target.parentElement && BX.hasClass(element, 'active')) {
					$(element).toggleClass('active')
				} else if(element == e.target.parentElement ) {
					$(element).toggleClass('active')
				}
			})
		} else if(BX.hasClass(e.target.parentElement, 'flag') && BX.hasClass(e.target.parentElement, 'active')) {
			$(this).parent().toggleClass('active')
		}
		if(BX.hasClass(e.target.parentElement, 'switch')) {
			$(this).parent().toggleClass('active');
		}
	});
    $(document).on('click', '.js_delete', function(e){
        BX.remove(e.target.parentElement)
    });
	/* Radio */
	$('.js_radio').click(function(){
		$(this).closest('.radio_container').find('.js_radio').removeClass('active');
		$(this).toggleClass('active');
	});
	/* Select */
	function matchCustom(params, data) {
		// If there are no search terms, return all of the data
		if ($.trim(params.term) === '') {
			return data;
		}

		// Do not display the item if there is no 'text' property
		if (typeof data.text === 'undefined') {
			return null;
		}

		// `params.term` should be the term that is used for searching
		// `data.text` is the text that is displayed for the data object
		if (data.text.indexOf(params.term) > -1) {
			var modifiedData = $.extend({}, data, true);
			modifiedData.text += ' (найдено)';

			// You can return modified objects from here
			// This includes matching the `children` how you want in nested data sets
			return modifiedData;
		}

		// Return `null` if the term should not be displayed
		return null;
	}

	$('.js_select').select2({
		//minimumResultsForSearch: -1,
		matcher: matchCustom,
		placeholder: "",
		width: '100%'
	});

	/* Datepicker */
	$('.js_datapicker').datepicker({
		// Можно выбрать тольо даты, идущие за сегодняшним днем, включая сегодня
		minDate: new Date()
	})

	//form validation
	var initFormValidation = function() {
		$.each($(".js-needs-validation"), function(){
			$(this).validate({
				errorClass: 'error',
				highlight: function(element, errorClass, validClass) {
					$(element).parent('.input_container').addClass(errorClass);
					if (element.type === 'checkbox') {
						$(element).parent('.checkbox').addClass(errorClass);
					}
				},
				unhighlight: function(element, errorClass, validClass) {
					$(element).parent('.input_container').removeClass(errorClass);
					if (element.type === 'checkbox') {
						$(element).parent('.checkbox').removeClass(errorClass);
					}
					if(Object.keys(this.invalid).length == 0){
						//$(".js-error-wrapper").hide();
					}
				},
				errorPlacement: function(error, element) {
					/*var $label = $(element).parent('fieldset').find('.label');
                    if (element[0].type != 'checkbox') {
                        error.insertAfter($label);
                    }
                    $(element).addClass('error');*/
				},
				submitHandler: function(form, e) {
					e.preventDefault();

					var url = $(form).data('url');
					var method = $(form).attr('method');

					if($(form).hasClass('js-submit-onvalid')) {
						form.submit();
					}

					if($(form).hasClass('js-submit')) {
						mySubmit({
							'url': url,
							'method': method,
							'data': $(form).serialize(),
						});
					}

				},
				rules: {
				}
			});


			$(this).find("[name='USER_PASSWORD']").rules("add", {"required": true});
			$(this).find("[name='USER_CONFIRM_PASSWORD']").rules("add", {"required": true, equalTo : '[name="USER_PASSWORD"]'});

			$(this).find(".js-email-validate").rules("add", {"email": true, "required": true});
			$(this).find(".js-checkbox-validate").each(function(i) {
				$(this).rules("add", {"required": true});
			});
		});
	}

	initFormValidation();

	let mySubmit = function (options) {
		let url = options.url?options.url:'',
			method = options.method?options.method:'GET',
			data = options.data?options.data:'',
			scrollTo = options.scrollTo?options.scrollTo:'',
			historyUrl = options.historyUrl?options.historyUrl:'',
			that = options.that?options.that:''//click event target
		;

		return new Promise(function (resolve, reject) {
			$.ajax({
				'url': url,
				'method': method,
				dataType: 'json',
				'data': data,
				beforeSend: function () {
					$('.b-preloader-radial').addClass('active');
				},
				complete: function () {
					$('.b-preloader-radial').removeClass('active');
				},
				success: function (json) {
					if (Boolean(json.success)) {

						if (Boolean(json.html)) {
							if (Boolean(json.htmlContainer)) {
								htmlContainer = json.htmlContainer;
							}
							if (htmlContainer) {
								$(htmlContainer).html($(json.html).find(htmlContainer).html());
							}
						}
						if (Boolean(json.timeoutReload)) {
							setTimeout(function(){
								location.reload(true);
							}, json.timeoutReload);
						}
						if (Boolean(json.reload)) {
							location.reload(true);
						}
						if (Boolean(json.redirect)) {
							location.href = json.redirect;
						}

						if (Boolean(json.url)) {
							history.pushState({json: json.json}, json.url, json.url);
						}
						if (historyUrl.length) {
							history.pushState(null,null, historyUrl);
						}

						if(Boolean(json.error_message)) {
							//UIkit.notification('<h3>' + json.error_message + '</h3>');
						}

					}

					if (Boolean(json.errors)) {
						for (let key in json.errors) {
							let error = {};
							error[key] = json.errors[key];
							window.validator.showErrors(error);
						}
					}

				}
			}).done(resolve).fail(reject);
		});
	};

});