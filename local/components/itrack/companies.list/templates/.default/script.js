$(document).ready(function() {
    $("#company_logo_input").change(function () {
        readURL(this);
    });
    $(".form_popup").submit(function (event) {
        event.preventDefault()
        var formData = new FormData()
        formData.append('type', $("#company_type").val())
        formData.append('name', $("#company_name").val())
        formData.append('full_name', $("#company_full_name").val())
        formData.append('adress', $("#company_adress").val())
        formData.append('legal_adress', $("#company_legal_adress").val())
        formData.append('inn', $("#company_inn").val())
        formData.append('kpp', $("#company_kpp").val())
        formData.append('logo', $("#company_logo_input").prop('files')[0])
        $.ajax({
            url: '/ajax/add_company.php',
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

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#company_logo_view').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}