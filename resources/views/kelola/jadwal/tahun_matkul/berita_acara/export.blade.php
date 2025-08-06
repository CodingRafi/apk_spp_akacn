<table>
    <thead>
        <tr>
            <th>Timestamp</th>
            <th>Bahwa pada hari ini</th>
            <th>Tanggal</th>
            <th>Diruang</th>
            <th>Telah diselenggarakan Ujian Mata Kuliah</th>
            <th>Tingkat/Semester</th>
            <th>Nama Dosen</th>
            <th>Jumlah Mahasiswa Yang Seharusnya Hadir</th>
            <th>Jumlah Mahasiswa Yang Tidak Hadir</th>
            <th>Dengan rincian sebagai berikut:</th>
            <th>Sifat ujian adalah</th>
            <th>Ujian berlangsung dengan tidak terjadi/terjadi* peristiwa. Uraikan kejadian</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $jadwal->presensi_mulai }}</td>
            <td>{{ getHari($jadwal->presensi_mulai) }}</td>
            <td>{{ $jadwal->tgl }}</td>
            <td>{{ $jadwal->ruang }}</td>
            <td>{{ $jadwal->matkul }}</td>
            <td>{{ $jadwal->tingkat }}</td>
            <td>{{ $jadwal->dosen }}</td>
            <td>{{ $totalHarusHadir }}</td>
            <td>{{ $totalTidakHadir }}</td>
            <td>{{ $jadwal->ket }}</td>
            <td>{{ $jadwal->sifat_ujian }}</td>
            <td>{{ $jadwal->status_ujian }}</td>
        </tr>
    </tbody>
</table>