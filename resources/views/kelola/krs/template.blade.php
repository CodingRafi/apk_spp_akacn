<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>NIM</th>
            <th>Presensi</th>
            <th>Aktivitas Partisipatif</th>
            <th>Hasil Proyek</th>
            <th>Quizz</th>
            <th>Tugas</th>
            <th>UTS</th>
            <th>UAS</th>
            <th>Nilai Akhir</th>
            <th>Mutu</th>
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
            <td></td>
        </tr>
        @endforeach
    </tbody>
</table>