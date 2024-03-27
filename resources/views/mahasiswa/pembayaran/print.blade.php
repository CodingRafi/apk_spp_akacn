<html>

<head>
    <title>KWITANSI</title>
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
    </style>
</head>

<body>
    <table style="width: 100%" aria-hidden="true">
        <tbody>
            <tr>
                <td style="width: fit-content;" style="width: 5%">
                    <img src="{{ public_path() . '/image/logo.png' }}" width="90">
                </td>
                <td style="text-align: center;">
                    <h1 style="font-size: 1.8rem;font-weight:bold;">BUKTI PEMBAYARAN BIAYA KULIAH</h1>
					<small style="font-size: 1.4rem;font-weight: 300;color:#363636">Akademi kimia analis caraka nusantara</small>
                </td>
            </tr>
        </tbody>
    </table>

    <hr class="mt-05" style="border: 1px solid #b7b7b7;">

    <div class="mt-05">
        <table aria-hidden="true">
            <tr>
                <td style="width: 29.5rem">
                    <table class="table table-bordered" style="font-size: 1.2rem" aria-hidden="true">
                        <tr>
                            <td style="padding-right: 2rem;">Nama</td>
                            <td style="padding-right: .5rem">:</td>
                            <td><strong>{{ $data->mahasiswa->name }}</strong></td>
                        </tr>
                        <tr>
                            <td style="padding-right: 2rem;">NIM</td>
                            <td style="padding-right: .5rem">:</td>
                            <td><strong>{{ $data->mahasiswa->email }}</strong></td>
                        </tr>
                        <tr>
                            <td style="padding-right: 2rem;">Prodi</td>
                            <td style="padding-right: .5rem">:</td>
                            <td><strong>{{ $data->mahasiswa->mahasiswa->prodi->nama }}</strong></td>
                        </tr>
                        <tr>
                            <td style="padding-right: 2rem;">{{ $data->tahun_semester_id ? 'Semester' : 'Pembayaran' }}</td>
                            <td style="padding-right: .5rem">:</td>
                            <td><strong>{{ $data->nama }}</strong></td>
                        </tr>
                        <tr>
                            <td style="padding-right: 2rem;">Nominal</td>
                            <td style="padding-right: .5rem">:</td>
                            <td><strong>{{ formatRupiah($data->nominal) }}</strong></td>
                        </tr>
                        <tr>
                            <td style="padding-right: 2rem;">Tgl. Bayar</td>
                            <td style="padding-right: .5rem">:</td>
                            <td><strong>{{ date("d F Y", strtotime($data->tgl_bayar)) }}</strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <hr class="mt-05" style="border: 1px solid #b7b7b7;">

    <div class="mt-05">
        <p style="font-size: 1.2rem;">Berkas cetak ini merupakan bukti resmi status pembayaran biaya kuliah mahasiswa.</p>
    </div>

    <div class="mt-05">
        <table aria-hidden="true">
            <tr>
                <td style="width: 30rem"></td>
                <td>
                    <img src="{{ public_path() . '/storage/' . $data->verify->ttd }}" alt="" style="width: 9rem;height:9rem;">
                    <br>
                    <p style="font-size: 1rem;text-align: center;">{{ $data->verify->name }}</p>
                </td>
            </tr>
        </table>
    </div>
    <br>

</body>

</html>