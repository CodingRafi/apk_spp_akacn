@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <a
                            href="{{ route('data-master.tahun-ajaran.matkul.index', ['id' => request('id')]) }}"><i
                                class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                        <h5 class="mb-0">Mahasiswa</h5>
                    </div>
                    <button type="button" class="btn btn-primary"
                        onclick="addForm('{{ route('data-master.tahun-ajaran.matkul.mhs.store', ['id' => request('id'), 'matkul_id' => request('matkul_id')]) }}', 'Tambah Mahasiswa', '#setMhs')">
                        Tambah
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-matkul">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="setMhs" tabindex="-1" role="dialog" aria-labelledby="setMhsLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form action="" id="form-set-mhs">
                    @method('post')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="setMhsLabel">Tambah Mahasiswa</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="tahun_ajaran_id" class="form-label">Tahun Masuk</label>
                            <select class="form-select" name="tahun_ajaran_id" id="tahun_ajaran_id" onchange="getMhs()">
                                <option value="">Pilih Tahun Masuk</option>
                                @foreach ($tahunMasuk as $tahun)
                                    <option value="{{ $tahun->id }}">{{ $tahun->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="mhs_id" class="form-label">Tahun Masuk</label>
                            <select class="form-select select2" name="mhs_id[]" id="mhs_id" style="width: 100%;" multiple>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary"
                            onclick="submitForm(this.form, this, () => tableMhs.ajax.reload())">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        let tableMhs;

        $(document).ready(function() {
            tableMhs = $('.table-matkul').DataTable({
                processing: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route('data-master.tahun-ajaran.matkul.mhs.data', ['id' => request('id'), 'matkul_id' => request('matkul_id')]) }}'
                },
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "login_key"
                    },
                    {
                        "data": "name"
                    },
                    {
                        "data": "options"
                    }
                ],
                pageLength: 25,
            });
        });

        function getMhs(){
            const tahun_masuk_id = $('#tahun_ajaran_id').val();

            $.ajax({
                url: `{{ route('data-master.tahun-ajaran.matkul.mhs.getMhs', ['id' => request('id'),'matkul_id' => request('matkul_id'),'tahun_masuk_id' => ":tahun_masuk_id"]) }}`.replace(':tahun_masuk_id', tahun_masuk_id),
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    $('#mhs_id').empty();
                    $.each(res, function(key, value) {
                        $('#mhs_id').append(
                            `<option value="${value.id}">${value.name} (${value.login_key})</option>`
                        );
                    });
                },
                error: function(err) {
                    alert('Gagal get mahasiswa');
                }
            })
        }
    </script>
@endpush
