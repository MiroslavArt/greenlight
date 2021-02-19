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
});