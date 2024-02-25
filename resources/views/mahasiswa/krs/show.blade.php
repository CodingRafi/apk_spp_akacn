    @extends('mylayouts.main')

    @section('container')
    @php
        $check_tgl = $tahun_semester->tgl_mulai_krs <= date('Y-m-d') && $tahun_semester->tgl_akhir_krs >= date('Y-m-d');
    @endphp
        <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('krs.index') }}"><i class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                            <h5 class="text-capitalize mb-0">KRS {{ $tahun_semester->nama }}</h5>
                        </div>
                        
                        @if ($check_tgl)
                            <button type="button" class="btn btn-primary"
                                onclick="addForm('{{ route('krs.store', $tahun_semester->id) }}', 'Tambah Mata Kuliah', '#addMatkul', getMatkul)">
                                Tambah Mata Kuliah
                            </button>
                        @endif
                    </div>
                    <div class="card-body">
                        @if (!$krs || ($krs && $krs->status == 'pending'))
                            @if (!$check_tgl)
                                <div class="alert alert-danger">
                                    Bukan waktu pengisian KRS
                                </div>
                            @else
                                <div class="alert alert-info">
                                    Silahkan pilih mata kuliah yang ingin diambil. Maximal
                                    <strong>{{ $tahun_semester->jatah_sks }}</strong> SKS. Tanggal pengisian
                                    <strong>{{ parseDate($tahun_semester->tgl_mulai_krs) }}</strong> -
                                    <strong>{{ parseDate($tahun_semester->tgl_akhir_krs) }}</strong>
                                </div>
                            @endif
                        @endif
                        <div class="table-responsive">
                            <table class="table table-pembayaran">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode</th>
                                        <th>Nama</th>
                                        <th>SKS</th>
                                        @if ($check_tgl)
                                            <th>Aksi</th>
                                        @endif
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($check_tgl)
            <div class="modal fade" id="addMatkul" tabindex="-1" aria-labelledby="addMatkulLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="addMatkulLabel">Modal title</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-info">
                                    <p class="mb-0">Maximal <strong>{{ $tahun_semester->jatah_sks }}</strong> SKS. Total
                                        SKS
                                        diambil <strong class="sks_diambil">0</strong></p>
                                </div>
                                <span class="text-warning"><small>*note Total SKS diambil = Matkul yang sudah di pilih dan
                                        select</small></span>
                                <div class="mb-0">
                                    <label for="tahun_matkul_id" class="form-label">Mata Kuliah</label>
                                    <select class="form-select select2" name="tahun_matkul_id[]" id="tahun_matkul_id"
                                        multiple style="width: 100%">
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary"
                                    onclick="submitForm(this.form, this)">Tambahkan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endsection

    @push('js')
        <script>
            let table;
            let max_sks = {{ $tahun_semester->jatah_sks }};
            let total_sks = 0;

            function getMatkul() {
                $('#tahun_matkul_id').empty().attr('disabled', 'disabled');
                $.ajax({
                    type: "GET",
                    url: "{{ route('krs.getMatkul', $tahun_semester->id) }}",
                    success: function(res) {
                        $.each(res.data, function(i, e) {
                            $('#tahun_matkul_id').append(
                                `<option value="${e.id}" data-sks="${e.sks_mata_kuliah}">${e.kode} - ${e.nama} (${e.sks_mata_kuliah} SKS | ${e.dosen})</option>`
                            )
                        })
                        $('#tahun_matkul_id').removeAttr('disabled');
                        getTotalSKS();
                    },
                    error: function() {
                        alert('Gagal get matkul, harap hubungi administrator');
                        $('#tahun_matkul_id').removeAttr('disabled');
                    }
                })
            }

            function getTotalSKS() {
                $.ajax({
                    type: "GET",
                    url: "{{ route('krs.getTotalSKS', $tahun_semester->id) }}",
                    success: function(res) {
                        total_sks = parseInt(res.total);
                        $('.sks_diambil').text(res.total);
                    },
                    error: function() {
                        alert('Gagal get total SKS, harap hubungi administrator');
                    }
                })
            }

            $('#tahun_matkul_id').on('change', function(e) {
                let sks = total_sks;
                ($(this).val()).forEach(e => {
                    sks += parseInt($('#tahun_matkul_id option[value="' + e + '"]').data('sks'));
                });
                $('.sks_diambil').text(sks);

                if (sks > max_sks) {
                    $('#addMatkul .alert').removeClass('alert-info').addClass('alert-danger');
                } else {
                    $('#addMatkul .alert').removeClass('alert-danger').addClass('alert-info');
                }
            })

            $(document).ready(function() {
                table = $('.table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: '{{ route('krs.dataMatkul', ['tahun_semester_id' => $tahun_semester->id]) }}',
                    columns: [{
                            "data": "DT_RowIndex"
                        },
                        {
                            "data": "kode"
                        },
                        {
                            "data": "nama"
                        },
                        {
                            "data": "sks_mata_kuliah"
                        },
                        @if ($check_tgl)
                            {
                                "data": "options"
                            }
                        @endif
                    ],
                    pageLength: 25,
                    responsive: true,
                });
            });
        </script>
    @endpush
