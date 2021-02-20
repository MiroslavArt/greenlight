$(function(){
	$('.class').click(function(){
		$(this).toggleClass('active');
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
	$('.js_checkbox input').click(function(){
		$(this).parent().toggleClass('active');
	});
	/* Select */
	$('.js_select').select2({
		minimumResultsForSearch: -1,
		placeholder: "",
		width: '100%'
	});

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
					console.log('submit')

					if($(form).hasClass('js-submit-onvalid')) {
						form.submit();
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

});