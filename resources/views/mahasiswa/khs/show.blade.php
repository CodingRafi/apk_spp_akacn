    @extends('mylayouts.main')

    @section('container')
        <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            @if (Auth::user()->hasRole('mahasiswa'))
                                <a href="{{ route('khs.index') }}"><i class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                            @else
                                <a href="{{ route('kelola-users.mahasiswa.show', $mhs_id) }}"><i
                                        class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                            @endif
                            <h5 class="text-capitalize mb-0">Kartu Hasil Studi {{ $tahun_semester->nama }}</h5>
                        </div>
                        @if (Auth::user()->hasRole('mahasiswa'))
                        <a href="{{ route('khs.print', request('tahun_semester_id')) }}" class="btn btn-primary">Download
                            KHS</a>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-matkul">
                                <thead>
                                    <tr>
                                        <th class="col-2">Kode MK</th>
                                        <th class="col-3">Mata Kuliah</th>
                                        <th class="col-1">SKS</th>
                                        <th class="col-1">Nilai</th>
                                        <th class="col-1">Bobot</th>
                                        <th class="col-2">Bobot X SKS</th>
                                        <th class="col-2">Kuesioner</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="Kuesioner" tabindex="-1" aria-labelledby="KuesionerLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <form action="{{ route('kuesioner.store') }}" method="post">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="KuesionerLabel">Kuesioner</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="tahun_matkul_id">
                            <input type="hidden" name="tahun_semester_id">
                            @foreach ($kuesioner as $item)
                                <div class="mb-3">
                                    <div>
                                        {!! $item->pertanyaan !!}
                                    </div>
                                    @if ($item->type == 'input')
                                        <input class="form-control" type="text" id="{{ $item->id }}"
                                            name="{{ $item->id }}" required />
                                    @else
                                        <div class="d-flex" style="gap: 1rem;">
                                            @foreach (config('services.choice_kuesioner') as $i => $choice)
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio"
                                                    name="{{ $item->id }}"
                                                    id="{{ $item->id }}{{ $i }}" required
                                                    value="{{ $i }}">
                                                <label class="form-check-label"
                                                    for="{{ $item->id }}{{ $i }}">
                                                    {{ $choice }}
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary"
                                onclick="submitForm(this.form, this, loadData)">Kirim</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endsection

    @push('js')
        <script>
            let tahun_semester_id = '{{ request('tahun_semester_id') }}';

            function generate_table(data, ipk) {
                let table = '';
                let total_sks = 0;
                let bobot_x_sks = 0;

                $.each(data, (i, e) => {
                    if (e.status == null || e.status == 0) {
                        table +=
                            `<tr>
                            <td>${e.kode_mk}</td>
                            <td>${e.matkul}</td>
                            <td colspan="4" class="text-center"><div class="badge bg-secondary text-white">BELUM TERSEDIA</div></td>
                            <td></td>
                            </tr>`;
                    } else if (e.status == 1 && e.kuesioner == null) {
                        table +=
                            `<tr>
                            <td>${e.kode_mk}</td>
                            <td>${e.matkul}</td>
                            <td colspan="4" class="text-center"><div class="badge bg-warning text-white">BELUM ISI KUESIONER</div></td>
                            @if (Auth::user()->hasRole('mahasiswa'))
                            <td><button class="btn btn-primary" onclick="isiKuesioner('${e.tahun_matkul_id}')">Isi Kuesioner</button></td>
                            @else
                            <td></td>
                            @endif
                            </tr>`;
                    } else {
                        table +=
                            `<tr>
                            <td>${e.kode_mk}</td>
                            <td>${e.matkul}</td>
                            <td>${e.jml_sks ?? ''}</td>
                            <td>${e.mutu ?? ''}</td>
                            <td>${e.nilai_mutu ?? ''}</td>
                            <td>${e.bobot_x_sks}</td>
                            <td><div class="badge bg-success text-white">SUDAH DIISI</div></td>
                            </tr>`;
                    }

                    if (e.kuesioner != null) {
                        total_sks += parseInt(e.jml_sks) ?? 0;
                        bobot_x_sks += parseFloat(e.bobot_x_sks) ?? 0;
                    }
                })

                let ipk_bobot_x_sks = Number(ipk.bobot_x_sks);
                let ipk_total_sks = Number(ipk.jml_sks);
                table += `
                <tr class="py-4">
                    <td colspan="2" style="text-align: right;font-weight: bold;border-bottom:0;">Total</td>
                    <td colspan="3" class="fw-bold">${total_sks}</td>
                    <td class="fw-bold py-4">${bobot_x_sks.toFixed(2)}</td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2" style="border-bottom:0;"></td>
                    <td colspan="3" class="fw-bold">Index Prestasi Semester</td>
                    <td colspan="2">${(bobot_x_sks > 0 || total_sks > 0 ? (bobot_x_sks / total_sks).toFixed(2) : 0)}</td>
                </tr>
                <tr>
                    <td colspan="2" style="border-bottom:0;"></td>
                    <td colspan="3" class="fw-bold">Index Prestasi Kumulatif</td>
                    <td colspan="2">${(ipk_bobot_x_sks > 0 || ipk_total_sks > 0 ? (ipk_bobot_x_sks / ipk_total_sks).toFixed(2) : 0)}</td>
                </tr>
                <tr>
                    <td colspan="2" style="border-bottom:0;"></td>
                    <td colspan="3" class="fw-bold">Total SKS</td>
                    <td colspan="2">${total_sks}</td>
                </tr>
                `

                return table;
            }

            function isiKuesioner(tahun_matkul_id) {
                $(`#Kuesioner`).modal("show");
                resetForm(`#Kuesioner form`);
                $('#Kuesioner input[name="tahun_matkul_id"]').val(tahun_matkul_id);
                $('#Kuesioner input[name="tahun_semester_id"]').val(tahun_semester_id);
            }

            function loadData() {
                $.ajax({
                    url: "{{ route('khs.data', ['tahun_semester_id' => $tahun_semester->id, 'mhs_id' => $mhs_id]) }}",
                    dataType: "json",
                    success: function(res) {
                        let table = generate_table(res.data, res.ipk)
                        $('.table-matkul tbody').empty().append(table);
                    },
                    error: function(err) {
                        console.log('Gagal get data');
                    }
                })
            }

            $(document).ready(function() {
                loadData()
            })
        </script>
    @endpush
