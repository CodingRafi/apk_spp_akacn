@extends('components.template-pdf')

@section('title', 'Rekap Nilai Akademik')

@section('content')
    <h2 style="text-align: center;margin-top: 1.3rem;font-size: 1.1rem;">REKAPITULASI NILAI AKADEMIK</h2>

    <div style="margin-top: 1rem;">
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
                        <td colspan="4" style="text-align: left;">{{ $semester }}</td>
                    </tr>
                    @foreach ($row as $item)
                        <tr>
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
                <td>{{ number_format($bobot_x_sks, 2) }}</td>
            </tr>
            <tr>
                <th style="text-align: left;">IPK</th>
                <td>:</td>
                <td>{{ $bobot_x_sks > 0 || $jml_sks > 0 ? number_format($bobot_x_sks / $jml_sks, 2) : 0 }}
                </td>
            </tr>
        </table>

        <table aria-hidden="true" style="width: 100%">
            <tr style="text-align: center;">
                <td style="padding-left: 65%">
                    Jakarta, {{ date('d F Y') }}
                    <br>
                    <div style="height: 5rem"></div>
                    <br>
                    Yuyun Lusini, M.Si
                </td>
            </tr>
        </table>
    </div>

    </table>
@overwrite
