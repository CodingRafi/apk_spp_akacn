@extends('components.template-pdf')

@section('title', 'Kartu Rencana Studi')

@section('content')
    <style>
        .table-container {
            width: 100%;
            border-collapse: collapse;
        }

        .table-item {
            text-align: center;
        }
    </style>
    @php
        $configHari = config('services.hari');
    @endphp

    <h2 style="text-align: center;margin-top: 1.3rem;font-size: 1.1rem;">KARTU RENCANA STUDI</h2>

    <div style="margin-top: 1rem;">
        <table style="float: left;width: 50%;font-size: 13px;" aria-hidden="true">
            <tr>
                <td style="width: 7rem;padding:0 auto;">NIM</td>
                <td style="padding:0 auto;">:</td>
                <td style="font-weight: bold;padding:0 auto;">{{ $krs->nim }}</td>
            </tr>
            <tr>
                <td style="width: 7rem;padding:0 auto;">Nama</td>
                <td style="padding:0 auto;">:</td>
                <td style="font-weight: bold;padding:0 auto;">{{ $krs->name }}</td>
            </tr>
            <tr>
                <td style="width: 7rem;padding:0 auto;">Dosen PA</td>
                <td style="padding:0 auto;">:</td>
                <td style="font-weight: bold;padding:0 auto;">{{ $krs->dosenPa }}</td>
            </tr>
        </table>
        <table style="float: left;font-size: 13px;" aria-hidden="true">
            <tr>
                <td style="width: 7rem;padding:0 auto;">Semester</td>
                <td style="padding:0 auto;">:</td>
                <td style="font-weight: bold;padding:0 auto;">{{ $krs->semester }}</td>
            </tr>
            <tr>
                <td style="width: 7rem;padding:0 auto;">Prodi</td>
                <td style="padding:0 auto;">:</td>
                <td style="font-weight: bold;padding:0 auto;">{{ $krs->prodi }}</td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    <div style="margin-top: 1rem;">
        <table aria-label="table-matkul" class="bordered" style="width: 100%;font-size:13px;">
            <thead>
                <tr>
                    <th style="padding: 11px;text-align: center;width: 3%;">#</th>
                    <th style="padding: 11px;text-align: center;width: 5%;">Hari</th>
                    <th style="padding: 11px;text-align: center;width: 8%;">Jam Kuliah</th>
                    <th style="padding: 11px;width: 15%;">Mata Kuliah</th>
                    <th style="padding: 11px;text-align: center;width: 5%;">SKS</th> 
                    <th style="padding: 11px;text-align: center;width: 10%;">Dosen</th>
                    <th style="padding: 11px;text-align: center;width: 10%;">Ruang</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalSks = 0;
                @endphp
                @foreach ($krsMatkul as $item)
                    <tr>
                        <td style="padding: 8px;text-align:center;">{{ $loop->iteration }}</td>
                        <td style="padding: 8px;text-align:center;">{{ $item->hari ? $configHari[$item->hari] : '' }}</td>
                        <td style="padding: 8px;text-align:center;">{{ date('h:i', strtotime($item->jam_mulai)) }} -
                            {{ date('h:i', strtotime($item->jam_akhir)) }}</td>
                        <td style="padding: 8px;">{{ $item->matkul }}</td>
                        <td style="padding: 8px;text-align:center;">{{ $item->sks_mata_kuliah }}</td>
                        <td style="padding: 8px;text-align:center;">{{ $item->dosen }}</td>
                        <td style="padding: 8px;text-align:center;">{{ $item->ruang }}</td>
                    </tr>
                    @php
                        $totalSks += $item->sks_mata_kuliah;
                    @endphp
                @endforeach
                <tr>
                    <th colspan="7" style="padding: 11px;">Jumlah SKS yang diambil: {{ $totalSks }}</th>
                </tr>
            </tbody>
        </table>
    </div>
    <div style="margin-top: 1rem;font-size: 13px;">
        <div style="width: 32%;float: right;">
            <table aria-hidden="true" style="width: 100%;margin-top: 1rem">
                <tr>
                    <td style="text-align: center;">
                        Mahasiswa/i
                    </td>
                </tr>
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                </tr>
            </table>
        </div>
        @php
            $dateNow = \Carbon\Carbon::now();
            \Carbon\Carbon::setLocale('id');

            $day = $dateNow->format('d');
            $month = $dateNow->translatedFormat('F'); // Nama bulan dalam bahasa Indonesia
            $year = $dateNow->format('Y');

            $dataFormat = "Jakarta, {$day} {$month} {$year}";
        @endphp
        <div style="width: 66%;">
            <table class="table-container" aria-hidden="true" style="padding: 0">
                @foreach (explode(',', $krs->dosenPa) as $key => $dosenPa)
                    @if ($key % 2 == 0)
                        <tr>
                            <td class="table-item">
                                <p>{{ $dataFormat }}</p>
                                <div style="height: 7rem"></div>
                                <p>{{ $dosenPa }}</p>
                            </td>
                        @else
                            <td class="table-item">
                                <p>{{ $dataFormat }}</p>
                                <div style="height: 7rem"></div>
                                <p>{{ $dosenPa }}</p>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </table>
        </div>
    </div>
@endsection
