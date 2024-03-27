@php
    $configHari = config('services.hari');
@endphp

<html>

<head>
    <title>KRS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            margin-top: 11mm;
            margin-left: 13mm;
            margin-right: 13mm;
            margin-bottom: 11mm;
            font-size: 9pt;
        }

        .content tr>td:first-child {
            text-align: justify;
        }

        .content tr td:last-child {
            font-size: 15px;
            line-height: 15px;
        }

        .content td {
            border-bottom: 0px dotted #E4E4E4;
            line-height: 20px;
            font-size: 14px;
            padding: 2px 0;
            display: table-cell;
            vertical-align: text-top;

        }

        .content td:first-child::after {
            content: "";
            display: inline-block;
            width: 100%;
        }

        table.bordered {
            border-collapse: collapse;
        }

        table.bordered th,
        table.bordered td {
            border: 1px solid #a09e9e;
            margin: 0;
            padding: .25rem;
        }

        table td {
            padding: 5px;
        }

        table.no-padding td {
            padding: 0px;
        }
    </style>
</head>

<body>
    <table style="width: 100%" aria-hidden="true">
        <tbody>
            <tr>
                <td style="width: fit-content;" style="width: 5%">
                    <img src="{{ public_path() . '/image/logo.png' }}" width="100">
                </td>
                <td style="text-align: center;">
                    <h1 style="font-size: 1.4rem;font-weight: 600;text-transform: uppercase;">Akademi kimia analis caraka
                        nusantara</h1>
                </td>
            </tr>
        </tbody>
    </table>

    <hr style="border: 1px solid #b7b7b7;margin-top: 10px;">

    <h2 style="text-align: center;margin-top: 1.3rem;font-size: 1.1rem;">KARTU RENCANA STUDI</h2>

    <div style="margin-top: 2rem;">
        <table style="float: left;width: 50%" aria-hidden="true">
            <tr>
                <td style="width: 7rem">NIM</td>
                <td>:</td>
                <td style="font-weight: bold">{{ $krs->nim }}</td>
            </tr>
            <tr>
                <td style="width: 7rem">Nama Mahasiswa</td>
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
                        <td style="padding: 8px;">{{ $configHari[$item->hari] }}</td>
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
                Depok, {{ date('d F Y') }}
                <br>
                Admin
            </td>
            <td></td>
            <td style="text-align: center;">
                Mahasiswa/i
            </td>
        </tr>
        <tr>
            <td>
                <img src="{{ public_path() . '/storage/' . $admin->ttd }}" alt="" style="width: 9rem;height:9rem;">
            </td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table>

</body>

</html>
