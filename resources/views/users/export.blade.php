<table>
    <thead>
        <tr>
            <th>No</th>
            <th>NIM</th>
            <th>Nama Mahasiswa</th>
            @foreach ($semesters as $semester)
                <th>Nominal {{ $semester->nama }}</th>
                <th>Status {{ $semester->nama }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $data)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $data->mahasiswa->nim }}</td>
                <td>{{ $data->name }}</td>
                @foreach ($semesters as $semester)
                    <td>{{ formatRupiah($data->pembayaran[$semester->id]['bayar']) }}</td>
                    <td>{{ $data->pembayaran[$semester->id]['publish'] ? ($data->pembayaran[$semester->id]['bayar'] >= $data->pembayaran[$semester->id]['harus'] ? 'LUNAS' : 'BELUM LUNAS') : '' }}
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
