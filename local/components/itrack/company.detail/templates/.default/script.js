$(document).ready(function() {

    var inscompanies = []

    BX.ajax.runAction('itrack:custom.api.signal.getCompanies', {
        data: {
            type: '2'
        }
    }).then(function (response) {
        inscompanies = response.data
        $( "#search_ins" ).autocomplete({
            source: inscompanies,
            focus: function( event, ui ) {
                $( "#search_ins" ).val( ui.item.label );
                return false;
            },
            select: function( event, ui ) {
                $( "#search_ins" ).val( ui.item.label );
                //$( "#sel_ins" ).val( ui.item.value );
                console.log(this)
                var form = BX.findParent(this, {"tag" : "form"});
                console.log(form);

                var insids = BX.findChild(form, {"class" : "inserted_co_id"}, true, true);
                var foundcomp = false
                insids.forEach(function(element){
                    if(element.getAttribute("value") == ui.item.value) {
                        foundcomp = true
                    }
                });
                if(foundcomp==false) {
                    var coblock = $("<div></div>").attr("class", "gray_block")
                    var inplock = $("<div></div>").attr("class", "input_container")
                    var labelcomp =  $("<label></label>").attr("class", "big_label").text(ui.item.label)
                    var inpcomp =  $("<input>").attr("type", "hidden").attr("class", "inserted_co_id").val(ui.item.value)
                    //var labelleader = $("<label></label>").addClass("flag js_checkbox")
                    //var leaderbox =  $("<input>").attr("type", "checkbox").attr("data-insc-leader", ui.item.value);
                    //labelleader.append(leaderbox)
                    var kursearch = $("<div></div>").attr("class", "input_container without_small")
                    var kursearchinp = $("<input>").attr("type", "text").attr("class", "text_input inserted_co_label kur_select")
                        .attr("placeholder", 'Выберите куратора по вводу букв из фамилии')
                    kursearch.append(kursearchinp)
                    inplock.append(inpcomp)
                    inplock.append(labelcomp)
                    //inplock.append(labelleader)
                    coblock.append(inplock)
                    //coblock.append(labelleader)
                    //$(".ins_comp").append(labelleader)


                    $(".ins_comp").after(kursearch)
                    $(".ins_comp").after(coblock)

                    BX.ajax.runAction('itrack:custom.api.signal.getUsers', {
                        data: {
                            company: ui.item.value
                        }
                    }).then(function (response) {
                        console.log(response);
                        kursearchinp.autocomplete({
                            source: response.data,
                            focus: function( event, ui ) {
                                return false;
                            },
                            select: function( event, ui ) {
                                kursearchinp.val(ui.item.label);
                                var cardblock = $("<div></div>").attr("class", "company_card_container")
                                var cardblockinc = $("<div></div>").attr("class", "company_card")
                                var delblock = $("<span></span>").attr("class", "delete")
                                var uls = $("<span></span>").attr("class", "company_card_list")
                                var liname = $("<li></li>")
                                liname.append($("<span></span>").text("ФИО"))
                                liname.append($("<p></p>").text(ui.item.label))
                                uls.append(liname)
                                var liposition = $("<li></li>")
                                liposition.append($("<span></span>").text("Должность"))
                                liposition.append($("<p></p>").text(ui.item.position))
                                uls.append(liposition)
                                var liemail = $("<li></li>")
                                liemail.append($("<span></span>").text("email"))
                                liemail.append($("<p></p>").text(ui.item.email))
                                uls.append(liemail)
                                var limphone = $("<li></li>")
                                limphone.append($("<span></span>").text("Моб телефон"))
                                limphone.append($("<p></p>").text(ui.item.mphone))
                                uls.append(limphone)
                                var liwphone = $("<li></li>")
                                liwphone.append($("<span></span>").text("Раб телефон"))
                                liwphone.append($("<p></p>").text(ui.item.wphone))
                                uls.append(liwphone)
                                var lileader = $("<li></li>")
                                var lileaderlabel = $("<label></label>").attr("class", "leader js_checkbox active").text("Назначен лидером")
                                var lileaderinput = $("<input>").attr("type", "checkbox").attr("data-insc-leader", ui.item.value)
                                lileaderlabel.append(lileaderinput)
                                lileader.append(lileaderlabel)
                                uls.append(lileader)
                                cardblockinc.append(uls)
                                cardblockinc.append(delblock)
                                cardblock.append(cardblockinc)
                                kursearch.after(cardblock)
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