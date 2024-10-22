<html>

<head>
    <style>
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

        * {
            font-family: Arial, Helvetica, sans-serif;
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
    <title>@yield('title')</title>
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

    <main>
        @yield('content')
    </main>
</body>

</html>
