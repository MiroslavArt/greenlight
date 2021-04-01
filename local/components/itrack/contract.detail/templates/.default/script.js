var addedins = 0
var addedadj = 0
var addedcompany = {}

$(document).ready(function() {

    // текущая дата по умолчанию
    var options = {
        year: 'numeric',
        month: 'numeric',
        day: 'numeric',
        timezone: 'UTC'
    };

    $("#doc_date").val(new Date().toLocaleString("ru", options))

    // подтянуть кураторов из договора
    var contractnum = $(".contract_number").attr("data-id")
    $("#kurtransfer").click(function(e){
        var form = BX.findParent(this, {"tag" : "form"})
        var kurids = BX.findChild(form, {"class" : "inserted_kur_co_id"}, true, true)
        BX.ajax.runAction('itrack:custom.api.signal.getContkurators', {
            data: {
                contract: contractnum
            }
        }).then(function (response) {
            console.log(response.data)
            response.data.forEach(function(kurator){
                var foundkur = false
                kurids.forEach(function(element){
                    if(element.getAttribute("value") == kurator.value) {
                        foundkur = true
                    }
                })
                if(foundkur==false) {
                    console.log(kurator)
                    if(kurator.type=='client') {
                        kuratoradd($( "#ins_kur_card" ), kurator, kurator.typeid, kurator.companyid, true)
                    } else if(kurator.type=='broker') {
                        kuratoradd($( "#brok_kur_card" ), kurator, kurator.typeid, kurator.companyid, true)
                    } else if(kurator.type=='insuer') {
                        $(".ins_kurators").each(function (index, el){
                            if($(el).attr("data-id") == kurator.companyid) {
                                kuratoradd($(el), kurator, kurator.typeid, kurator.companyid, true)
                            }
                        })
                    }
                }
            })
        }, function (error) {
            //сюда будут приходить все ответы, у которых status !== 'success'
            console.log(error);
        });
    });

    // клиент и его кураторы
    var clientid = $( "#kur_client_search_ins").attr('data-id')
    addedcompany[clientid] = 0
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
                    kuratoradd($( "#ins_kur_card" ), ui.item, 4, clientid)
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
    addedcompany[brokerid] = 0
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
                    kuratoradd($( "#brok_kur_card" ), ui.item, 1, brokerid)
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
        addedcompany[compid] = 0
        var cardblock = $("<div></div>").attr("class", "company_card_container ins_kurators").attr("data-id", compid)
        var kursearch = $("<div></div>").attr("class", "input_container without_small")
        var kursearchinp = $("<input>").attr("type", "text").attr("class", "text_input inserted_co_label kur_select")
            .attr("placeholder", 'Выберите куратора(-ов) от СК по вводу букв из ФИО')
        kursearch.append(kursearchinp)
        //$(el).parent().parent().after(cardblock)
        //$(el).parent().parent().after(kursearch)
        $(el).parent().parent().parent().append(kursearch)
        $(el).parent().parent().parent().append(cardblock)
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
                        kuratoradd(cardblock, ui.item, 2, ui.item.companyid)
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
                    var allblocks = $("<div></div>").attr("class", "ins_adjuster")
                    var coblock = $("<div></div>").attr("class", "gray_block delete_left")
                    var delblock = $("<span></span>").attr("class", "delete js_delete1")
                    var inplock = $("<div></div>").attr("class", "input_container with_flag")
                    var labelcomp =  $("<label></label>").attr("class", "big_label").text(ui.item.label)
                    var inpcomp =  $("<input>").attr("type", "hidden").attr("class", "inserted_adj_id").val(ui.item.value)
                    if(addedadj==0) {
                        var labelleader = $("<label></label>").attr("class", "flag js_checkbox active")
                    } else {
                        var labelleader = $("<label></label>").attr("class", "flag js_checkbox")
                    }
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
                    coblock.append(delblock)
                    coblock.append(inplock)
                    allblocks.append(coblock)
                    allblocks.append(kursearch)
                    allblocks.append(cardblock)
                    $("#ins_adjusters").append(allblocks)
                    //$("#ins_adjusters").append(coblock)
                    //$("#ins_adjusters").append(kursearch)
                    //$("#ins_adjusters").append(cardblock)
                    addedadj++
                    addedcompany[ui.item.value] = 0
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
                                    kuratoradd(cardblock, ui.item, 3, ui.item.companyid)
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

    // добавление файлов
    var inputFile = $('.loss_file')
    var filesContainer= $('.docs_list')
    var files = []

    // обработчики добавления файлов
    inputFile.change(function() {
        let newFiles = [];
        for(let index = 0; index < inputFile[0].files.length; index++) {
            let file = inputFile[0].files[index];
            newFiles.push(file);
            files.push(file);
        }

        newFiles.forEach(file => {
            var point =  $("<li></li>")
            var fileElement = $(`<p>${file.name}</p>`).attr("class", "link")
            var delElement = $('<span></span>').attr("class", "delete")

            fileElement.data('fileData', file);
            point.append(fileElement)
            point.append(delElement)
            filesContainer.append(point)

            delElement.click(function(event) {
                let fileElement = $(event.target).prev();
                let indexToRemove = files.indexOf(fileElement.data('fileData'));
                fileElement.parent().remove();
                files.splice(indexToRemove, 1);
            });
        });
    });

    // функция при отправке формы
    $( ".form_popup" ).submit(function( event ) { // задаем функцию при срабатывании события "submit" на элементе <form>
        event.preventDefault(); // действие события по умолчанию не будет срабатывать
        var inscompanies = []
        var insleader = 0
        var adjusters = []
        var adjleader = 0
        var kurators = []
        var kurleaders = []
        var kuratorscl = 0
        var kuratorsbr = 0
        var kuratorsins = 0
        var kuratorsadj = 0

        $(".inserted_co_id").each(function (index, el){
            // Для каждого элемента сохраняем значение в personsIdsArray,
            // если значение есть.
            var v  = $(el).val();
            if (v) inscompanies.push(v);
            if($(el).next().hasClass('active')) {
                insleader = v
            }
        })

        $(".inserted_adj_id").each(function (index, el){
            // Для каждого элемента сохраняем значение в personsIdsArray,
            // если значение есть.
            var v  = $(el).val();
            if (v) adjusters.push(v);
            if($(el).next().hasClass('active')) {
                adjleader = v
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
                    kuratorsbr++
                } else if($(el).next().hasClass('insco')) {
                    kuratorsins++
                } else if($(el).next().hasClass('client')) {
                    kuratorscl++
                } else if($(el).next().hasClass('adjuster')) {
                    kuratorsadj++
                }
            }
        })

        var mistake = ''

        if(inscompanies.length == 0) {
            mistake += 'Не выбрана страховая компания.'
        }
        if(adjusters.length == 0) {
            mistake += 'Не выбран аджастер.'
        }
        if(kuratorscl==0) {
            mistake += 'Не выбраны кураторы от клиента.'
        }
        if(kuratorsbr==0) {
            mistake += 'Не выбраны кураторы от страхового брокера.'
        }
        if(kuratorsins==0) {
            mistake += 'Не выбраны кураторы от страховой компании.'
        }
        if(kuratorsadj==0) {
            mistake += 'Не выбраны кураторы от аджастера.'
        }
        if(mistake) {
            $("#mistake").text(mistake)
        } else {
            var form_data = new FormData();
            //console.log(files[0])
            form_data.append('contract', contractnum)
            form_data.append('clientid', clientid)
            form_data.append('brokerid', brokerid)
            form_data.append('docnum',$("#doc_num").val())
            form_data.append('docdate',$("#doc_date").val())
            form_data.append('description',$("#loss_descr").val())
            form_data.append('reqdoc',$("#req_doc").val())
            form_data.append('reqdate',$("#req_date").val())
            form_data.append('user',$("#users").val())
            form_data.append('req_term',$("#req_term").val())
            form_data.append('status','red')
            form_data.append('inscompanies', inscompanies)
            form_data.append('insleader', insleader)
            form_data.append('adjusters', adjusters)
            form_data.append('adjleader', adjleader)
            form_data.append('kurators', kurators)
            form_data.append('kurleaders', kurleaders)
            $.each(files,function(index,value){
                //console.log(value)
                form_data.append('file'+index, value);
            });
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
                        $("#mistake").text(data.error)
                    } else {
                        location.reload();
                    }
                }
            })
        }
    })
})

function kuratoradd(cardblock, item, type, company, transfer = false) {
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
    if(transfer) {
        if(type == 1) {
            if(item.isleader==true) {
                //if(addedcompany[company]==0) {
                var lileaderlabel = $("<label></label>").attr("class", "leader broker js_checkbox active").text("Назначен лидером")
            } else {
                var lileaderlabel = $("<label></label>").attr("class", "leader broker js_checkbox").text("Назначен лидером")
            }
        } else if (type == 2) {
            if(item.isleader==true) {
                //if(addedcompany[company]==0) {
                var lileaderlabel = $("<label></label>").attr("class", "leader insco js_checkbox active").text("Назначен лидером")
            } else {
                var lileaderlabel = $("<label></label>").attr("class", "leader insco js_checkbox").text("Назначен лидером")
            }
        } else if (type == 3) {
            if(addedcompany[company]==0) {
                var lileaderlabel = $("<label></label>").attr("class", "leader adjuster js_checkbox active").text("Назначен лидером")
            } else {
                var lileaderlabel = $("<label></label>").attr("class", "leader adjuster js_checkbox").text("Назначен лидером")
            }
        } else if (type == 4) {
            if(item.isleader==true) {
                //if(addedcompany[company]==0) {
                var lileaderlabel = $("<label></label>").attr("class", "leader client js_checkbox active").text("Назначен лидером")
            } else {
                var lileaderlabel = $("<label></label>").attr("class", "leader client js_checkbox").text("Назначен лидером")
            }
        }
    } else {
        if(type == 1) {
            if(addedcompany[company]==0) {
                //if(addedcompany[company]==0) {
                var lileaderlabel = $("<label></label>").attr("class", "leader broker js_checkbox active").text("Назначен лидером")
            } else {
                var lileaderlabel = $("<label></label>").attr("class", "leader broker js_checkbox").text("Назначен лидером")
            }
        } else if (type == 2) {
            if(addedcompany[company]==0) {
                //if(addedcompany[company]==0) {
                var lileaderlabel = $("<label></label>").attr("class", "leader insco js_checkbox active").text("Назначен лидером")
            } else {
                var lileaderlabel = $("<label></label>").attr("class", "leader insco js_checkbox").text("Назначен лидером")
            }
        } else if (type == 3) {
            if(addedcompany[company]==0) {
                var lileaderlabel = $("<label></label>").attr("class", "leader adjuster js_checkbox active").text("Назначен лидером")
            } else {
                var lileaderlabel = $("<label></label>").attr("class", "leader adjuster js_checkbox").text("Назначен лидером")
            }
        } else if (type == 4) {
            if(addedcompany[company]==0) {
                //if(addedcompany[company]==0) {
                var lileaderlabel = $("<label></label>").attr("class", "leader client js_checkbox active").text("Назначен лидером")
            } else {
                var lileaderlabel = $("<label></label>").attr("class", "leader client js_checkbox").text("Назначен лидером")
            }
        }
    }

    addedcompany[company]++



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