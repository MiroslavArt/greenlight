$(function(){
	$('.js_open_list').click(function(){
		var $this = $(this);
		$this.prev('.js_list_docs').slideToggle();
		$this.toggleClass('active');
		if($this.hasClass('active')){
			$this.text('Свернуть весь список');
		} else {
			$this.text('Развернуть весь список');
		}
	});
	$('.js_desc_show').click(function(){
		var $this = $(this);
		$this.prev('.js_desc_hide').slideToggle();
		$this.toggleClass('active');
		if($this.hasClass('active')){
			$this.text('Свернуть описание');
		} else {
			$this.text('Посмотреть описание полностью');
		}
	});
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
	$('.js_checkbox input').click(function(){
		$(this).parent().toggleClass('active');
	});
	/* Radio */
	$('.js_radio').click(function(){
		$(this).closest('.radio_container').find('.js_radio').removeClass('active');
		$(this).toggleClass('active');
	});
	/* Select */
	$('.js_select').select2({
		minimumResultsForSearch: -1,
		placeholder: "",
		width: '100%'
	});
});