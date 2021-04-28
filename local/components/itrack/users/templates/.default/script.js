$(document).ready(function() {

    //добавление договора страхования
    $('#add_contract').click(function(){
        var selcompany = $("#company").val();
        BX.ajax.runAction('itrack:custom.api.signal.getParticipantstargets', {
            data: {
                type: 'contract',
                participant: selcompany
            }
        }).then(function (response) {
            var select = $("<select></select>").addClass("select js_select sel_contracts");
            select.append($("<option></option>").attr("value", "N/A").text("N/A"));
            $.each(response.data,function(index,value){
                select.append($("<option></option>").attr("value", value.ID).text(value.NAME));
            });
            $("#contract_container").prepend(select)
            select.select2({
                //minimumResultsForSearch: -1,
                matcher: matchCustom,
                placeholder: "",
                width: '100%'
            });
        }, function (error) {
            //сюда будут приходить все ответы, у которых status !== 'success'
            console.log(error)
        });
    });

    //добавление убытка
    $('#add_loss').click(function(){
        var selcompany = $("#company").val();
        BX.ajax.runAction('itrack:custom.api.signal.getParticipantstargets', {
            data: {
                type: 'lost',
                participant: selcompany
            }
        }).then(function (response) {
            var select = $("<select></select>").addClass("select js_select sel_losses");
            select.append($("<option></option>").attr("value", "N/A").text("N/A"));
            $.each(response.data,function(index,value){
                select.append($("<option></option>").attr("value", value.ID).text(value.NAME));
            });
            $("#loss_container").prepend(select)
            select.select2({
                //minimumResultsForSearch: -1,
                matcher: matchCustom,
                placeholder: "",
                width: '100%'
            });
        }, function (error) {
            //сюда будут приходить все ответы, у которых status !== 'success'
            console.log(error)
        });
    });

    // изменение компании
    $("#company").change(function(e) {
        $("#contract_container").empty()
        $("#loss_container").empty()
        var selcompany = $(this).val();
        BX.ajax.runAction('itrack:custom.api.signal.getParticipantstargets', {
            data: {
                type: 'contract',
                participant: selcompany
            }
        }).then(function (response) {

            var select = $("<select></select>").addClass("select js_select sel_contracts");
            select.append($("<option></option>").attr("value", "N/A").text("N/A"));
            $.each(response.data,function(index,value){
                select.append($("<option></option>").attr("value", value.ID).text(value.NAME));
            });
            $("#contract_container").prepend(select)
            select.select2({
                //minimumResultsForSearch: -1,
                matcher: matchCustom,
                placeholder: "",
                width: '100%'
            })
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
            var select = $("<select></select>").addClass("select js_select sel_losses");
            select.append($("<option></option>").attr("value", "N/A").text("N/A"));
            $.each(response.data,function(index,value){
                select.append($("<option></option>").attr("value", value.ID).text(value.NAME));
            });
            $("#loss_container").prepend(select)
            select.select2({
                //minimumResultsForSearch: -1,
                matcher: matchCustom,
                placeholder: "",
                width: '100%'
            })
        }, function (error) {
            //сюда будут приходить все ответы, у которых status !== 'success'
            console.log(error)
        });
    })

    //сохранение пользователя
    $( ".js_user_add" ).submit(function( event ){
        event.preventDefault();
        var contracts = []
        var losses = []
        var userdata = {}
        userdata.lastname = $("#last_name").val()
        userdata.name = $("#name").val()
        userdata.secondname = $("#second_name").val()
        userdata.email = $("#email").val()
        userdata.pwd = $("#email").attr('data-id')
        userdata.company = $("#company").val()
        userdata.persphone = $("#persphone").val()
        userdata.workphone = $("#workphone").val()
        userdata.addphone = $("#code").val()
        if($(".switch").hasClass('active')) {
            userdata.superuser = true
        } else {
            userdata.superuser = false
        }
        userdata.position = $("#position").val()
        $(".sel_contracts").each(function(index,value){
            contracts.push($(value).val())
        });
        $(".sel_losses").each(function(index,value){
            losses.push($(value).val())
        });

        userdata.contract = contracts
        userdata.loss = losses
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