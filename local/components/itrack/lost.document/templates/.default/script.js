$(document).ready(function() {
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
        formData.append('lost_id', $("#lost_id").val())
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
})