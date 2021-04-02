$(document).ready(function() {
    var main_lost =  $(".block_title").attr("data-lost")
    var curuser = $(".block_title").attr("data-user")
    var status = $(".block_title").attr("data-status")
    var lost_id = $("#lost_id").val()
    var inputFile = $('#loss_file');
    var newFiles = [];
    inputFile.change(function() {
        for(let index = 0; index < inputFile[0].files.length; index++) {
            var file = inputFile[0].files[index];
            newFiles.push(file);
        }
        newFiles.forEach(file => {
            var fileElement = $(`<p>${file.name}</p>`)
            $(".upload_btn_text").html(fileElement)
        });
    })

    $(".form_popup").submit(function (event) {
        event.preventDefault()
        var formData = new FormData()
        formData.append('lost_id', lost_id)
        formData.append('doc_name', $("#doc_name").val())
        formData.append('loss_file', $("#loss_file").prop('files')[0])
        formData.append('doc_date', $("#doc_date").val())
        formData.append('comment', $("#comment").val())
        $.ajax({
            url: '/ajax/add_loss_file.php',
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
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

    })

    $(".js_deletefile").click(function (event) {
        var fileid = $(this).parent().attr("data-id")
        BX.ajax.runAction('itrack:custom.api.signal.delLostfile', {
            data: {
                fileid: fileid
            }
        }).then(function (response) {
            //console.log(response)
            if(response.data=='success') {
                location.reload();
            } else {
                alert(response.data)
            }
        }, function (error) {
            //сюда будут приходить все ответы, у которых status !== 'success'
            //console.log(error)
            alert(error)
        });

    })

    $(".form_popup2").submit(function (event) {
        event.preventDefault()
        var lossdocid = $(this).attr("data-id")
        var newcomment = BX.findChild(event.target, {"class" : "textarea"}, true, true)
        var newcommenttxt = ''
        newcomment.forEach(function(element){
            newcommenttxt = $(element).val()
        })
        BX.ajax.runAction('itrack:custom.api.signal.updateLostfilecomment', {
            data: {
                fileid: lossdocid,
                newcomment: newcommenttxt
            }
        }).then(function (response) {
            //console.log(response)
            if(response.data=='success') {
                location.reload();
            } else {
                alert(response.data)
            }
        }, function (error) {
            //сюда будут приходить все ответы, у которых status !== 'success'
            //console.log(error)
            alert(error)
        });


    })
    // workflow - акцепт
    $("#accept").click(function (event) {
        BX.ajax.runAction('itrack:custom.api.signal.acceptLostdoc', {
            data: {
                lostid: main_lost,
                lostdocid: lost_id,
                status: status,
                user: curuser
            }
        }).then(function (response) {
            //console.log(response)
            if(response.data=='updated') {
                location.reload();
            } else {
                alert(response.data)
            }
        }, function (error) {
            //сюда будут приходить все ответы, у которых status !== 'success'
            console.log(error)
            //alert(error)
        });
    })
    // workflow - отклонение
    $(".form_popup3").submit(function (event) {
        event.preventDefault()
        var newcomment = BX.findChild(event.target, {"class" : "textarea"}, true, true)
        var newcommenttxt = ''
        newcomment.forEach(function(element){
            newcommenttxt = $(element).val()
        })
        //console.log(event)
        BX.ajax.runAction('itrack:custom.api.signal.declineLostdoc', {
            data: {
                lostid: main_lost,
                lostdocid: lost_id,
                status: status,
                user: curuser,
                comment: newcommenttxt
            }
        }).then(function (response) {
            //console.log(response)
            if(response.data=='updated') {
                location.reload();
            } else {
                alert(response.data)
            }
        }, function (error) {
            //сюда будут приходить все ответы, у которых status !== 'success'
            console.log(error)
            //alert(error)
        });
    })

    /*$("#decline").click(function (event) {
        BX.ajax.runAction('itrack:custom.api.signal.declineLostdoc', {
            data: {
                lostid: main_lost,
                lostdocid: lost_id,
                status: status,
                user: curuser
            }
        }).then(function (response) {
            //console.log(response)
            if(response.data=='updated') {
                location.reload();
            } else {
                alert(response.data)
            }
        }, function (error) {
            //сюда будут приходить все ответы, у которых status !== 'success'
            console.log(error)
            //alert(error)
        });
    })*/
})