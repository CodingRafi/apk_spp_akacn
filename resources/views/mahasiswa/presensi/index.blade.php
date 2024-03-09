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
                    <div class="row">
                        <div class="col-md-4">
                            <select name="tahun_semester_id" id="tahun_semester_id" class="form-control mb-3">
                                <option value="">Pilih Semester</option>
                                @foreach ($tahun_semester as $semester)
                                    <option value="{{ $semester->id }}">{{ $semester->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead>
                                <tr>
                                    <th>Matkul</th>
                                    <td>1</td>
                                    <td>2</td>
                                    <td>3</td>
                                    <td>4</td>
                                    <td>5</td>
                                    <td>6</td>
                                    <td>7</td>
                                    <td>UTS</td>
                                    <td>8</td>
                                    <td>9</td>
                                    <td>10</td>
                                    <td>11</td>
                                    <td>12</td>
                                    <td>13</td>
                                    <td>14</td>
                                    <td>UAS</td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
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
        function generate_table(data) {
            let table = '';

            $.each(data, (i, e) => {
                table +=
                    `<tr>
                    <td>${e.matkul}</td>`;

                e.presensi.forEach(el => {
                    table += `<td>${el.jadwal_id ? (el.status ?? '-') : ''}</td>`;
                })

                table += `</tr>`;
            })

            $('.table tbody').html(table);
        }

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

        function get_presensi() {
            $('.table tbody').empty();
            $('.table tbody').append(`<tr>
                                        <td colspan="17" class="text-center py-4">
                                            <div class="spinner-border" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </td>
                                    </tr>`);
            $.ajax({
                url: "{{ route('presensi.data') }}",
                dataType: "json",
                data: {
                    tahun_semester_id: $('#tahun_semester_id').val()
                },
                success: function(res) {
                    generate_table(res.data)
                },
                error: function(err) {
                    console.log('Gagal get presensi');
                }
            })
        }

        $('#tahun_semester_id').on('change', get_presensi)
    </script>
@endpush
