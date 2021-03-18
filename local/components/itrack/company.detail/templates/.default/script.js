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
                    var coblock = $("<div></div>").attr("class", "gray_block");
                    var inplock = $("<div></div>").attr("class", "input_container");
                    var labelcomp =  $("<label></label>").attr("class", "big_label").text(ui.item.label);
                    var inpcomp =  $("<input>").attr("type", "hidden").attr("class", "inserted_co_id").val(ui.item.value);
                    inplock.append(inpcomp)
                    inplock.append(labelcomp)
                    coblock.append(inplock)
                    $(".ins_comp").after(coblock)
                }
                return false;
            }
        })
    }, function (error) {
        //сюда будут приходить все ответы, у которых status !== 'success'
        console.log(error);

    });

})