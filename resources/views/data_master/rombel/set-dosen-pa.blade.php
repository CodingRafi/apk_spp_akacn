@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="text-capitalize mb-0">Set Dosen PA Rombel</h5>
                    @can('add_rombel')
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#dosenPa">
                            Set Dosen PA
                        </button>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Prodi</th>
                                    @can('add_rombel')
                                        <th>Aksi</th>
                                    @endcan
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="modal fade" id="dosenPa" tabindex="-1" aria-labelledby="dosenPaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('data-master.rombel.storeDosenPa') }}" method="get">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="dosenPaLabel">Set Dosen PA</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @method('post')
                        <div class="mb-3">
                            <label for="tahun_masuk_id" class="form-label">Tahun Masuk</label>
                            <select class="form-select" name="tahun_masuk_id">
                                <option value="">Pilih Tahun Masuk</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="dosen_pa" class="form-label">Dosen</label>
                            <select class="form-select" name="dosen_pa_id">
                                <option value="">Pilih Dosen</option>
                                @foreach ($dosen as $row)
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-start px-3">
                        <button type="button" class="btn btn-primary" onclick="submitForm(this.form, this)">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        getTahunAjaran()
        function getTahunAjaran(id_pivot = '') {
            $.ajax({
                url: "{{ route('data-master.rombel.getTahunAjaran') }}" + "?id_pivot=" + id_pivot,
                type: "GET",
                success: function(res) {
                    $('#tahun_masuk_id').empty().append('<option value="">Pilih Tahun Masuk</option>')
                    $.each(res.data, function(i, e) {
                        $('#tahun_masuk_id').append(
                            `<option value="${e.id}">${e.tahun}</option>`
                        )
                    })
                },
                error: function(err) {
                    console.log(err)
                }
            })
        }
    </script>
@endpush
