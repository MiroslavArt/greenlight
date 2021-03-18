$(document).ready(function() {
    console.log("script")

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
                $( "#sel_ins" ).val( ui.item.value );
                return false;
            }
        })
    }, function (error) {
        //сюда будут приходить все ответы, у которых status !== 'success'
        console.log(error);

    });
})