<html>

<head>
    <style>
    

        * {
            font-family: Arial, Helvetica, sans-serif;
        }

        @page {
            margin: 130px 35px 55px;
        }

        header {
            position: fixed;
            top: -6.2rem;
            left: 0px;
            right: 0px;
            height: 5.8rem;
            font-size: 20px !important;
        }

        footer {
            position: fixed;
            bottom: -1.8rem;
            left: 0px;
            right: 0px;
            height: 50px;
            text-align: center;
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
     <header>
        <img src="{{ public_path() . '/image/logo-pdf.png' }}" style="width: 30rem">
    </header>

    <footer>
        <p style="text-align: center;margin-bottom: 0;font-size: 13px;">Komplek Timah, Kelapa Dua Cimanggis Depok - 16951 Telp : (021)
            8710001 Fax : (021)
            8728523 <br> email : akacaraka@yahoo.com.id, Info@akacn.ac.id Home page : www.akacn.ac.id</p>
    </footer>

    @php
        setlocale(LC_TIME, 'id_ID.utf8');
    @endphp
    <main>
        <div>
            <h3 style="text-align: center">TRANSKIP AKADEMIK MAHASISWA</h3>
            <table aria-hidden="true" style="margin:auto;font-size: 13px">
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
        </div>
    
        <div>
            <table aria-label="table-matkul" class="table bordered" style="width: 100%;text-align:center;font-size: 13px">
                <thead>
                    <tr>
                        <th style="padding: 8px;">Kode</th>
                        <th style="padding: 8px;text-align:left;">Mata Kuliah</th>
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
                                <td style="padding: 5px;text-align:left;">{{ $item->matkul }}</td>
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

            <table aria-hidden="true" style="margin-top: 1rem;font-size: 13px">
                <tr>
                    <td>Jakarta, {{ date('d F Y') }}</td>
                </tr>
                <tr>
                    <td style="height: 7rem"></td>
                </tr>
                <tr>
                    <td>Mutiara Dewi Rukmana, M.Si</td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
