@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="text-capitalize">Rekap Jadwal ({{ parseDate($gaji->tgl_awal) }} - {{ parseDate($gaji->tgl_akhir) }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Mata Kuliah</th>
                                    <th>Presensi Mulai</th>
                                    <th>Presensi Selesai</th>
                                    <th>Tanggal</th>
                                    <th>Materi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        let table;
        $(document).ready(function() {
            table = $('.table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('gaji.rekap_jadwal_data', ['gaji_id' => request('gaji_id')]) }}',
                columns: [
                    {
                        "data": "type"
                    },
                    {
                        "data": "matkul"
                    },
                    {
                        "data": "presensi_mulai"
                    },
                    {
                        "data": "presensi_selesai"
                    },
                    {
                        "data": "tgl"
                    },
                    {
                        "data": "materi"
                    },
                    {
                        "data": "status"
                    },
                ],
                pageLength: 25,
            });
        });
    </script>
@endpush
