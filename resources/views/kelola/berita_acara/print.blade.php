@extends('components.template-pdf')

@section('title', 'Berita Acara')

@section('content')
    @php
        $dateNow = \Carbon\Carbon::now();
        \Carbon\Carbon::setLocale('id');

        $day = $dateNow->format('d');
        $month = $dateNow->translatedFormat('F'); // Nama bulan dalam bahasa Indonesia
        $year = $dateNow->format('Y');

        $dataFormat = "Jakarta, {$day} {$month} {$year}"
    @endphp
    <div class="daftar-hadir" style="page-break-after: always;">
        <h2 style="text-align: center;margin-top: 1.3rem;font-size: 1.1rem;margin-bottom: 0;">DAFTAR HADIR KULIAH MAHASISWA
        </h2>
        <h2 style="text-align: center;font-size: 1.1rem;margin-top: 10px;">{{ $semester->semester }} Tahun Akademik
            {{ $semester->tahun_ajaran }}</h2>

        <div style="margin-top: 1rem;">
            <table aria-hidden="true" style="width: 100%">
                <tr>
                    <td style="width: 48%;text-align: right;">Kode Mata Kuliah</td>
                    <td style="width: 2%">:</td>
                    <td style="font-weight: bold;width:48%;">{{ $matkul->kode }}</td>
                </tr>
                <tr>
                    <td style="width: 48%;text-align: right;">Nama Mata Kuliah</td>
                    <td style="width: 2%">:</td>
                    <td style="font-weight: bold;width:48%;">{{ $matkul->nama }}</td>
                </tr>
                <tr>
                    <td style="width: 48%;text-align: right;">Dosen Pengampu</td>
                    <td style="width: 2%">:</td>
                    <td style="font-weight: bold;width:48%;">{{ Auth::user()->name }}</td>
                </tr>
                <tr>
                    <td style="width: 48%;text-align: right;">SKS</td>
                    <td style="width: 2%">:</td>
                    <td style="font-weight: bold;width:48%;">{{ $matkul->sks_mata_kuliah }}</td>
                </tr>
            </table>
        </div>
        <div style="margin-top: 2rem;">
            <table aria-label="table-daftar-hadir" style="width: 100%;text-align:center" class="bordered">
                <thead>
                    <tr>
                        <th style="padding: 11px;width: 20%">Timestamp</th>
                        <th style="padding: 11px;width: 15%">Tanggal</th>
                        <th style="padding: 11px;width: 15%">Jam</th>
                        <th style="padding: 11px;width: 50%">Mahasiswa</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jadwal as $item)
                        <tr>
                            <td>{{ $item->created_at }}</td>
                            <td>{{ date('d-m-Y', strtotime($item->created_at)) }}</td>
                            <td>{{ date('H:i', strtotime($matkul->jam_mulai)) }} -
                                {{ date('H:i', strtotime($matkul->jam_akhir)) }}</td>
                            <td>
                                <table style="width: 100%;text-align: center;" aria-hidden="true">
                                    @foreach ($item->mahasiswa as $mhs)
                                        <tr>
                                            <td style="border-width: 0">{{ $mhs->name }}</td>
                                            <td style="border-width: 0">{{ $mhs->login_key }}</td>
                                            <td style="border-width: 0">{{ $mhs->pivot->status }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <table aria-hidden="true" style="margin-top: 1rem;margin-left: 27.5rem;">
                <tr>
                    <td style="text-align: center;">
                        {{ $dataFormat }}
                    </td>
                </tr>
                <tr>
                    <td>
                        <img src="{{ public_path() . '/image/ttd_direktur.jpg' }}" alt="" style="width: 13rem">
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="berita-acara" style="page-break-after: always;">
        <h2 style="text-align: center;margin-top: 1.3rem;font-size: 1.1rem;margin-bottom: 0;">BERITA ACARA KULIAH MAHASISWA
        </h2>
        <h2 style="text-align: center;font-size: 1.1rem;margin-top: 10px;">{{ $semester->semester }} Tahun Akademik
            {{ $semester->tahun_ajaran }}</h2>

        <div style="margin-top: 1rem;">
            <table aria-hidden="true" style="width: 100%">
                <tr>
                    <td style="width: 48%;text-align: right;">Kode Mata Kuliah</td>
                    <td style="width: 2%">:</td>
                    <td style="font-weight: bold;width:48%;">{{ $matkul->kode }}</td>
                </tr>
                <tr>
                    <td style="width: 48%;text-align: right;">Nama Mata Kuliah</td>
                    <td style="width: 2%">:</td>
                    <td style="font-weight: bold;width:48%;">{{ $matkul->nama }}</td>
                </tr>
                <tr>
                    <td style="width: 48%;text-align: right;">Dosen Pengampu</td>
                    <td style="width: 2%">:</td>
                    <td style="font-weight: bold;width:48%;">{{ Auth::user()->name }}</td>
                </tr>
                <tr>
                    <td style="width: 48%;text-align: right;">SKS</td>
                    <td style="width: 2%">:</td>
                    <td style="font-weight: bold;width:48%;">{{ $matkul->sks_mata_kuliah }}</td>
                </tr>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            <table aria-label="table-daftar-hadir" style="width: 100%;text-align:center" class="bordered">
                <thead>
                    <tr>
                        <th style="padding: 11px;width: 20%">Timestamp</th>
                        <th style="padding: 11px;width: 20%">Waktu</th>
                        <th style="padding: 11px;width: 20%">Jumlah mahasiswa yang hadir</th>
                        <th style="padding: 11px;width: 20%">Jumlah mahasiswa yang tidak hadir</th>
                        <th style="padding: 11px;width: 20%">Materi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jadwal as $item)
                        <tr>
                            <td>{{ $item->created_at }}</td>
                            <td>{{ $item->created_at }}</td>
                            <td>{{ $item->mahasiswa()->where('status', 'H')->count() }}</td>
                            <td>{{ $item->mahasiswa()->where('status', '!=', 'H')->count() }}</td>
                            <td>{{ $item->materi }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <table aria-hidden="true" style="margin-top: 1rem;margin-left: 27.5rem;">
                <tr>
                    <td style="text-align: center;">
                        {{ $dataFormat }}
                    </td>
                </tr>
                <tr>
                    <td>
                        <img src="{{ public_path() . '/image/ttd_direktur.jpg' }}" alt="" style="width: 13rem">
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="nilai" style="page-break-after: never;">
        <h2 style="text-align: center;margin-top: 1.3rem;font-size: 1.1rem;margin-bottom: 0;">DAFTAR NILAI
        </h2>
        <h2 style="text-align: center;font-size: 1.1rem;margin-top: 10px;">{{ $semester->semester }} Tahun Akademik
            {{ $semester->tahun_ajaran }}</h2>

        <div style="margin-top: 1rem;">
            <table aria-hidden="true" style="width: 100%">
                <tr>
                    <td style="width: 48%;text-align: right;">Kode Mata Kuliah</td>
                    <td style="width: 2%">:</td>
                    <td style="font-weight: bold;width:48%;">{{ $matkul->kode }}</td>
                </tr>
                <tr>
                    <td style="width: 48%;text-align: right;">Nama Mata Kuliah</td>
                    <td style="width: 2%">:</td>
                    <td style="font-weight: bold;width:48%;">{{ $matkul->nama }}</td>
                </tr>
                <tr>
                    <td style="width: 48%;text-align: right;">Dosen Pengampu</td>
                    <td style="width: 2%">:</td>
                    <td style="font-weight: bold;width:48%;">{{ Auth::user()->name }}</td>
                </tr>
                <tr>
                    <td style="width: 48%;text-align: right;">SKS</td>
                    <td style="width: 2%">:</td>
                    <td style="font-weight: bold;width:48%;">{{ $matkul->sks_mata_kuliah }}</td>
                </tr>
            </table>
            <div style="margin-top: 1rem;">
                <table aria-label="table-nilai" style="width: 100%;text-align:center" class="bordered">
                    <thead>
                        <tr>
                            <th style="padding: 11px;" rowspan="2">No</th>
                            <th style="padding: 11px;" rowspan="2">NIM</th>
                            <th style="padding: 11px;" rowspan="2">Nama</th>
                            <th style="padding: 11px;" rowspan="2">Kehadiran</th>
                            <th style="padding: 11px;" rowspan="2">Tugas</th>
                            <th style="padding: 11px;" rowspan="2">UTS</th>
                            <th style="padding: 11px;" rowspan="2">UAS</th>
                            <th style="padding: 11px;" colspan="2">Nilai Akhir</th>
                        </tr>
                        <tr>
                            <th style="padding: 11px;">Angka</th>
                            <th style="padding: 11px;">Huruf</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($nilai as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->login_key }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->presensi }}</td>
                                <td>{{ $item->tugas }}</td>
                                <td>{{ $item->uts }}</td>
                                <td>{{ $item->uas }}</td>
                                <td>{{ $item->nilai_akhir }}</td>
                                <td>{{ $item->mutu }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <table aria-hidden="true" style="margin-top: 1rem;margin-left: 27.5rem;">
                    <tr>
                        <td>
                            {{ $dataFormat }}
                        </td>
                    </tr>
                    <tr>
                        <div style="height: 6rem"></div>
                    </tr>
                    <tr>
                        <td style="text-align: center">{{ Auth::user()->name }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endsection
