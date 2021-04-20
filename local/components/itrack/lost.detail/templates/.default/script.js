$(document).ready(function() {

    // текущая дата по умолчанию
    var options = {
        year: 'numeric',
        month: 'numeric',
        day: 'numeric',
        timezone: 'UTC'
    };

    //$(".ico_date").each(function (index, el){
        // Для каждого элемента сохраняем значение в personsIdsArray,
        // если значение есть.
    $("#term_date").val(new Date().toLocaleString("ru", options))
    //});

    /*$(".reqdoclink").fancybox({
        afterClose: function(){
            location.reload()
        }
    });*/
    $( ".form1" ).submit(function( event ) {
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

    $( ".form2" ).submit(function( event ) {
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

    $( ".form3" ).submit(function( event ) {
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

    // редактирование убытка
    // клиент и его кураторы
    var clientid = $( "#kur_client_search_ins").attr('data-id')
    BX.ajax.runAction('itrack:custom.api.signal.getUsers', {
        data: {
            company: clientid
        }
    }).then(function (response) {
        $( "#kur_client_search_ins").autocomplete({
            source: response.data,
            focus: function( event, ui ) {
                return false;
            },
            select: function( event, ui ) {
                $(this).parent().parent().toggleClass('hidden');
                var form = BX.findParent(this, {"tag" : "form"});
                //console.log(form);
                var kurids = BX.findChild(form, {"class" : "inserted_kur_co_id"}, true, true)
                var foundkur = false
                kurids.forEach(function(element){
                    if(element.getAttribute("value") == ui.item.value) {
                        foundkur = true
                    }
                })
                if(foundkur==false) {
                    $( "#kur_client_search_ins").val(ui.item.label);
                    kuratoradd($( "#ins_kur_card" ), ui.item, 4)
                }
                return false;
            }
        });

    }, function (error) {
        //сюда будут приходить все ответы, у которых status !== 'success'
        console.log(error);
    });

    // брокер и его кураторы
    var brokerid = $( "#kur_broker_search_ins" ).attr('data-id');
    BX.ajax.runAction('itrack:custom.api.signal.getUsers', {
        data: {
            company: brokerid
        }
    }).then(function (response) {
        $( "#kur_broker_search_ins").autocomplete({
            source: response.data,
            focus: function( event, ui ) {
                return false;
            },
            select: function( event, ui ) {
                $(this).parent().parent().toggleClass('hidden');
                var form = BX.findParent(this, {"tag" : "form"});
                //console.log(form);
                var kurids = BX.findChild(form, {"class" : "inserted_kur_co_id"}, true, true)
                var foundkur = false
                kurids.forEach(function(element){
                    if(element.getAttribute("value") == ui.item.value) {
                        foundkur = true
                    }
                })
                if(foundkur==false) {
                    $( "#kur_broker_search_ins").val(ui.item.label);
                    kuratoradd($( "#brok_kur_card" ), ui.item, 1)
                }
                return false;
            }
        });
    }, function (error) {
        //сюда будут приходить все ответы, у которых status !== 'success'
        console.log(error);
    });

    // ск и его кураторы
    $(".inserted_co_id").each(function (index, el){
        var compid = $(el).val()
        var cardblock = $(el).parent().parent().parent().next()
        var addkurtext = $("<span></span>").text("Добавить куратора")
        var addkur = $("<a>").attr("href", '#').attr("class","link ico_add js_add")
        addkur.append(addkurtext)
        var inplockdiv = $("<div></div>").attr("class", "form_row brok_comp hidden")
        var kursearch = $("<div></div>").attr("class", "input_container without_small")
        var kursearchinp = $("<input>").attr("type", "text").attr("class", "text_input inserted_co_label kur_select")
            .attr("placeholder", 'Выберите куратора(-ов) от СК по вводу букв из ФИО')
        kursearch.append(kursearchinp)
        inplockdiv.append(kursearch)

        $(el).parent().parent().after(addkur)
        addkur.after(inplockdiv)

        BX.ajax.runAction('itrack:custom.api.signal.getUsers', {
            data: {
                company: compid
            }
        }).then(function (response) {
            kursearchinp.autocomplete({
                source: response.data,
                focus: function( event, ui ) {
                    return false;
                },
                select: function( event, ui ) {
                    $(this).parent().parent().toggleClass('hidden');
                    var form = BX.findParent(this, {"tag": "form"});
                    //console.log(form);
                    var kurids = BX.findChild(form, {"class": "inserted_kur_co_id"}, true, true)
                    var foundkur = false
                    kurids.forEach(function(element){
                        if(element.getAttribute("value") == ui.item.value) {
                            foundkur = true
                        }
                    })
                    if(foundkur==false) {
                        kursearchinp .val(ui.item.label);
                        kuratoradd(cardblock, ui.item, 2)
                    }
                    return false;
                }
            });
        }, function (error) {
            //сюда будут приходить все ответы, у которых status !== 'success'
            console.log(error);
        });

    })

    // аджастер и его кураторы
    var adjcompanies = []
    BX.ajax.runAction('itrack:custom.api.signal.getCompanies', {
        data: {
            type: '3'
        }
    }).then(function (response) {
        adjcompanies = response.data
        $( "#search_adj" ).autocomplete({
            source: adjcompanies,
            focus: function( event, ui ) {
                $( "#search_adj" ).val( ui.item.label );
                return false;
            },
            select: function( event, ui ) {
                $(this).parent().parent().toggleClass('hidden');
                $( "#search_adj" ).val( ui.item.label );
                //$( "#sel_ins" ).val( ui.item.value );
                //console.log(this)
                var form = BX.findParent(this, {"tag" : "form"});
                //console.log(form);

                var insids = BX.findChild(form, {"class" : "inserted_co_id"}, true, true);
                var foundcomp = false
                insids.forEach(function(element){
                    if(element.getAttribute("value") == ui.item.value) {
                        foundcomp = true
                    }
                });
                if(foundcomp==false) {
                    var allblocks = $("<div></div>").attr("class", "ins_adjuster")
                    var coblock = $("<div></div>").attr("class", "gray_block delete_left")
                    var delblock = $("<span></span>").attr("class", "delete js_delete1")
                    var inplock = $("<div></div>").attr("class", "input_container with_flag")
                    var labelcomp =  $("<label></label>").attr("class", "big_label").text(ui.item.label)
                    var inpcomp =  $("<input>").attr("type", "hidden").attr("class", "inserted_co_id").attr("data-type", 'aj').val(ui.item.value)
                    var labelleader = $("<label></label>").attr("class", "flag js_checkbox")
                    var leaderbox =  $("<input>").attr("type", "checkbox").attr("data-insc-leader", ui.item.value)
                    labelleader.append(leaderbox)
                    var addkur = $("<a>").attr("href", '#').attr("class","link ico_add js_add")
                    var addkurtext = $("<span></span>").text("Добавить куратора")
                    addkur.append(addkurtext)
                    var inplockdiv = $("<div></div>").attr("class", "form_row brok_comp hidden")
                    var kursearch = $("<div></div>").attr("class", "input_container without_small")
                    var kursearchinp = $("<input>").attr("type", "text").attr("class", "text_input inserted_co_label kur_select")
                        .attr("placeholder", 'Выберите куратора(-ов) от аджастера по вводу букв из ФИО')
                    var cardblock = $("<div></div>").attr("class", "company_card_container")
                    kursearch.append(kursearchinp)
                    inplockdiv.append(kursearch)
                    inplock.append(labelcomp)
                    inplock.append(inpcomp)
                    inplock.append(labelleader)
                    coblock.append(delblock)
                    coblock.append(inplock)
                    allblocks.append(coblock)
                    allblocks.append(addkur)
                    allblocks.append(inplockdiv)
                    allblocks.append(cardblock)
                    $("#ins_adjusters").append(allblocks)
                    BX.ajax.runAction('itrack:custom.api.signal.getUsers', {
                        data: {
                            company: ui.item.value
                        }
                    }).then(function (response) {
                        //console.log(response);
                        kursearchinp.autocomplete({
                            source: response.data,
                            focus: function( event, ui ) {
                                return false;
                            },
                            select: function( event, ui ) {
                                $(this).parent().parent().toggleClass('hidden');
                                var form = BX.findParent(this, {"tag" : "form"});
                                //console.log(form);
                                var kurids = BX.findChild(form, {"class" : "inserted_kur_co_id"}, true, true)
                                var foundkur = false
                                kurids.forEach(function(element){
                                    if(element.getAttribute("value") == ui.item.value) {
                                        foundkur = true
                                    }
                                })
                                if(foundkur==false) {
                                    kursearchinp.val(ui.item.label);
                                    kuratoradd(cardblock, ui.item, 3)
                                }
                                return false;
                            }
                        });
                    }, function (error) {
                        //сюда будут приходить все ответы, у которых status !== 'success'
                        console.log(error);

                    });
                }
                return false;
            }
        })
    }, function (error) {
        //сюда будут приходить все ответы, у которых status !== 'success'
        console.log(error);
    });


    $( ".form4" ).submit(function( event ) {
        event.preventDefault();

        var inscompanies = []
        var insleader = 0
        var adjusters = []
        var adjleader = 0
        var kurators = []
        var kurleaders = []
        var kuratorscl = []
        var kuratorsbr = []
        var kuratorsins = []
        var kuratorsadj = []
        var needaccept = []
        var neednotify = []

        var clientid = $( "#kur_client_search_ins").attr('data-id')
        var brokerid = $( "#kur_broker_search_ins" ).attr('data-id')
        if($("#clientaccept").hasClass('active')) {
            needaccept.push(25)
        }
        if($("#brokeraccept").hasClass('active')) {
            needaccept.push(26)
        }
        if($("#insaccept").hasClass('active')) {
            needaccept.push(27)
        }
        if($("#adjaccept").hasClass('active')) {
            needaccept.push(28)
        }
        if($("#clientnot").hasClass('active')) {
            neednotify.push(29)
        }
        if($("#brokernot").hasClass('active')) {
            neednotify.push(30)
        }
        if($("#insnot").hasClass('active')) {
            neednotify.push(31)
        }
        if($("#adjnot").hasClass('active')) {
            neednotify.push(32)
        }

        $(".inserted_co_id").each(function (index, el){
            // Для каждого элемента сохраняем значение в personsIdsArray,
            // если значение есть.
            var v  = $(el).val();
            if (v) {
                if ($(el).attr('data-type') == 'ins') {
                    inscompanies.push(v);
                    if ($(el).next().hasClass('active')) {
                        insleader = v
                    }
                } else {
                    adjusters.push(v);
                    if($(el).next().hasClass('active')) {
                        adjleader = v
                    }

                }
            }

        })

        $(".inserted_kur_co_id").each(function (index, el){
            // Для каждого элемента сохраняем значение в personsIdsArray,
            // если значение есть.
            var v  = $(el).val();
            if (v) {
                kurators.push(v);
                if($(el).next().hasClass('active')) {
                    kurleaders.push(v)
                }
                if($(el).next().hasClass('broker')) {
                    kuratorsbr.push(v)
                } else if($(el).next().hasClass('insco')) {
                    kuratorsins.push(v)
                } else if($(el).next().hasClass('client')) {
                    kuratorscl.push(v)
                } else if($(el).next().hasClass('adjuster')) {
                    kuratorsadj.push(v)
                }
            }
        })

        var mistake = ''


        if(kurleaders.length != (2 + inscompanies.length + adjusters.length)) {
            mistake += 'Указаны не все кураторы-лидеры.'
        }

        if(!$("#doc_date").val()) {
            mistake += 'Не указана дата убытка.'
        }

        if(insleader==0) {
            mistake += 'Не указана страховая компания-лидер.'
        }
        if(adjleader==0) {
            mistake += 'Не указан аджастер-лидер.'
        }
        if(inscompanies.length == 0) {
            mistake += 'Не выбрана страховая компания.'
        }
        if(inscompanies.length == 0) {
            mistake += 'Не выбрана страховая компания.'
        }
        if(adjusters.length == 0) {
            mistake += 'Не выбран аджастер.'
        }
        if(kuratorscl.length == 0) {
            mistake += 'Не выбраны кураторы от клиента.'
        }
        if(kuratorsbr.length == 0) {
            mistake += 'Не выбраны кураторы от страхового брокера.'
        }
        if(kuratorsins.length == 0) {
            mistake += 'Не выбраны кураторы от страховой компании.'
        }
        if(kuratorsadj.length == 0) {
            mistake += 'Не выбраны кураторы от аджастера.'
        }
        if(mistake) {
            $("#mistake3").text(mistake)
        } else {
            var form_data = new FormData();
            //console.log(files[0])
            form_data.append('lostid', $("#lostid").val())
            form_data.append('clientid', clientid)
            form_data.append('brokerid', brokerid)
            form_data.append('docdate',$("#doc_date").val())
            form_data.append('inscompanies', inscompanies)
            form_data.append('insleader', insleader)
            form_data.append('adjusters', adjusters)
            form_data.append('adjleader', adjleader)
            form_data.append('kurators', kurators)
            form_data.append('kurleaders', kurleaders)
            form_data.append('kuratorscl', kuratorscl)
            form_data.append('kuratorsins', kuratorsins)
            form_data.append('kuratorsbr', kuratorsbr)
            form_data.append('kuratorsadj', kuratorsadj)
            form_data.append('needaccept', needaccept)
            form_data.append('neednotify', neednotify)
            $.ajax({
                url: '/ajax/add_loss.php',
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                success: function(data){
                    console.log(data);
                    if(data.error) {
                        $("#mistake3").text(data.error)
                    } else {
                        location.reload();
                    }
                }
            })
        }

    })


})

function kuratoradd(cardblock, item, type) {
    var cardblockinc = $("<div></div>").attr("class", "company_card")
    var inpkur =  $("<input>").attr("type", "hidden").attr("class", "inserted_kur_co_id").val(item.value)
    var delblock = $("<span></span>").attr("class", "delete js_delete")
    var uls = $("<span></span>").attr("class", "company_card_list")
    var liname = $("<li></li>")
    liname.append($("<span></span>").text("ФИО"))
    liname.append($("<p></p>").text(item.label))
    uls.append(liname)
    var liposition = $("<li></li>")
    liposition.append($("<span></span>").text("Должность"))
    liposition.append($("<p></p>").text(item.position))
    uls.append(liposition)
    var liemail = $("<li></li>")
    liemail.append($("<span></span>").text("email"))
    liemail.append($("<p></p>").text(item.email))
    uls.append(liemail)
    var limphone = $("<li></li>")
    limphone.append($("<span></span>").text("Моб. телефон"))
    limphone.append($("<p></p>").text(item.mphone))
    uls.append(limphone)
    var liwphone = $("<li></li>")
    liwphone.append($("<span></span>").text("Раб. телефон"))
    liwphone.append($("<p></p>").text(item.wphone))
    uls.append(liwphone)
    var lileader = $("<li></li>")

    if(type == 1) {
        var lileaderlabel = $("<label></label>").attr("class", "leader broker js_checkbox").text("Назначен лидером")
    } else if (type == 2) {
        var lileaderlabel = $("<label></label>").attr("class", "leader insco js_checkbox").text("Назначен лидером")
    } else if (type == 3) {
        var lileaderlabel = $("<label></label>").attr("class", "leader adjuster js_checkbox").text("Назначен лидером")
    } else if (type == 4) {
        var lileaderlabel = $("<label></label>").attr("class", "leader client js_checkbox").text("Назначен лидером")
    }

    var lileaderinput = $("<input>").attr("type", "checkbox").attr("data-insc-leader", item.value)
    lileaderlabel.append(lileaderinput)
    lileader.append(inpkur)
    lileader.append(lileaderlabel)
    uls.append(lileader)
    //cardblockinc.append(inpcomp)
    cardblockinc.append(uls)
    cardblockinc.append(delblock)
    cardblock.append(cardblockinc)
}