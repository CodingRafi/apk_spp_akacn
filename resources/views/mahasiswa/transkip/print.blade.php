<html>

<head>
    <style>
        @page {
            margin: 285px 35px 30px;
        }

        header {
            position: fixed;
            top: -17.4rem;
            left: 0px;
            right: 0px;
            height: 7rem;
            font-size: 13px !important;
        }

        * {
            font-family: Arial, Helvetica, sans-serif;
        }

        .content tr>td:first-child {
            text-align: justify;
        }

        .content tr td:last-child {
            font-size: 10px;
            line-height: 15px;
        }

        .content td {
            border-bottom: 0px dotted #E4E4E4;
            line-height: 20px;
            font-size: 10px;
            padding: 2px 0;
            display: table-cell;
            vertical-align: text-top;

        }

        .content td:first-child::after {
            content: "";
            display: inline-block;
            width: 100%;
        }

        .table.bordered {
            border-collapse: collapse;
        }

        .table.bordered th,
        .table.bordered td {
            border: 1px solid #a09e9e;
            margin: 0;
            padding: .25rem;
        }

        .table td {
            padding: 5px;
        }

        .table.no-padding td {
            padding: 0px;
        }
    </style>
    <title>Transkip Akademik</title>
</head>

<body>
    @php
        setlocale(LC_TIME, 'id_ID.utf8');
    @endphp
    <header>
        <h3 style="text-align: center">TRANSKIP AKADEMIK MAHASISWA</h3>
        <table aria-hidden="true" style="margin:auto">
            <tr>
                <td>Nomor Seri Transkip Akademik</td>
                <td>:</td>
                <td></td>
            </tr>
            <tr>
                <td>Nomor Ijazah Nasional</td>
                <td>:</td>
                <td></td>
            </tr>
            <tr>
                <td>Program Pendidikan</td>
                <td>:</td>
                <td>{{ $data->jenjang }}</td>
            </tr>
            <tr>
                <td>Program Studi</td>
                <td>:</td>
                <td>{{ $data->prodi }}</td>
            </tr>
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td>{{ $data->name }}</td>
            </tr>
            <tr>
                <td>Tempat dan Tanggal Lahir</td>
                <td>:</td>
                <td>{{ $data->tempat_lahir }},
                    {{ \Carbon\Carbon::parse($data->tgl_lahir)->locale('id')->isoFormat('D MMMM Y') }}</td>
            </tr>
            <tr>
                <td>Nomor Induk Mahasiswa (NIM)</td>
                <td>:</td>
                <td>{{ $data->nim }}</td>
            </tr>
            <tr>
                <td>Tanggal, Bulan, dan Tahun Kelulusan</td>
                <td>:</td>
                <td></td>
            </tr>
            <tr>
                <td>Jumlah SKS</td>
                <td>:</td>
                <td>{{ $totalSKS }}</td>
            </tr>
            <tr>
                <td>Index Prestasi Komulatif</td>
                <td>:</td>
                <td>{{ number_format(end($ipk)['ipk'], 2) }}</td>
            </tr>
            <tr>
                <td>Predikat Kelulusan</td>
                <td>:</td>
                <td></td>
            </tr>
        </table>
    </header>

    <main>
        <div>
            <table aria-label="table-matkul" class="table bordered" style="width: 100%;text-align:center">
                <thead>
                    <tr>
                        <th style="padding: 8px;">Kode</th>
                        <th style="padding: 8px;">Mata Kuliah</th>
                        <th style="padding: 8px;">SKS</th>
                        <th style="padding: 8px;">Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rekap as $semester => $row)
                        <tr>
                            <td colspan="4" style="text-align: left;">{{ $semester }}</td>
                        </tr>
                        @foreach ($row as $item)
                            <tr>
                                <td style="padding: 5px;">{{ $item->kode_mk }}</td>
                                <td style="padding: 5px;">{{ $item->matkul }}</td>
                                @if ($item->kuesioner != null && $item->status == 1)
                                    <td style="padding: 5px;">{{ $item->jml_sks }}</td>
                                    <td style="padding: 5px;">{{ $item->mutu }}</td>
                                @else
                                    <td style="padding: 5px;"></td>
                                    <td style="padding: 5px;"></td>
                                @endif
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2" style="text-align: center">Jumlah SKS dan IP {{ $semester }}</td>
                            <td>{{ $ipk[$semester]['sks'] }}</td>
                            <td>{{ number_format($ipk[$semester]['ipk'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
    </main>
</body>

</html>
