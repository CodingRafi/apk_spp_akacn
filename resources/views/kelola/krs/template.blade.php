<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>NIM</th>
            <th>Angka</th>
            <th>Huruf</th>
            <th>Aktivitas Partisipatif</th>
            <th>Hasil Proyek</th>
            <th>Kognitif/Pengetahuan Quiz</th>
            <th>Kognitif/Pengetahuan Tugas</th>
            <th>Kognitif/Pengetahuan Ujian Tengah Semester</th>
            <th>Kognitif/Pengetahuan Ujian Akhir Semester</th>
            <th>Publish</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $data)
        <tr>
            <td>{{ $data->name }}</td>
            <td>{{ $data->login_key }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        @endforeach
    </tbody>
</table>