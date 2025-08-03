@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card" id="card-list-jadwal">
                <div class="card-header d-flex justify-content-between">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('kelola-presensi.jadwal.index') }}"><i
                                class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                        <h5 class="text-capitalize mb-0">Mata Kuliah : {{ $matkul->matkul }}</h5>
                    </div>
                    @can('add_kelola_presensi')
                        <button type="button" class="btn btn-primary"
                            onclick="addForm('{{ route('kelola-presensi.jadwal.tahun_matkul.store', ['tahun_matkul_id' => request('tahun_matkul_id')]) }}', 'Tambah Jadwal', '#jadwal', clearForm)">
                            Tambah
                        </button>
                    @endcan
                </div>
                <div class="card-body">
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
                            <label class="form-label">Mata Kuliah</label>
                            <input class="form-control" type="text" value="{{ $matkul->matkul }}" disabled />
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

    <template id="form-ujian">
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
        <div class="mb-3">
            <label for="tingkat" class="form-label">Tingkat/Semester</label>
            <input class="form-control" type="text" id="tingkat" name="tingkat" />
        </div>
        <div class="mb-3">
            <label for="ruang_id" class="form-label">Ruang</label>
            <select name="ruang_id" id="ruang_id" class="form-control">
                <option value="">Pilih</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="sifat_ujian_id" class="form-label">Sifat Ujian</label>
            <select name="sifat_ujian_id" id="sifat_ujian_id" class="form-control">
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
            $.ajax({
                url: "{{ route('kelola-presensi.jadwal.tahun_matkul.getTotalPelajaran', ['tahun_matkul_id' => request('tahun_matkul_id')]) }}",
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

        // Modified get_materi to handle unbinding/rebinding
        function get_materi(data = {}) {

            @if (getRole()->name == 'admin')
                const check = $('#type').val() && $('#type')
                    .val() == 'pertemuan';
            @endif

            if (check) {
                $.ajax({
                    url: "{{ route('kelola-presensi.jadwal.tahun_matkul.getMateri', ['tahun_matkul_id' => request('tahun_matkul_id')]) }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        except: data.materi_id
                    },
                    success: function(res) {
                        $('#materi_id').empty().append(`<option value="">Pilih Materi</option>`);

                        $.each(res.data, function(i, e) {
                            $('#materi_id').append(
                                `<option value="${e.id}">${e.materi} (${e.type})</option>`)
                        })

                        if (data.materi_id) {
                            // Temporarily unbind the main change listener
                            $('#type').off('change', handleTypeMatkulChange);
                            // Set value and trigger change for Select2
                            $('#materi_id').val(data.materi_id).trigger('change');
                            // Rebind the main change listener
                            $('#type').on('change', handleTypeMatkulChange);
                        }
                    },
                    error: function(err) {
                        alert('Gagal get materi');
                    }
                })
            }
        }

        function getPengajar(data = {}) {
            $('#pengajar_id').empty().append(`<option value="">Pilih Pengajar</option>`);
            $.ajax({
                url: '{{ route('kelola-presensi.jadwal.tahun_matkul.getPengajar', ['tahun_matkul_id' => request('tahun_matkul_id')]) }}',
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

        function getRuang(data = {}) {
            $('#ruang_id').empty().append(`<option value="">Pilih Ruang</option>`);
            $.ajax({
                url: '{{ route('kelola-presensi.jadwal.getRuang') }}',
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    $.each(res.data, function(i, e) {
                        $('#ruang_id').append(
                            `<option value="${e.id}">${e.nama}</option>`)
                    })

                    if (data.ruang_id) {
                        $('#ruang_id').val(data.ruang_id).trigger('change'); // Trigger for Select2
                    }
                },
                error: function(err) {
                    alert('Gagal get ruang');
                }
            })
        }

        function getSifatUjian(data = {}) {
            $('#sifat_ujian_id').empty().append(`<option value="">Pilih Sifat Ujian</option>`);
            $.ajax({
                url: '{{ route('kelola-presensi.jadwal.getSifatUjian') }}',
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    $.each(res.data, function(i, e) {
                        $('#sifat_ujian_id').append(
                            `<option value="${e.id}">${e.nama}</option>`)
                    })

                    if (data.sifat_ujian_id) {
                        $('#sifat_ujian_id').val(data.sifat_ujian_id).trigger('change'); // Trigger for Select2
                    }
                },
                error: function(err) {
                    alert('Gagal get ruang');
                }
            })
        }

        $('#type').on('change', function() {
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
        $('#type').on('change', handleTypeMatkulChange);

        function editJadwal(data) {
            // Temporarily unbind the main change listener to prevent recursive calls
            $('#type').off('change', handleTypeMatkulChange);

            clearForm(); // Clear the form first

            // Populate common fields and trigger change for Select2
            $('#kode').val(data.kode);
            $('input[name="tgl"]').val(data.tgl);
            $('textarea[name="ket"]').val(data.ket);
            $('#type').val(data.type).trigger('change'); // Important to set type first

            // Call functions that fetch and set data for dependent fields.
            // These functions now handle their own Select2 triggers internally.
            generateForm(data); // Pass the full data object to generateForm

            // Rebind the main change listener after all form fields are populated
            $('#type').on('change', handleTypeMatkulChange);
        }

        function generateForm(data = {}) {
            let type = data.type ?? $('#type').val();

            if (type) {
                $('.div-ujian, .div-pengajar, .div-alert').empty();
                $('.div-pengajar').html($('#select-pengajar').html());
                // $('#pengajar_id').empty(); // This will be handled by getPengajar/getPengawas

                if (type == 'ujian') {
                    $('label[for="pengajar_id"]').text('Pengawas');
                    $('.div-ujian').html($('#form-ujian').html());
                    $('#tingkat').val(data.tingkat);
                    getPengawas(data); // Pass data for pre-selection
                    getJenisUjian(data); // Pass data for pre-selection
                    getRuang(data);
                    getSifatUjian(data);
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
                    getPengajar(data); // Pass data for pre-selection
                }
            }
        }

        function getJenisUjian(data = {}) {
            $.ajax({
                url: '{{ route('kelola-presensi.jadwal.tahun_matkul.getJenisUjian', ['tahun_matkul_id' => request('tahun_matkul_id')]) }}',
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

        let table;
        $(document).ready(function() {
            table = $('.table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route('kelola-presensi.jadwal.tahun_matkul.dataTahunMatkul', ['tahun_matkul_id' => request('tahun_matkul_id')]) }}',
                    data: function(p) {
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
            $('#filter-status')
                .on('change', function() {
                    table.ajax.reload();
                });
        });
    </script>
@endpush
