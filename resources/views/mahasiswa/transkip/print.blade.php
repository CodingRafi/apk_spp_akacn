<html>

<head>
    <title>Transkip</title>
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

    <h2 style="text-align: center;margin-top: 1.3rem;font-size: 1.1rem;">REKAPITULASI NILAI AKADEMIK</h2>

    <div style="margin-top: 2rem;">
        <table aria-hidden="true">
            <tr>
                <td style="width: 7rem">NIM</td>
                <td>:</td>
                <td style="font-weight: bold">{{ $data->nim }}</td>
            </tr>
            <tr>
                <td style="width: 7rem">Nama Mahasiswa</td>
                <td>:</td>
                <td style="font-weight: bold">{{ $data->name }}</td>
            </tr>
            <tr>
                <td style="width: 7rem">Angkatan</td>
                <td>:</td>
                <td style="font-weight: bold">{{ $data->angkatan }}</td>
            </tr>
            <tr>
                <td style="width: 7rem">Dosen PA</td>
                <td>:</td>
                <td style="font-weight: bold">{{ $data->dosenPa }}</td>
            </tr>
            <tr>
                <td style="width: 7rem">Prodi</td>
                <td>:</td>
                <td style="font-weight: bold">{{ $data->prodi }}</td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    <div style="margin-top: 2rem;">
        <table aria-label="table-matkul" class="bordered" style="width: 100%;text-align:center">
            <thead>
                <tr>
                    <th style="padding: 8px;">#</th>
                    <th style="padding: 8px;">Kode</th>
                    <th style="padding: 8px;">Mata Kuliah</th>
                    <th style="padding: 8px;">SKS</th>
                    <th style="padding: 8px;">Nilai</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $jml_sks = 0;
                    $bobot_x_sks = 0;
                @endphp
                @foreach ($rekap as $semester => $row)
                    <tr>
                        <td colspan="5" style="text-align: left;">{{ $semester }}</td>
                    </tr>
                    @foreach ($row as $item)
                        <tr>
                            <td style="padding: 5px;">{{ $loop->parent->iteration }}</td>
                            <td style="padding: 5px;">{{ $item->kode_mk }}</td>
                            <td style="padding: 5px;">{{ $item->matkul }}</td>
                            @if ($item->kuesioner != null)
                                @php
                                    $jml_sks += $item->jml_sks;
                                    $bobot_x_sks += $item->bobot_x_sks;
                                @endphp
                                <td style="padding: 5px;">{{ $item->jml_sks }}</td>
                                <td style="padding: 5px;">{{ $item->mutu }}</td>
                            @else
                                <td colspan="2" style="padding: 5px;">BELUM ISI KUESIONER</td>
                            @endif
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>

        <table aria-hidden="true" style="margin-top: 1rem;">
            <tr>
                <th style="text-align: left;">Total SKS Lulus</th>
                <td>:</td>
                <td>{{ $jml_sks }}</td>
            </tr>
            <tr>
                <th style="text-align: left;">Total Mutu</th>
                <td>:</td>
                <td>{{ $bobot_x_sks }}</td>
            </tr>
            <tr>
                <th style="text-align: left;">IPK</th>
                <td>:</td>
                <td>{{ ($bobot_x_sks > 0 || $jml_sks > 0) ? number_format($bobot_x_sks / $jml_sks, 2) : 0 }}
                </td>
            </tr>
        </table>
    </div>

    </table>

</body>

</html>
