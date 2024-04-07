$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

function addForm(url, title = "Tambah", modal = "#modal-form", func) {
    $(`${modal}`).modal("show");
    $(`${modal} .modal-title`).text(title);
    $(`${modal} form`).attr("action", url);
    $(`${modal} [name=_method]`).val("post");

    resetForm(`${modal} form`);
    initTinyMCE();

    if (func != undefined) {
        func();
    }
}

function submitForm(originalForm, selector = "", func) {
    let oldValue = $(selector).html();
    $(selector)
        .html(`<i class="ti-reload fa-spin mr-2"></i> Loading...`)
        .attr("disabled", true);

    let form = $(originalForm);
    let data = new FormData(originalForm);
    let form_textarea = form.find("textarea");

    form_textarea.each(function () {
        if ($(this).hasClass("textarea-tinymce")) {
            let editor = tinymce.get($(this).attr("id"));
            if (editor) {
                let content = editor.getContent();
                data.set($(this).attr("name"), content);
            }
            editor.remove();
        }
    });

    $.post({
        url: form.attr("action"),
        data: data,
        dataType: "json",
        contentType: false,
        cache: false,
        processData: false,
    })
        .done((response) => {
            $(selector).html(oldValue).attr("disabled", false);

            $(".modal").modal("hide");
            showAlert(response.message, "success");

            if (typeof table !== "undefined") {
                table.ajax.reload();
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

function editForm(url, title = "Edit", modal = "#modal-form", func) {
    $.get(url)
        .done((response) => {
            $(`${modal}`).modal("show");
            $(`${modal} .modal-title`).text(title);
            $(`${modal} form`).attr("action", url);
            $(`${modal} [name=_method]`).val("put");

            resetForm(`${modal} form`);
            loopForm(response.data, modal);
            initTinyMCE();

            if (func != undefined) {
                func(response.data);
            }
        })
        .fail((errors) => {
            alert("Tidak dapat menampilkan data");
            return;
        });
}

function resetForm(selector) {
    $(selector)[0].reset();

    $(".select2").trigger("change");

    clearErrorValidationBootstrap();
}

function clearErrorValidationBootstrap() {
    $(
        ".form-control, .custom-select, [type=radio], [type=checkbox], [type=file], .select2, .note-editor"
    ).removeClass("is-invalid");
    $(".invalid-feedback").remove();
}

function deleteData(url) {
    Swal.fire({
        title: "Apakah anda yakin ingin hapus data ini?",
        text: "Data yang terhapus tidak dapat dikembalikan",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya",
        cancelButtonText: "Tidak",
    }).then((result) => {
        if (result.isConfirmed) {
            $(".form-delete").attr("action", url).submit();
        }
    });
}

function deleteDataAjax(url, func) {
    Swal.fire({
        title: "Apakah anda yakin ingin hapus data ini?",
        text: "Data yang terhapus tidak dapat dikembalikan",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya",
        cancelButtonText: "Tidak",
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(url, {
                _method: "delete",
            })
                .done((response) => {
                    showAlert(response.message, "success");
                    if (typeof table !== "undefined") {
                        table.ajax.reload();
                    }

                    if (func != undefined) {
                        func(response.data);
                    }
                })
                .fail((errors) => {
                    showAlert("Tidak dapat menghapus data");
                    return;
                });
        }
    });
}

function loopForm(originalForm, modal) {
    for (field in originalForm) {
        if ($(`${modal} [name=${field}]`).attr("type") != "file") {
            if ($(`${modal} [name=${field}]`).hasClass("textarea-tinymce")) {
                tinymce.remove();
                initTinyMCE(`#${$(`${modal} [name=${field}]`).attr('id')}`, originalForm[field]);
            } else if ($(`${modal} [name=${field}]`).attr("type") == "radio") {
                // radio
                $(`${modal} [name=${field}]`)
                    .filter(`[value="${originalForm[field]}"]`)
                    .prop("checked", true);
            } else if ($(`${modal} [name=${field}]`).attr("type") == "checkbox") {
                // radio
                $(`${modal} [name=${field}]`)
                    .filter(`[value="${originalForm[field]}"]`)
                    .prop("checked", true);
            } else if($(`${modal} [name=${field}]`).attr("type") == 'time'){
                // Time
                $(`${modal} [name=${field}]`).val(originalForm[field].substring(0, 5));
            }
             else {
                if (
                    $(`${modal} [name=${field}]`).length == 0 &&
                    $(`[name="${field}[]"]`).attr("multiple")
                ) {
                    // select multiple
                    $(`[name="${field}[]"]`).val(originalForm[field]);
                } else {
                    $(`${modal} [name=${field}]`).val(originalForm[field]);
                }
            }

            if ($(`[name="${field}[]"]`).length > 1) {
                // checkbox multiple
                originalForm[field].forEach((el) => {
                    $(`[name="${field}[]"]`)
                        .filter(`[value="${el}"]`)
                        .prop("checked", true);
                });
            }

            $(".select2").trigger("change");
        } else {
            $(`.preview-${field}`).attr("src", originalForm[field]).show();
        }
    }
}
