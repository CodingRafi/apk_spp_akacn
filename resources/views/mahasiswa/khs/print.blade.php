@extends('components.template-pdf')

@section('title', 'Kartu Hasil Studi')

@section('content')
    <h2 style="text-align: center;margin-top: 1.3rem;font-size: 1.1rem;">KARTU HASIL STUDI</h2>

    <div style="margin-top: 1rem;">
        <table style="float: left;width: 50%;font-size: 13px;" aria-hidden="true">
            <tr>
                <td style="width: 7rem;padding:0 auto;">NIM</td>
                <td style="padding:0 auto;">:</td>
                <td style="font-weight: bold;padding:0 auto;">{{ $data->nim }}</td>
            </tr>
            <tr>
                <td style="width: 7rem;padding:0 auto;">Nama</td>
                <td style="padding:0 auto;">:</td>
                <td style="font-weight: bold;padding:0 auto;">{{ $data->name }}</td>
            </tr>
            <tr>
                <td style="width: 7rem;padding:0 auto;">Dosen PA</td>
                <td style="padding:0 auto;">:</td>
                <td style="font-weight: bold;padding:0 auto;">{{ $data->dosenPa }}</td>
            </tr>
        </table>
        <table style="float: left;font-size: 13px;" aria-hidden="true">
            <tr>
                <td style="width: 7rem;padding:0 auto;">Semester</td>
                <td style="padding:0 auto;">:</td>
                <td style="font-weight: bold;padding:0 auto;">{{ $tahunSemester->nama }}</td>
            </tr>
            <tr>
                <td style="width: 7rem;padding:0 auto;">Prodi</td>
                <td style="padding:0 auto;">:</td>
                <td style="font-weight: bold;padding:0 auto;">{{ $data->prodi }}</td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    @php
        $jml_sks = 0;
        $bobot_x_sks = 0;
    @endphp

    <div style="margin-top: .8rem;">
        <table aria-label="table-matkul" class="bordered" style="width: 100%;font-size: 13px;">
            <thead>
                <tr>
                    <th style="padding: 11px;text-align:center;">#</th>
                    <th style="padding: 11px;text-align:center;">Kode MK</th>
                    <th style="padding: 11px;">Mata Kuliah</th>
                    <th style="padding: 11px;text-align:center;">SKS</th>
                    <th style="padding: 11px;text-align:center;">Nilai</th>
                    <th style="padding: 11px;text-align:center;">Bobot</th>
                    <th style="padding: 11px;text-align:center;">Bobot X SKS</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($khs as $item)
                    <tr>
                        <td style="text-align:center;">{{ $loop->iteration }}</td>
                        <td style="text-align:center;">{{ $item->kode_mk }}</td>
                        <td>{{ $item->matkul }}</td>
                        @if ($item->kuesioner != null)
                            @php
                                $jml_sks += $item->jml_sks;
                                $bobot_x_sks += $item->bobot_x_sks;
                            @endphp
                            <td style="text-align:center;">{{ $item->jml_sks }}</td>
                            <td style="text-align:center;">{{ $item->mutu }}</td>
                            <td style="text-align:center;">{{ $item->nilai_mutu }}</td>
                            <td style="text-align:center;">{{ $item->bobot_x_sks }}</td>
                        @else
                            <td colspan="4" style="text-align:center;">
                                BELUM ISI KUESIONER
                            </td>
                        @endif
                    </tr>
                @endforeach
                <tr>
                    <th style="padding: 11px;" colspan="3">Total</th>
                    <th style="padding: 11px;" colspan="3">{{ $jml_sks }}</th>
                    <th style="padding: 11px;">{{ number_format($bobot_x_sks, 2) }}</th>
                </tr>
            </tbody>
        </table>
    </div>

    <table aria-hidden="true" style="width: 100%;margin-top: 1rem;font-size: 13px;">
        <tr>
            <td style="width: 45%;">
                Jakarta, {{ date('d F Y') }}
                <br>
                <div style="height: 5rem"></div>
                <br>
                Dewi Rukmana, M.Si
            </td>
            <td>
                <table aria-hidden="true">
                    <tr>
                        <th style="text-align: left;">Indeks Prestasi Semester</th>
                        <td>{{ $bobot_x_sks > 0 || $jml_sks > 0 ? number_format($bobot_x_sks / $jml_sks, 2) : 0 }}</td>
                    </tr>
                    <tr>
                        <th style="text-align: left;">Indeks Prestasi Kumulatif</th>
                        <td>{{ $ipk->bobot_x_sks > 0 || $ipk->jml_sks > 0 ? number_format($ipk->bobot_x_sks / $ipk->jml_sks, 2) : 0 }}
                        </td>
                    </tr>
                    <tr>
                        <th style="text-align: left;">Total SKS Lulus</th>
                        <td>{{ $jml_sks }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
@endsection
