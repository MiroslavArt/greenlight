$(document).ready(function() {
    //console.log("script")
    $( ".form_popup" ).submit(function( event ){
        event.preventDefault();
        var userdata = {}
        userdata.lastname = $("#last_name").val()
        userdata.name = $("#name").val()
        userdata.secondname = $("#second_name").val()
        userdata.email = $("#email").val()
        userdata.pwd = $("#email").attr('data-id')
        userdata.company = $("#company").val()
        userdata.persphone = $("#persphone").val()
        userdata.workphone = $("#workphone").val() + ' ' + $("#code").val()
        if($(".switch").hasClass('active')) {
            userdata.superuser = true
        } else {
            userdata.superuser = false
        }
        userdata.position = $("#position").val()
        userdata.contract = $("#contract").val()
        userdata.loss = $("#loss").val()
        if(!userdata.email) {
            $("#mistake").text("Не заполнено обязательное поле Email!")
        } else {
            BX.ajax.runAction('itrack:custom.api.signal.addUser', {
                data: {
                    userdata: userdata
                }
            }).then(function (response) {
                console.log(response)
                if(response.data = 'added') {
                    location.reload();
                } else {
                    $("#mistake").text(response.data)
                }
            }, function (error) {
                //сюда будут приходить все ответы, у которых status !== 'success'
                console.log(error)
                $("#mistake").text(error)
            });
        }
    })
})