function generateRandomCode(length) {
    const characters =
        "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    let result = "";

    for (let i = 0; i < length; i++) {
        const randomIndex = Math.floor(Math.random() * characters.length);
        result += characters.charAt(randomIndex);
    }

    return result;
}

function getNeoFeeder(url, tableData) {
    $.LoadingOverlay("show");
    $.ajax({
        url: url,
        success: function(res) {
            showAlert(res.output, 'success')
            $.LoadingOverlay("hide");
            tableData.ajax.reload();
        },
        error: function(err) {
            $.LoadingOverlay("hide");
            showAlert(err.responseJSON.output, 'error')
        }
    })
}
