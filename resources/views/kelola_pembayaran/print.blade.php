<html>

<head>
    <title>DETAIL PEMBAYARAN</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        body {
            margin-top: 11mm;
            margin-left: 12.5mm;
            margin-right: 12.5mm;
            margin-bottom: 11mm;
            font-size: 9pt;
        }

        .pt-o4 {
            padding-top: .4rem;
        }

        .mt-05 {
            margin-top: .9rem;
        }

        .mt-1 {
            margin-top: 1rem;
        }

        .mt-2 {
            margin-top: 2rem;
        }

        .mt-3 {
            margin-top: 3rem;
        }

        .mt-4 {
            margin-top: 4rem;
        }

        .mt-5 {
            margin-top: 5rem;
        }

        .d-block {
            display: block;
        }

        .text-left {
            text-align: left !important;
        }

        .text-center {
            text-align: center !important;
        }

        .text-right {
            text-align: right !important;
        }

        .text-justify {
            text-align: justify !important;
        }

        .page-break {
            page-break-after: always;
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
            border: 1px solid #000;
            margin: 0;
            padding: .25rem;
        }

        table td {
            padding: 5px;
        }

        table.no-padding td {
            padding: 0px;
        }

        [type="radio"] {
            margin-left: -3px;
        }

        .va-top {
            vertical-align: top;
        }

        .page-break {
            page-break-after: always;
        }

        .table-bordered {
            width: 100%;
            border-collapse: collapse;
        }

        .table-bordered th, .table-bordered td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
    </style>
</head>

<body>
    <table style="width: 100%">
        <tbody>
            <tr>
                <td style="text-align: center;">
                    <h1 style="font-size: 1.8rem;font-weight:bold;">DETAIL PEMBAYARAN</h1>
                </td>
            </tr>
        </tbody>
    </table>

    <hr style="margin: .6rem 0;">

    <table style="font-size: 1rem">
        <tr>
            <td>Nama</td>
            <td>:</td>
            <td>{{ $data->name }}</td>
        </tr>
        <tr>
            <td>NIM</td>
            <td>:</td>
            <td>{{ $data->mahasiswa->nim }}</td>
        </tr>
        <tr>
            <td>Prodi</td>
            <td>:</td>
            <td>{{ $data->mahasiswa->prodi->nama }}</td>
        </tr>
    </table>

    <hr style="margin: .6rem 0;">

    @foreach ($pembayarans as $pembayaran)
        <table style="font-size: 1rem;width: 100%">
            <tr>
                <td>{{ $pembayaran['semester']->nama }} {{ $pembayaran['semester']->publish ? ($pembayaran['total'] >= $pembayaran['semester']->nominal ? '(LUNAS)' : '(BELUM LUNAS)') : '(BELUM DI PUBLISH)' }}</td>
            </tr>
            <tr>
                <td>Bayar: {{ formatRupiah(($pembayaran['semester']->publish ? $pembayaran['semester']->nominal : 0)) }}</td>
            </tr>
            <tr>
                <td>Sudah dibayar: {{ formatRupiah($pembayaran['total']) }}</td>
            </tr>
            <tr>
                <td>Kekurangan: {{ formatRupiah(((($pembayaran['semester']->nominal - $pembayaran['total']) < 0) || !$pembayaran['semester']->publish) ? 0 : $pembayaran['semester']->nominal - $pembayaran['total']) }}</td>
            </tr>
            <tr>
                @if (count($pembayaran['payments']) > 0)
                <table class="table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal Bayar</th>
                            <th>Nominal</th>
                            <th>Bukti</th>
                            <th>Diverifikasi oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pembayaran['payments'] as $payment)
                            <tr>
                                <td>{{ date("d F Y", strtotime($payment->tgl_bayar)) }}</td>
                                <td>{{ formatRupiah($payment->nominal) }}</td>
                                <td><a href="{{ asset('storage/' . $payment->bukti) }}">Lihat</a></td>
                                <td>{{ $payment->nama_verify }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </tr>
        </table>
        <hr style="margin: .6rem 0;">
    @endforeach
</body>

</html>
