$(document).ready(function() {

    // изменение компании
    $("#company").change(function(e) {
        var selcompany = $(this).val();
        $("#contract").empty()
        $("#loss").empty()
        BX.ajax.runAction('itrack:custom.api.signal.getParticipantstargets', {
            data: {
                type: 'contract',
                participant: selcompany
            }
        }).then(function (response) {
            $.each(response.data,function(index,value){
                $("#contract").append($("<option></option>").attr("value", value.ID).text(value.NAME));
            });
        }, function (error) {
            //сюда будут приходить все ответы, у которых status !== 'success'
            console.log(error)
        });
        BX.ajax.runAction('itrack:custom.api.signal.getParticipantstargets', {
            data: {
                type: 'lost',
                participant: selcompany
            }
        }).then(function (response) {
            $.each(response.data,function(index,value){
                $("#loss").append($("<option></option>").attr("value", value.ID).text(value.NAME));
            });
        }, function (error) {
            //сюда будут приходить все ответы, у которых status !== 'success'
            console.log(error)
        });
    })

    //сохранение пользователя
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
                if(response.data=='added') {
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