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
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead>
                                <tr>
                                    <th>Matkul</th>
                                    @for ($i = 1; $i <= config('services.max_pertemuan'); $i++)
                                        <th>{{ $i }}</th>
                                    @endfor
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
                    </div>
                    <div class="modal-footer justify-content-start px-3">
                        <button type="button" class="btn btn-primary" onclick="submitForm(this.form, this)">Submit</button>
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

            for (const key in data) {
                let tableMatkul = '<tr>';
                tableMatkul += `<td>${data[key].matkul}</td>`;

                data[key].presensi.forEach(e => {
                    tableMatkul += `<td>${(e.id ? (e.status ?? '-') : '') ?? ''}</td>`;
                })

                tableMatkul += '</tr>';
                table += tableMatkul;
            }

            $('.table tbody').html(table);
        }

        function get_presensi() {
            $('.table tbody').empty();
            $('.table tbody').append(`<tr>
                                        <td colspan="15" class="text-center py-4">
                                            <div class="spinner-border" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </td>
                                    </tr>`);
            $.ajax({
                url: "{{ route('presensi.data') }}",
                dataType: "json",
                success: function(res) {
                    generate_table(res.data)
                }
            })
        }

        get_presensi();

        $(document).ready(function() {

        })
    </script>
@endpush
