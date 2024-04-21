@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="text-capitalize">Transkip Nilai</h5>
                    <a href="{{ route('transkip.print') }}" class="btn btn-primary">Download Transkip</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-matkul text-center">
                            <thead>
                                <tr>
                                    <th class="col-4">Kode</th>
                                    <th class="col-4">Mata Kuliah</th>
                                    <th class="col-2">SKS</th>
                                    <th class="col-2">Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4" class="py-5">
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
@endsection

@push('js')
    <script>
        function generate_table(data) {
            let table = '';
            let total_sks = 0;
            let bobot_x_sks = 0;

            $.each(data, (semester, row) => {
                table += `
                <tr>
                    <td colspan="4" class="bg-info text-white text-start">${semester}</td>
                </tr>
                `;

                $.each(row, (i, e) => {
                    if (e.status == null || e.status == 0) {
                        table +=
                            `<tr>
                                <td>${e.kode_mk}</td>
                                <td>${e.matkul}</td>
                                <td colspan="2" class="text-center"><div class="badge bg-secondary text-white">BELUM TERSEDIA</div></td>
                            </tr>`;
                    } else if (e.status == 1 && e.kuesioner == null) {
                        table +=
                            `<tr>
                                <td>${e.kode_mk}</td>
                                <td>${e.matkul}</td>
                                <td colspan="2" class="text-center"><div class="badge bg-warning text-white">BELUM ISI KUESIONER</div></td>
                            </tr>`;
                    } else {
                        table +=
                            `<tr>
                                <td>${e.kode_mk}</td>
                                <td>${e.matkul}</td>
                                <td>${e.jml_sks ?? ''}</td>
                                <td>${e.mutu ?? ''}</td>
                            </tr>`;
                        total_sks += parseInt(e.jml_sks) ?? 0;
                        bobot_x_sks += parseFloat(e.bobot_x_sks) ?? 0;
                    }
                })
            })

            table += `
            <tr>
                <td colspan="4">
                    <div class="card my-4 col-md-4 shadow-none border-1 text-start">
                        <table class="table table-borderless">
                            <tr>
                                <th>Total SKS Lulus</th>
                                <td>${total_sks}</td>
                            </tr>
                            <tr>
                                <th>Total Mutu</th>
                                <td>${bobot_x_sks.toFixed(2)}</td>
                            </tr>
                            <tr>
                                <th>IPK</th>
                                <td>${ bobot_x_sks > 0 || total_sks > 0 ? (bobot_x_sks / total_sks).toFixed(2) : 0}</td>
                            </tr>
                            <tr>
                                <th>SKS Dipeloreh</th>
                                <td>${total_sks}</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
            `;

            return table;
        }

        function loadData() {
            $.ajax({
                url: "{{ route('transkip.data') }}",
                dataType: "json",
                success: function(res) {
                    let table = generate_table(res.data)
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
