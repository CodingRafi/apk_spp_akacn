@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="text-capitalize">Presensi</h5>
                    <button type="button" class="btn btn-primary"
                        onclick="addForm('{{ route('presensi.store') }}', 'Presensi', '#presensi')">
                        Tambah
                    </button>
                </div>
                <div class="card-body">
                    @include('mahasiswa.presensi.table')
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="presensi" tabindex="-1" aria-labelledby="presensiLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="get">
                    @method('post')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="presensiLabel">Presensi</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="kode" class="form-label">Kode</label>
                            <input class="form-control" type="text" id="kode" name="kode" />
                        </div>
                        <button type="button" class="btn btn-primary w-100"
                            onclick="submitPresensi(this.form, this)">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function submitPresensi(originalForm, selector) {
            let oldValue = $(selector).html();
            $(selector)
                .html(`<i class="ti-reload fa-spin mr-2"></i> Loading...`)
                .attr("disabled", true);

            let form = $(originalForm);
            let data = new FormData(originalForm);

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
                    Swal.fire({
                        title: "SUCCESS",
                        text: "Presensi anda berhasil tersimpan.",
                        icon: "success",
                        showCancelButton: false,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "OK"
                    }).then((result) => {
                        get_presensi();
                    });
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
    </script>
@endpush
