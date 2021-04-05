$(document).ready(function() {
    var inputFile = $('.req_file');
    var newFiles = [];
    var options = {
        year: 'numeric',
        month: 'numeric',
        day: 'numeric',
        timezone: 'UTC'
    };

    var curdate = new Date().toLocaleString("ru", options)


    inputFile.change(function(e) {
       // for(let index = 0; index < e.target.files.length; index++) {
            var file = $(e.target).prop('files')[0]
            newFiles.push(file);
        //}
        console.log(newFiles)

        var btntxt = BX.findChild(e.target.parentElement, {"class" : "upload_btn_text"}, true, false)

        newFiles.forEach(file => {
            var fileElement = $(`<p>${file.name}</p>`)
            $(btntxt).html(fileElement)
        });
    })

    $(".upload_doc_container").submit(function (event) {
        event.preventDefault()
        var lost_id = $(event.target).attr('data-id')
        var newcomment = BX.findChild(event.target, {"class" : "text_input"}, true, true)
        var newcommenttxt = ''
        newcomment.forEach(function(element){
            newcommenttxt = $(element).val()
        })
        var formData = new FormData()
        console.log(newFiles[0])
        formData.append('lost_id', lost_id)
        formData.append('doc_name', newcommenttxt)
        formData.append('loss_file', newFiles[0])
        formData.append('doc_date', curdate)
        formData.append('comment', newcommenttxt)
        formData.append('type', '1')
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
                    var mistake = BX.findChild(event.target, {"class" : "mistakereq"}, true, true)
                    mistake.forEach(function(element){
                        $(element).text(data.error)
                    })
                } else {
                    //    location.reload();
                    var liblock = $("<li></li>")
                    var lidivOne = $("<div></div>").attr('class', 'doc_format')
                    var lidescOne = $("<div></div>").attr('class', 'doc_list_title').text('Формат документа')
                    var lispanOne = $("<span></span>").attr('class', 'name_format').text(data.success.type)
                    lidivOne.append(lidescOne)
                    lidivOne.append(lispanOne)
                    liblock.append(lidivOne)
                    var lidivTwo = $("<div></div>").attr('class', 'doc_desc')
                    var lidescTwo = $("<div></div>").attr('class', 'doc_list_title').text('Краткое описание')
                    var lihref = $('<a>').attr('href', data.success.tmp_name).attr('download',data.success.name).text(newcommenttxt)
                    lidivTwo.append(lidescTwo)
                    lidivTwo.append(lihref)
                    liblock.append(lidivTwo)
                    var delspan = $("<span></span>").attr('class', 'delete js_deletereqdoc').attr('data-id', data.success.docid)
                    liblock.append(delspan)
                    console.log(liblock)
                    var addfile = BX.findChild(event.target.parentElement, {"class" : "doc_list"}, true, false)
                    console.log(addfile)
                    $(addfile).append(liblock)
                }
            }
        })
    })
});