$(function(){
    $('.js_open_dropdown').click(function(){
        if ($(this).hasClass('active')) {
            // ���� ������� �� ������ � ������� active (�� ���� ���� ��� ������)
            $('.company_card_dropdown').fadeOut();
            $('.company_card_list li').removeClass('active');
        }else{
            //������� ������ ��� �������� �����
            $('.company_card_dropdown').fadeOut();
            $('.company_card_list li').removeClass('active');
            //���������� ������ ������������ �������������
            $(this).closest('li').find('.company_card_dropdown').fadeToggle();
            $(this).closest('li').toggleClass('active');
        }
    });
    //��������� �� ��� ������� �� ����� ��������� �������
    $(document).click(function(event){
        if (!$(event.target).closest('.company_card_dropdown,.js_open_dropdown').length){
            $('body').find('.company_card_dropdown').fadeOut();
            $('.company_card_list li').removeClass('active');
        }
    });
    //��������� �� ��� ������� �� esc
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