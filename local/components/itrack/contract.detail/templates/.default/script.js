$(document).ready(function() {

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
                    kuratoradd($( "#ins_kur_card" ), ui.item)
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
                    kuratoradd($( "#brok_kur_card" ), ui.item)
                }
                return false;
            }
        });
    }, function (error) {
        //сюда будут приходить все ответы, у которых status !== 'success'
        console.log(error);
    });

    // клиент и его кураторы
    $(".inserted_co_id").each(function (index, el){
        var compid = $(el).val()
        var cardblock = $("<div></div>").attr("class", "company_card_container")
        var kursearch = $("<div></div>").attr("class", "input_container without_small")
        var kursearchinp = $("<input>").attr("type", "text").attr("class", "text_input inserted_co_label kur_select")
            .attr("placeholder", 'Выберите куратора(-ов) от СК по вводу букв из ФИО')
        kursearch.append(kursearchinp)
        $(el).parent().parent().after(cardblock)
        $(el).parent().parent().after(kursearch)
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
                        kursearchinp .val(ui.item.label);
                        kuratoradd(cardblock, ui.item)
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
                    //var allblocks = $("<div></div>").attr("class", "gray_blocks")
                    var coblock = $("<div></div>").attr("class", "gray_block")
                    var inplock = $("<div></div>").attr("class", "input_container with_flag")
                    var labelcomp =  $("<label></label>").attr("class", "big_label").text(ui.item.label)
                    var inpcomp =  $("<input>").attr("type", "hidden").attr("class", "inserted_adj_id").val(ui.item.value)
                    var labelleader = $("<label></label>").attr("class", "flag js_checkbox")
                    var leaderbox =  $("<input>").attr("type", "checkbox").attr("data-insc-leader", ui.item.value)
                    labelleader.append(leaderbox)
                    var kursearch = $("<div></div>").attr("class", "input_container without_small")
                    var kursearchinp = $("<input>").attr("type", "text").attr("class", "text_input inserted_co_label kur_select")
                        .attr("placeholder", 'Выберите куратора(-ов) от аджастера по вводу букв из ФИО')
                    var cardblock = $("<div></div>").attr("class", "company_card_container")
                    kursearch.append(kursearchinp)
                    inplock.append(labelcomp)
                    inplock.append(inpcomp)
                    inplock.append(labelleader)
                    coblock.append(inplock)
                    $("#ins_adjusters").append(coblock)
                    $("#ins_adjusters").append(kursearch)
                    $("#ins_adjusters").append(cardblock)

                    //$(".ins_comp").after(allblocks)
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
                                    kuratoradd(cardblock, ui.item)
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
})

function kuratoradd(cardblock, item) {
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
    limphone.append($("<span></span>").text("Моб телефон"))
    limphone.append($("<p></p>").text(item.mphone))
    uls.append(limphone)
    var liwphone = $("<li></li>")
    liwphone.append($("<span></span>").text("Раб телефон"))
    liwphone.append($("<p></p>").text(item.wphone))
    uls.append(liwphone)
    var lileader = $("<li></li>")
    var lileaderlabel = $("<label></label>").attr("class", "leader js_checkbox").text("Назначен лидером")
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