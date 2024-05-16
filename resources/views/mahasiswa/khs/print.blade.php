@extends('components.template-pdf')

@section('title', 'Kartu Hasil Studi')

@section('content')
    <h2 style="text-align: center;margin-top: 1.3rem;font-size: 1.1rem;">KARTU HASIL STUDI</h2>

    <div style="margin-top: 2rem;">
        <table style="float: left;width: 50%" aria-hidden="true">
            <tr>
                <td style="width: 7rem">NIM</td>
                <td>:</td>
                <td style="font-weight: bold">{{ $data->nim }}</td>
            </tr>
            <tr>
                <td style="width: 7rem">Nama</td>
                <td>:</td>
                <td style="font-weight: bold">{{ $data->name }}</td>
            </tr>
            <tr>
                <td style="width: 7rem">Dosen PA</td>
                <td>:</td>
                <td style="font-weight: bold">{{ $data->dosenPa }}</td>
            </tr>
        </table>
        <table style="float: left" aria-hidden="true">
            <tr>
                <td style="width: 7rem">Semester</td>
                <td>:</td>
                <td style="font-weight: bold">{{ $tahunSemester->nama }}</td>
            </tr>
            <tr>
                <td style="width: 7rem">Prodi</td>
                <td>:</td>
                <td style="font-weight: bold">{{ $data->prodi }}</td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    @php
        $jml_sks = 0;
        $bobot_x_sks = 0;
    @endphp

    <div style="margin-top: 2rem;">
        <table aria-label="table-matkul" class="bordered" style="width: 100%;text-align:center">
            <thead>
                <tr>
                    <th style="padding: 11px;">#</th>
                    <th style="padding: 11px;">Kode MK</th>
                    <th style="padding: 11px;">Mata Kuliah</th>
                    <th style="padding: 11px;">SKS</th>
                    <th style="padding: 11px;">Nilai</th>
                    <th style="padding: 11px;">Bobot</th>
                    <th style="padding: 11px;">Bobot X SKS</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($khs as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->kode_mk }}</td>
                        <td>{{ $item->matkul }}</td>
                        @if ($item->kuesioner != null)
                            @php
                                $jml_sks += $item->jml_sks;
                                $bobot_x_sks += $item->bobot_x_sks;
                            @endphp
                            <td>{{ $item->jml_sks }}</td>
                            <td>{{ $item->mutu }}</td>
                            <td>{{ $item->nilai_mutu }}</td>
                            <td>{{ $item->bobot_x_sks }}</td>
                        @else
                            <td colspan="4">
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

    <table aria-hidden="true" style="width: 100%;margin-top: 1rem">
        <tr>
            <td style="width: 45%;">
                Depok, {{ date('d F Y') }}
                <br>
                <div style="height: 5rem"></div>
                <br>
                {{ $data->dosenPa }}
            </td>
            <td>
                <table aria-hidden="true" style="font-size: 1.05rem">
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
