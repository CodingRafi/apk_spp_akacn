@extends('mylayouts.main')

@push('css')
    <style>
        .select2-container:has(span[aria-labelledby="select2-filter-tahun-ajaran-container"]) {
            z-index: 999;
        }
    </style>
@endpush

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card" id="card-list-jadwal">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="text-capitalize mb-0">Jadwal</h5>
                    @can('add_kelola_presensi')
                        <button type="button" class="btn btn-primary"
                            onclick="addForm('{{ route('kelola-presensi.jadwal.store') }}', 'Tambah Jadwal', '#jadwal', clearForm)">
                            Tambah
                        </button>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <select id="filter-tahun-ajaran" class="select2" onchange="getSemester();filterGetMatkul();"
                                style="width: 100%;">
                                <option value="">Pilih Tahun Ajaran</option>
                                @foreach ($tahunAjarans as $tahun_ajaran)
                                    <option value="{{ $tahun_ajaran->id }}">{{ $tahun_ajaran->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <select id="filter-prodi" class="form-select" onchange="getSemester();filterGetMatkul();">
                                <option value="">Pilih Prodi</option>
                                @foreach ($prodis as $prodi)
                                    <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <select id="filter-tahun-semester" class="form-select">
                                <option value="">Pilih Semester</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <select id="filter-tahun-matkul" class="form-select">
                                <option value="">Pilih Mata Kuliah</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <select id="filter-status" class="form-select">
                                <option value="all">Pilih Status</option>
                                <option value="1">Menunggu Verifikasi</option>
                                <option value="2">Disetujui</option>
                                <option value="3">Ditolak</option>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Presensi</th>
                                    <th>Kode Matkul</th>
                                    <th>Tanggal</th>
                                    <th>Matkul</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="jadwal" tabindex="-1" aria-labelledby="jadwalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form action="{{ route('kelola-presensi.whitelist-ip.store') }}" method="post">
                    @method('post')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="jadwalLabel"></h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="div-alert"></div>
                        <div class="mb-3">
                            <label for="prodi_id" class="form-label">Prodi</label>
                            <select name="prodi_id" id="prodi_id" class="form-select" style="width: 100%"
                                onchange="getPelajaran()">
                                <option value="">Pilih Prodi</option>
                                @foreach ($prodis as $prodi)
                                    <option value="{{ $prodi->id }}">{{ $prodi->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tahun_ajaran_id" class="form-label">Tahun Ajaran</label>
                            <select name="tahun_ajaran_id" id="tahun_ajaran_id" style="width: 100%"
                                onchange="getPelajaran()">
                                <option value="">Pilih Tahun Ajaran</option>
                                @foreach ($tahunAjarans as $tahun_ajaran)
                                    <option value="{{ $tahun_ajaran->id }}">{{ $tahun_ajaran->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tahun_matkul_id" class="form-label">Pelajaran</label>
                            <select name="tahun_matkul_id" id="tahun_matkul_id" style="width: 100%">
                                <option value="">Pilih Pelajaran</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="kode" class="form-label">Kode Presensi</label>
                            <div class="d-flex" style="gap: 1rem;">
                                <input class="form-control" type="text" id="kode" name="kode" />
                                <button class="btn btn-primary btn-generate" onclick="generateCode()"
                                    type="button">Generate</button>
                            </div>
                        </div>
                        @if (getRole()->name == 'admin')
                            <div class="mb-3">
                                <label for="type" class="form-label">Type</label>
                                <select name="type" id="type" class="form-control">
                                    <option value="">Pilih Type</option>
                                    <option value="ujian">Ujian</option>
                                    <option value="pertemuan">Pertemuan</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="tgl" class="form-label">Tanggal</label>
                                <input class="form-control" type="date" name="tgl" />
                            </div>
                            <div class="div-ujian"></div>
                            <div class="div-pengajar"></div>
                        @else
                            <input type="hidden" name="type" value="pertemuan">
                            <input type="hidden" name="pengajar_id" value="{{ Auth::user()->id }}">
                            <div class="mb-3">
                                <label for="materi_id" class="form-label">Materi</label>
                                <select name="materi_id" id="materi_id" class="form-control">
                                    <option value="">Pilih Materi</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="ket" class="form-label">Keterangan</label>
                                <textarea cols="30" rows="10" class="form-control" name="ket"></textarea>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer justify-content-start px-3">
                        <button type="button" class="btn btn-primary"
                            onclick="submitForm(this.form, this, () => table.ajax.reload())">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <template id="select-ujian">
        <div class="mb-3">
            <label for="jenis" class="form-label">Jenis Ujian</label>
            <select name="jenis" id="jenis" class="form-control">
                <option value="">Pilih Jenis Ujian</option>
            </select>
        </div>
    </template>

    <template id="select-pengajar">
        <div class="mb-3">
            <label for="pengajar_id" class="form-label"></label>
            <select name="pengajar_id" id="pengajar_id" class="form-control">
                <option value="">Pilih</option>
            </select>
        </div>
    </template>

    <template id="select-materi">
        <div class="mb-3">
            <label for="materi_id" class="form-label">Materi</label>
            <select name="materi_id" id="materi_id" class="form-control">
                <option value="">Pilih Materi</option>
            </select>
        </div>
    </template>
@endsection

@push('js')
    <script>
        function generateCode() {
            $('#kode').val(generateRandomCode(6));
        }

        // Named function for the main change listener to allow easy unbinding/rebinding
        const handleTypeMatkulChange = () => generateForm();

        function getTotal(data = {}) {
            $('.div-alert').empty();
            const tahun_ajaran_id = data.tahun_ajaran_id ?? $('#tahun_ajaran_id').val();
            const tahun_matkul_id = data.tahun_matkul_id ?? $('#tahun_matkul_id').val();
            if (tahun_ajaran_id && tahun_matkul_id) {
                $.ajax({
                    url: "{{ route('kelola-presensi.jadwal.getTotalPelajaran', ['tahun_ajaran_id' => ':tahun_ajaran_id', 'tahun_matkul_id' => ':tahun_matkul_id']) }}"
                        .replace(':tahun_matkul_id', tahun_matkul_id).replace(':tahun_ajaran_id', tahun_ajaran_id),
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        $('.div-alert').empty();
                        if (res.total < 14) {
                            $('.div-alert').append(
                                `<div class="alert alert-primary" role="alert">Sudah terjadi ${res.total} kali pelajaran</div>`
                            );
                        } else {
                            $('.div-alert').append(
                                `<div class="alert alert-danger" role="alert">Sudah terjadi ${res.total} kali pelajaran. Tidak bisa melakukan pelajaran</div>`
                            )
                        }
                    },
                    error: function(err) {
                        showAlert(err.responseJSON.message)
                    }
                })
            }
        }

        // Modified get_materi to handle unbinding/rebinding
        function get_materi(data = {}) {
            $('#materi_id').empty().append(`<option value="">Pilih Materi</option>`);
            const tahun_matkul_id = data.tahun_matkul_id ?? $('#tahun_matkul_id').val();
            const tahun_ajaran_id = data.tahun_ajaran_id ?? $('#tahun_ajaran_id').val();

            @if (getRole()->name == 'admin')
                const check = tahun_matkul_id && tahun_ajaran_id && $('#type').val() && $('#type')
                    .val() == 'pertemuan';
            @else
                const check = tahun_matkul_id && tahun_ajaran_id;
            @endif

            if (check) {
                $.ajax({
                    url: "{{ route('kelola-presensi.jadwal.getMateri', ['tahun_ajaran_id' => ':tahun_ajaran_id', 'tahun_matkul_id' => ':tahun_matkul_id']) }}"
                        .replace(':tahun_matkul_id', tahun_matkul_id).replace(':tahun_ajaran_id', tahun_ajaran_id),
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        except: data.materi_id
                    },
                    success: function(res) {
                        $.each(res.data, function(i, e) {
                            $('#materi_id').append(
                                `<option value="${e.id}">${e.materi} (${e.type})</option>`)
                        })

                        if (data.materi_id) {
                            // Temporarily unbind the main change listener
                            $('#type, #tahun_matkul_id').off('change', handleTypeMatkulChange);
                            // Set value and trigger change for Select2
                            $('#materi_id').val(data.materi_id).trigger('change');
                            // Rebind the main change listener
                            $('#type, #tahun_matkul_id').on('change', handleTypeMatkulChange);
                        }
                    },
                    error: function(err) {
                        alert('Gagal get materi');
                    }
                })
            }
        }

        // Modified getPelajaran to handle unbinding/rebinding
        function getPelajaran(tahun_matkul_id_param) {
            const prodi_id = $('#prodi_id').val();
            const tahun_ajaran_id = $('#tahun_ajaran_id').val();
            if (prodi_id && tahun_ajaran_id) {
                $('#tahun_matkul_id').empty().append(`<option value="">Pilih Pelajaran</option>`);
                $.ajax({
                    url: '{{ route('kelola-presensi.jadwal.getPelajaran', ['prodi_id' => ':prodi_id', 'tahun_ajaran_id' => ':tahun_ajaran_id']) }}'
                        .replace(':prodi_id', prodi_id).replace(':tahun_ajaran_id', tahun_ajaran_id),
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        $.each(res.data, function(i, e) {
                            $('#tahun_matkul_id').append(
                                `<option value="${e.id}">${e.label}</option>`)
                        })

                        if (tahun_matkul_id_param) {
                            // Temporarily unbind the main change listener
                            $('#type, #tahun_matkul_id').off('change', handleTypeMatkulChange);
                            // Set value and trigger change for Select2
                            $('#tahun_matkul_id').val(tahun_matkul_id_param).trigger('change');
                            // Rebind the main change listener
                            $('#type, #tahun_matkul_id').on('change', handleTypeMatkulChange);
                        }
                    },
                    error: function(err) {
                        alert('Gagal get matkul');
                    }
                })
            }
        }

        function getSemester() {
            const prodi_id = $('#filter-prodi').val();
            const tahun_ajaran_id = $('#filter-tahun-ajaran').val();

            if (prodi_id && tahun_ajaran_id) {
                $('#filter-tahun-semester').empty().append(`<option value="">Pilih Semester</option>`);
                $.ajax({
                    url: '{{ route('kelola-presensi.jadwal.getSemester', ['prodi_id' => ':prodi_id', 'tahun_ajaran_id' => ':tahun_ajaran_id']) }}'
                        .replace(':prodi_id',
                            prodi_id).replace(':tahun_ajaran_id', tahun_ajaran_id),
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        $.each(res.data, function(i, e) {
                            $('#filter-tahun-semester').append(
                                `<option value="${e.id}">${e.nama}</option>`)
                        })
                    },
                    error: function(err) {
                        alert('Gagal get semester');
                    }
                })
            }
        }

        function filterGetMatkul() {
            const prodi_id = $('#filter-prodi').val();
            const tahun_ajaran_id = $('#filter-tahun-ajaran').val();
            $('#filter-tahun-matkul').empty().append(`<option value="">Pilih Matkul</option>`);
            if (prodi_id && tahun_ajaran_id) {
                $.ajax({
                    url: '{{ route('kelola-presensi.jadwal.getMatkul', ['prodi_id' => ':prodi_id', 'tahun_ajaran_id' => ':tahun_ajaran_id']) }}'
                        .replace(':prodi_id', prodi_id).replace(':tahun_ajaran_id', tahun_ajaran_id),
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        $.each(res.data, function(i, e) {
                            $('#filter-tahun-matkul').append(
                                `<option value="${e.id}">${e.nama}</option>`)
                        })
                    },
                    error: function(err) {
                        alert('Gagal get matkul');
                    }
                })
            }
        }

        function getPengajar(tahun_ajaran_id, tahun_matkul_id, data = {}) {
            $('#pengajar_id').empty().append(`<option value="">Pilih Pengajar</option>`);
            if (tahun_ajaran_id && tahun_matkul_id) {
                $.ajax({
                    url: '{{ route('kelola-presensi.jadwal.getPengajar', ['tahun_ajaran_id' => ':tahun_ajaran_id', 'tahun_matkul_id' => ':tahun_matkul_id']) }}'
                        .replace(':tahun_matkul_id', tahun_matkul_id).replace(':tahun_ajaran_id', tahun_ajaran_id),
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        $.each(res.data, function(i, e) {
                            $('#pengajar_id').append(
                                `<option value="${e.id}">${e.name} | ${e.login_key}</option>`)
                        })

                        if (data.pengajar_id) {
                            $('#pengajar_id').val(data.pengajar_id).trigger('change'); // Trigger for Select2
                        }
                    },
                    error: function(err) {
                        alert('Gagal get pengajar');
                    }
                })
            }
        }

        function getPengawas(data = {}) {
            $('#pengajar_id').empty().append(`<option value="">Pilih Pengawas</option>`);
            $.ajax({
                url: '{{ route('kelola-presensi.jadwal.getPengawas') }}',
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    $.each(res.data, function(i, e) {
                        $('#pengajar_id').append(
                            `<option value="${e.id}">${e.name} | ${e.login_key}</option>`)
                    })

                    if (data.pengajar_id) {
                        $('#pengajar_id').val(data.pengajar_id).trigger('change'); // Trigger for Select2
                    }
                },
                error: function(err) {
                    alert('Gagal get pengawas');
                }
            })
        }

        $('#tahun_matkul_id, #type').on('change', function() {
            get_materi();
        })

        function clearForm() {
            // Clear all relevant form fields and trigger change for Select2 to update its display
            $('#jadwal form')[0].reset(); // Resets all form elements
            $('#jadwal select').val('').trigger('change'); // Clears Select2 fields

            $('.div-ujian, .div-pengajar, .div-alert').empty();
        }

        // Initial listener for user changes. This should trigger generateForm()
        // It's crucial this listener uses the named function.
        $('#type, #tahun_matkul_id').on('change', handleTypeMatkulChange);

        function editJadwal(data) {
            // Temporarily unbind the main change listener to prevent recursive calls
            $('#type, #tahun_matkul_id').off('change', handleTypeMatkulChange);

            clearForm(); // Clear the form first

            // Populate common fields and trigger change for Select2
            $('#prodi_id').val(data.prodi_id).trigger('change');
            $('#tahun_ajaran_id').val(data.tahun_ajaran_id).trigger('change');
            $('#kode').val(data.kode);
            $('input[name="tgl"]').val(data.tgl);
            $('textarea[name="ket"]').val(data.ket);
            $('#type').val(data.type).trigger('change'); // Important to set type first

            // Call functions that fetch and set data for dependent fields.
            // These functions now handle their own Select2 triggers internally.
            getPelajaran(data.tahun_matkul_id);
            generateForm(data); // Pass the full data object to generateForm

            // Rebind the main change listener after all form fields are populated
            $('#type, #tahun_matkul_id').on('change', handleTypeMatkulChange);
        }

        function generateForm(data = {}) {
            let type = data.type ?? $('#type').val();
            let tahun_matkul_id = data.tahun_matkul_id ?? $('#tahun_matkul_id').val();
            let tahun_ajaran_id = data.tahun_ajaran_id ?? $('#tahun_ajaran_id').val();

            if (type && tahun_matkul_id && tahun_ajaran_id) {
                $('.div-ujian, .div-pengajar, .div-alert').empty();
                $('.div-pengajar').html($('#select-pengajar').html());
                // $('#pengajar_id').empty(); // This will be handled by getPengajar/getPengawas

                if (type == 'ujian') {
                    $('label[for="pengajar_id"]').text('Pengawas');
                    $('.div-ujian').html($('#select-ujian').html());
                    getPengawas(data); // Pass data for pre-selection
                    getJenisUjian(tahun_ajaran_id, tahun_matkul_id, data); // Pass data for pre-selection
                } else { // type == 'pertemuan'
                    $('label[for="pengajar_id"]').text('Pengajar');
                    $('.div-pengajar').append($('#select-materi').html());

                    // Only call get_materi if it's a meeting AND a materi_id is provided,
                    // or if the select2 for materi is present and needs to be populated on add.
                    // This prevents unnecessary calls or issues if type is ujian.
                    if (data.materi_id || ($('#jadwal').data('bs.modal') && $('#jadwal').data('bs.modal')._element.id ===
                            'jadwal' && type === 'pertemuan')) {
                        get_materi(data); // Pass data for pre-selection
                    }

                    getTotal(data); // Always get total for 'pertemuan'
                    getPengajar(tahun_ajaran_id, tahun_matkul_id, data); // Pass data for pre-selection
                }
            }
        }

        function getJenisUjian(tahun_ajaran_id, tahun_matkul_id, data = {}) {
            if (tahun_ajaran_id && tahun_matkul_id) {
                $.ajax({
                    url: '{{ route('kelola-presensi.jadwal.getJenisUjian', ['tahun_ajaran_id' => ':tahun_ajaran_id', 'tahun_matkul_id' => ':tahun_matkul_id']) }}'
                        .replace(':tahun_matkul_id', tahun_matkul_id).replace(':tahun_ajaran_id', tahun_ajaran_id),
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        except: data.jenis_ujian
                    },
                    success: function(res) {
                        $('#jenis').empty().append(`<option value="">Pilih Jenis Ujian</option>`);
                        $.each(res.data, function(i, e) {
                            $('#jenis').append(`<option value="${e}">${e.toUpperCase()}</option>`)
                        })

                        if (data.jenis_ujian) {
                            $('#jenis').val(data.jenis_ujian).trigger('change'); // Trigger for Select2
                        }
                    },
                    error: function(err) {
                        alert('Gagal get jenis ujian');
                    }
                })
            }
        }

        let table;
        $(document).ready(function() {
            // Initialize Select2 for modal fields
            $('#tahun_ajaran_id, #tahun_matkul_id').select2({
                dropdownParent: $("#jadwal") // Ensure dropdown appears correctly within the modal
            });

            table = $('.table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route('kelola-presensi.jadwal.data') }}',
                    data: function(p) {
                        p.prodi_id = $('#filter-prodi').val();
                        p.tahun_semester_id = $('#filter-tahun-semester').val();
                        p.tahun_matkul_id = $('#filter-tahun-matkul').val();
                        p.tahun_ajaran_id = $('#filter-tahun-ajaran').val();
                        p.status = $('#filter-status').val();
                    }
                },
                columns: [{
                        "data": "DT_RowIndex"
                    },
                    {
                        "data": "kode"
                    },
                    {
                        "data": "kode_matkul"
                    },
                    {
                        "data": "tgl"
                    },
                    {
                        "data": "matkul"
                    },
                    {
                        "data": "status"
                    },
                    {
                        "data": "options"
                    }
                ],
                pageLength: 25,
            });

            // Filter change events to reload DataTable
            $('#filter-tahun-semester, #filter-tahun-matkul, #filter-prodi, #filter-status, #filter-tahun-ajaran')
                .on('change', function() {
                    table.ajax.reload();
                });

            getSemester();
            filterGetMatkul();
        });
    </script>
@endpush
