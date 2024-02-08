$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function submitForm(originalForm, selector = "", func) {
    let oldValue = $(selector).html();
    $(selector)
        .html(`<i class="ti-reload fa-spin mr-2"></i> Loading...`)
        .attr("disabled", true);

    $.post({
        url: $(originalForm).attr("action"),
        data: new FormData(originalForm),
        dataType: "json",
        contentType: false,
        cache: false,
        processData: false,
    })
        .done((response) => {
            console.log(response);
            $(selector).html(oldValue).attr("disabled", false);

            $(".modal").modal("hide");
            showAlert(response.message, "success");

            if (typeof table !== "undefined") {
                table.ajax.reload();
            } else {
                location.reload();
            }

            if (func != undefined) {
                func(response.data);
            }
        })
        .fail((errors) => {
            $(selector).html(oldValue).attr("disabled", false);

            if (errors.status == 422) {
                loopErrors(errors.responseJSON.errors);
                return;
            }

            showAlert(errors.responseJSON.message, "danger");
        });
}

function loopErrors(errors) {
    $(
        ".form-control, .custom-select, [type=radio], [type=checkbox], [type=file], .select2, .note-editor"
    ).removeClass("is-invalid");
    $(".invalid-feedback").remove();

    if (errors == undefined) {
        return;
    }

    for (error in errors) {
        $(`[name=${error}]`).addClass("is-invalid");

        if ($(`[name=${error}]`).hasClass("select2")) {
            $(
                `<span class="error invalid-feedback">${errors[error][0]}</span>`
            ).insertAfter($(`[name=${error}]`).next());
        } else if ($(`[name=${error}]`).hasClass("summernote")) {
            $(".note-editor").addClass("is-invalid");
            $(
                `<span class="error invalid-feedback">${errors[error][0]}</span>`
            ).insertAfter($(`[name=${error}]`).next());
        } else if ($(`[name=${error}]`).hasClass("custom-control-input")) {
            if ($(`[name="${error}[]"]`).length == 0) {
                $(
                    `<span class="error invalid-feedback">${errors[error][0]}</span>`
                ).insertAfter($(`[name=${error}]`).next());
            } else {
                $(`[name="${error}[]"]`).addClass("is-invalid");
                $(
                    `<span class="error invalid-feedback">${errors[error][0]}</span>`
                ).insertAfter($(`[name="${error}[]"]`).next());
            }
        } else {
            if ($(`[name=${error}]`).length == 0) {
                $(`[name="${error}[]"]`).addClass("is-invalid");
                $(
                    `<span class="error invalid-feedback">${errors[error][0]}</span>`
                ).insertAfter($(`[name="${error}[]"]`).next());
            } else {
                if (
                    $(`[name=${error}]`)
                        .next()
                        .hasClass("input-group-append") ||
                    $(`[name=${error}]`).next().hasClass("input-group-prepend")
                ) {
                    $(
                        `<span class="error invalid-feedback">${errors[error][0]}</span>`
                    ).insertAfter($(`[name=${error}]`).parent());
                    $(".input-group-append .input-group-text").css(
                        "border-radius",
                        "0 4px 4px 0"
                    );
                    $(".input-group-prepend")
                        .next()
                        .css("border-radius", "0 4px 4px 0");
                } else {
                    $(
                        `<span class="error invalid-feedback">${errors[error][0]}</span>`
                    ).insertAfter($(`[name=${error}]`));
                }
            }
        }
    }
}
