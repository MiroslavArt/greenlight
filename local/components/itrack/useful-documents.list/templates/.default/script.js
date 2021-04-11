$(function(){

    $('body').on('click', '.js-deactive-documents', function (e) {
        e.preventDefault();

        var selected = [];
        $("[name='doc_id[]']:checked").each(function(i) {
            selected.push($(this).val());
        });

        if(selected.length > 0) {
            mySubmit({
                'url': window.location.href,
                'data': encodeURI('action=unlink&doc_id[]=' + selected.join('&doc_id[]='))
            });
        }
    })
});