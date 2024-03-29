    @extends('mylayouts.main')

    @section('container')
        <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('khs.index') }}"><i class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                            <h5 class="text-capitalize mb-0">Kartu Hasil Studi {{ $tahun_semester->nama }}</h5>
                        </div>
                        <a href="{{ route('khs.print', request('tahun_semester_id')) }}" class="btn btn-primary">Download KHS</a>
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
                <form action="{{ route('kuesioner.store') }}">
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
                                            @for ($i = 1; $i <= 5; $i++)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio"
                                                        name="{{ $item->id }}"
                                                        id="{{ $item->id }}{{ $i }}" required
                                                        value="{{ $i }}">
                                                    <label class="form-check-label"
                                                        for="{{ $item->id }}{{ $i }}">
                                                        {{ $i }}
                                                    </label>
                                                </div>
                                            @endfor
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
                            <td><button class="btn btn-primary" onclick="isiKuesioner('${e.tahun_matkul_id}')">Isi Kuesioner</button></td>
                            </tr>`;
                    } else {
                        table +=
                            `<tr>
                            <td>${e.kode_mk}</td>
                            <td>${e.matkul}</td>
                            <td>${e.sks ?? ''}</td>
                            <td>${e.nilai ?? ''}</td>
                            <td>${e.bobot ?? ''}</td>
                            <td>${e.bobot_x_sks}</td>
                            <td><div class="badge bg-success text-white">SUDAH DIISI</div></td>
                            </tr>`;
                    }

                    total_sks += parseInt(e.jml_sks) ?? 0;
                    bobot_x_sks += parseInt(e.bobot_x_sks) ?? 0;
                })

                table += `
                <tr class="py-4">
                    <td colspan="2" style="text-align: right;font-weight: bold;border-bottom:0;">Total</td>
                    <td colspan="3" class="fw-bold">${total_sks}</td>
                    <td class="fw-bold py-4">${bobot_x_sks}</td>
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
                    <td colspan="2">${(ipk.bobot_x_sks > 0 || ipk.total_sks > 0 ? (ipk.bobot_x_sks / ipk.total_sks).toFixed(2) : 0)}</td>
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
                    url: "{{ route('khs.data', ['tahun_semester_id' => $tahun_semester->id]) }}",
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
