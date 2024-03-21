<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>Harus Dibayar</th>
            <th>Total Pembayaran</th>
            <th>Potongan</th>
            <th>Sisa</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $data)
        <tr>
            <td>{{ $data->nama }}</td>
            <td>{{ $data->harus }}</td>
            <td>{{ $data->total_pembayaran }}</td>
            <td>{{ $data->potongan }}</td>
            <td>{{ $data->sisa }}</td>
            <td>{{ $data->sisa > 0 ? 'Belum Lunas' : 'Lunas' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>