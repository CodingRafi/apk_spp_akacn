<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transkip Akademik</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        td, th {
            padding: 8px;
            border: 1px solid #000;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .no-border td {
            border: none;
        }
        .header-table {
            margin-bottom: 40px;
        }
        .header-table td {
            border: none;
        }
        .title {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="title">TRANSKIP AKADEMIK MAHASISWA</div>

<table class="header-table">
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
        <td>{{ $data->tempat_lahir }}, {{ \Carbon\Carbon::parse($data->tgl_lahir)->locale('id')->isoFormat('D MMMM Y') }}</td>
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

<table aria-label="table-matkul" class="table bordered">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Mata Kuliah</th>
            <th>SKS</th>
            <th>Nilai</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rekap as $semester => $row)
            <tr class="no-border">
                <td colspan="4" style="text-align: left;"><strong>{{ $semester }}</strong></td>
            </tr>
            @foreach ($row as $item)
                <tr>
                    <td>{{ $item->kode_mk }}</td>
                    <td>{{ $item->matkul }}</td>
                    @if ($item->kuesioner != null && $item->status == 1)
                        <td>{{ $item->jml_sks }}</td>
                        <td>{{ $item->mutu }}</td>
                    @else
                        <td></td>
                        <td></td>
                    @endif
                </tr>
            @endforeach
            <tr>
                <td colspan="2" style="text-align: center"><strong>Jumlah SKS dan IP {{ $semester }}</strong></td>
                <td>{{ $ipk[$semester]['sks'] }}</td>
                <td>{{ number_format($ipk[$semester]['ipk'], 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
