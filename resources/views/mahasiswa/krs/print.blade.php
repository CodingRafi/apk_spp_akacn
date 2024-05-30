@extends('components.template-pdf')

@section('title', 'Kartu Rencana Studi')

@section('content')
    @php
        $configHari = config('services.hari');
    @endphp

    <h2 style="text-align: center;margin-top: 1.3rem;font-size: 1.1rem;">KARTU RENCANA STUDI</h2>

    <div style="margin-top: 2rem;">
        <table style="float: left;width: 50%" aria-hidden="true">
            <tr>
                <td style="width: 7rem">NIM</td>
                <td>:</td>
                <td style="font-weight: bold">{{ $krs->nim }}</td>
            </tr>
            <tr>
                <td style="width: 7rem">Nama</td>
                <td>:</td>
                <td style="font-weight: bold">{{ $krs->name }}</td>
            </tr>
            <tr>
                <td style="width: 7rem">Dosen PA</td>
                <td>:</td>
                <td style="font-weight: bold">{{ $krs->dosenPa }}</td>
            </tr>
        </table>
        <table style="float: left" aria-hidden="true">
            <tr>
                <td style="width: 7rem">Semester</td>
                <td>:</td>
                <td style="font-weight: bold">{{ $krs->semester }}</td>
            </tr>
            <tr>
                <td style="width: 7rem">Prodi</td>
                <td>:</td>
                <td style="font-weight: bold">{{ $krs->prodi }}</td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    <div style="margin-top: 2rem;">
        <table aria-label="table-matkul" class="bordered" style="width: 100%;text-align:center">
            <thead>
                <tr>
                    <th style="padding: 11px;">#</th>
                    <th style="padding: 11px;">Hari</th>
                    <th style="padding: 11px;">Jam Kuliah</th>
                    <th style="padding: 11px;">Mata Kuliah</th>
                    <th style="padding: 11px;">SKS</th>
                    <th style="padding: 11px;">Dosen</th>
                    <th style="padding: 11px;">Ruang</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalSks = 0;
                @endphp
                @foreach ($krsMatkul as $item)
                    <tr>
                        <td style="padding: 8px;">{{ $loop->iteration }}</td>
                        <td style="padding: 8px;">{{ $item->hari ? $configHari[$item->hari] : '' }}</td>
                        <td style="padding: 8px;">{{ date('h:i', strtotime($item->jam_mulai)) }} -
                            {{ date('h:i', strtotime($item->jam_akhir)) }}</td>
                        <td style="padding: 8px;">{{ $item->matkul }}</td>
                        <td style="padding: 8px;">{{ $item->sks_mata_kuliah }}</td>
                        <td style="padding: 8px;">{{ $item->dosen }}</td>
                        <td style="padding: 8px;">{{ $item->ruang }}</td>
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

    <table aria-hidden="true" style="width: 100%;margin-top: 1rem">
        <tr>
            <td>
                Jakarta, {{ date('d F Y') }}
            </td>
            <td></td>
            <td style="text-align: center;">
                Mahasiswa/i
            </td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table>
@endsection
