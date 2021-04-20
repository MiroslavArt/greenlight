var addedins = 0
var addedcompany = {}

$(document).ready(function() {

    // дата договора
    var options = {
        year: 'numeric',
        month: 'numeric',
        day: 'numeric',
        timezone: 'UTC'
    };

    $("#docdate").val(new Date().toLocaleString("ru", options))

    // добавление файлов
    var inputFile1 = $('.cont_file1');
    var inputFile2 = $('.cont_file2');
    var inputFile3 = $('.cont_file3');
    var filesContainer= $('.docs_list');
    var files = [];

    // обработчики добавления файлов
    inputFile1.change(function() {
        let newFiles = [];
        for(let index = 0; index < inputFile1[0].files.length; index++) {
            let file = inputFile1[0].files[index];
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

    inputFile2.change(function() {
        let newFiles = [];
        for(let index = 0; index < inputFile2[0].files.length; index++) {
            let file = inputFile2[0].files[index];
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

    inputFile3.change(function() {
        let newFiles = [];
        for(let index = 0; index < inputFile3[0].files.length; index++) {
            let file = inputFile3[0].files[index];
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
                let indexToRemove = files.indexOf(fileElement.data('fileData'))
                fileElement.parent().remove();
                files.splice(indexToRemove, 1);
            });
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
                    kuratoradd($( "#brok_kur_card" ), ui.item, 1, brokerid)
                }
                return false;
            }
        });
    }, function (error) {
        //сюда будут приходить все ответы, у которых status !== 'success'
        console.log(error);
    });

    // страховая компания и ее кураторы
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
                $(this).parent().parent().toggleClass('hidden');
                $( "#search_ins" ).val( ui.item.label );
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
                    var allblocks = $("<div></div>").attr("class", "ins_insuer")
                    var coblock = $("<div></div>").attr("class", "gray_block")
                    var delblock = $("<span></span>").attr("class", "delete js_delete1")
                    var inplock = $("<div></div>").attr("class", "input_container with_flag")
                    var labelcomp =  $("<label></label>").attr("class", "big_label").text(ui.item.label)
                    var inpcomp =  $("<input>").attr("type", "hidden").attr("class", "inserted_co_id").val(ui.item.value)
                    if(addedins==0) {
                        var labelleader = $("<label></label>").attr("class", "flag js_checkbox active")
                    } else {
                        var labelleader = $("<label></label>").attr("class", "flag js_checkbox")
                    }
                    var leaderbox =  $("<input>").attr("type", "checkbox").attr("data-insc-leader", ui.item.value)
                    labelleader.append(leaderbox)
                    var addkur = $("<a>").attr("href", '#').attr("class","link ico_add js_add")
                    var addkurtext = $("<span></span>").text("Добавить куратора")
                    addkur.append(addkurtext)
                    var inplockdiv = $("<div></div>").attr("class", "form_row brok_comp hidden")
                    var kursearch = $("<div></div>").attr("class", "input_container without_small")
                    var kursearchinp = $("<input>").attr("type", "text").attr("class", "text_input inserted_co_label kur_select")
                        .attr("placeholder", 'Выберите куратора(-ов) от страховой компании по вводу букв из ФИО')
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
                    $("#ins_insuers").append(allblocks)
                    addedins++
                    addedcompany[ui.item.value] = 0
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
                                var kurids = BX.findChild(form, {"class" : "inserted_kur_co_id"}, true, true)
                                var foundkur = false
                                kurids.forEach(function(element){
                                    if(element.getAttribute("value") == ui.item.value) {
                                        foundkur = true
                                    }
                                })
                                if(foundkur==false) {
                                    kursearchinp.val(ui.item.label);
                                    kuratoradd(cardblock, ui.item, 2, ui.item.companyid)
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

    // функция при отправке формы
    $( ".form_popup" ).submit(function( event ){ // задаем функцию при срабатывании события "submit" на элементе <form>
        event.preventDefault(); // действие события по умолчанию не будет срабатывать
        var docnum = $("#docnum").val()
        var docdate = $("#docdate").val()
        //var instype = $("#instype").val()
        var instype = $('#instype option:selected').text()
        var original = 0
        var needaccept = []
        var neednotify = []
        if($("#provideoriginal").hasClass('active')) {
            original = 5
        }
        if($("#clientaccept").hasClass('active')) {
            needaccept.push(17)
        }
        if($("#brokeraccept").hasClass('active')) {
            needaccept.push(18)
        }
        if($("#insaccept").hasClass('active')) {
            needaccept.push(19)
        }
        if($("#adjaccept").hasClass('active')) {
            needaccept.push(20)
        }
        if($("#clientnot").hasClass('active')) {
            neednotify.push(21)
        }
        if($("#brokernot").hasClass('active')) {
            neednotify.push(22)
        }
        if($("#insnot").hasClass('active')) {
            neednotify.push(23)
        }
        if($("#adjnot").hasClass('active')) {
            neednotify.push(24)
        }

        var inscompanies = []
        var insleader = 0
        var kurators = []
        var kurleaders = []
        var kuratorscl = []
        var kuratorsbr = []
        var kuratorsins = []

        $(".inserted_co_id").each(function (index, el){
            // Для каждого элемента сохраняем значение в personsIdsArray,
            // если значение есть.
            var v  = $(el).val();
            if (v) inscompanies.push(v);
            if($(el).next().hasClass('active')) {
                insleader = v
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
                }
            }
        })

        var mistake = ''

        if(kurleaders.length != (2 + inscompanies.length)) {
            mistake += 'Указаны не все кураторы-лидеры.'
        }

        if(insleader==0) {
            mistake += 'Не указана страховая компания - лидер.'
        }
        if(inscompanies.length == 0) {
            mistake += 'Не выбрана страховая компания.'
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

        if(mistake) {
            $("#mistake").text(mistake)
        } else {
            var form_data = new FormData();
            //console.log(files[0])
            form_data.append('docnum', docnum)
            form_data.append('docdate', docdate)
            form_data.append('instype', instype)
            form_data.append('clientid', clientid)
            form_data.append('brokerid', brokerid)
            form_data.append('original', original)
            form_data.append('inscompanies', inscompanies)
            form_data.append('insleader', insleader)
            form_data.append('kurators', kurators)
            form_data.append('kurleaders', kurleaders)
            form_data.append('kuratorscl', kuratorscl)
            form_data.append('kuratorsins', kuratorsins)
            form_data.append('kuratorsbr', kuratorsbr)
            form_data.append('needaccept', needaccept)
            form_data.append('neednotify', neednotify)

            $.each(files,function(index,value){
                //console.log(value)
                form_data.append('file'+index, value);
            });
            $.ajax({
                url: '/ajax/add_contract.php',
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
    });
})

function kuratoradd(cardblock, item, type, company) {
    //console.log(company)
    //console.log(addedcompany)
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
        if(addedcompany[company]==0) {
            var lileaderlabel = $("<label></label>").attr("class", "leader broker js_checkbox active").text("Назначен лидером")
        } else {
            var lileaderlabel = $("<label></label>").attr("class", "leader broker js_checkbox").text("Назначен лидером")
        }
    } else if (type == 2) {
        if(addedcompany[company]==0) {
            var lileaderlabel = $("<label></label>").attr("class", "leader insco js_checkbox active").text("Назначен лидером")
        } else {
            var lileaderlabel = $("<label></label>").attr("class", "leader insco js_checkbox").text("Назначен лидером")
        }
    } else {
        if(addedcompany[company]==0) {
            var lileaderlabel = $("<label></label>").attr("class", "leader client js_checkbox active").text("Назначен лидером")
        } else {
            var lileaderlabel = $("<label></label>").attr("class", "leader client js_checkbox").text("Назначен лидером")
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