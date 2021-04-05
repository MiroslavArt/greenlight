$(document).ready(function() {

    $(".reqdoclink").fancybox({
        afterClose: function(){
            location.reload()
        }
    });


    $( ".form_popup" ).submit(function( event ) {
        event.preventDefault();
        var formData = $(this).serializeArray();  // создаем переменную, которая содержит закодированный набор элементов формы в виде строки
        console.log(formData);
        BX.ajax.runAction('itrack:custom.api.signal.addLostdoc', {
            data: {
                formdata: formData
            }
        }).then(function (response) {
            console.log(response)
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
})