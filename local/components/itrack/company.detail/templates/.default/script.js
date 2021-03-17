$(document).ready(function() {
    console.log("script")

    BX.ajax.runAction('itrack:custom.api.signal.getCompanies', {
        data: {
            type: '2'
        }
    }).then(function (response) {
        console.log(response);
    }, function (error) {
        //сюда будут приходить все ответы, у которых status !== 'success'
        console.log(error);

    });
})