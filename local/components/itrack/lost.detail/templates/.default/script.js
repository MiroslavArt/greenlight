$(document).ready(function() {

    // текущая дата по умолчанию
    var options = {
        year: 'numeric',
        month: 'numeric',
        day: 'numeric',
        timezone: 'UTC'
    };

    $(".ico_date").each(function (index, el){
        // Для каждого элемента сохраняем значение в personsIdsArray,
        // если значение есть.
        $(el).val(new Date().toLocaleString("ru", options))
    });

    /*$(".reqdoclink").fancybox({
        afterClose: function(){
            location.reload()
        }
    });*/


    $( ".form_popup" ).submit(function( event ) {
        event.preventDefault();
        var formData = $(this).serializeArray();  // создаем переменную, которая содержит закодированный набор элементов формы в виде строки
        BX.ajax.runAction('itrack:custom.api.signal.addLostdoc', {
            data: {
                formdata: formData
            }
        }).then(function (response) {
            if(response.data=='added') {
                location.reload();
            } else {
                $("#mistake").text(response.data)
            }
        }, function (error) {
            //сюда будут приходить все ответы, у которых status !== 'success'
            console.log(error)
        });
    })

    $( ".form_popup1" ).submit(function( event ) {
        event.preventDefault();
        var formData = $(this).serializeArray();  // создаем переменную, которая содержит закодированный набор элементов формы в виде строки
        BX.ajax.runAction('itrack:custom.api.signal.updateLossdesc', {
            data: {
                formdata: formData
            }
        }).then(function (response) {
            if(response.data=='updated') {
                location.reload();
            } else {
                $("#mistake1").text(response.data)
            }
        }, function (error) {
            //сюда будут приходить все ответы, у которых status !== 'success'
            console.log(error)
        });
    })


    $( ".form_popup2" ).submit(function( event ) {
        event.preventDefault();
        var formData = $(this).serializeArray();  // создаем переменную, которая содержит закодированный набор элементов формы в виде строки
        BX.ajax.runAction('itrack:custom.api.signal.closeLoss', {
            data: {
                formdata: formData
            }
        }).then(function (response) {
            if(response.data=='added') {
                location.reload();
            } else {
                $("#mistake2").text(response.data)
            }
        }, function (error) {
            //сюда будут приходить все ответы, у которых status !== 'success'
            console.log(error)
        });
    })
})