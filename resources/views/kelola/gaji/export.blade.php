<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Tunjangan</th>
            <th>Total Biaya SKS</th>
            <th>Total Biaya Transport</th>
            <th>Total Gaji</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $data)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $data->name }}</td>
            <td>{{ formatRupiah($data->tunjangan) }}</td>
            <td>{{ formatRupiah($data->total_fee_matkul) }}</td>
            <td>{{ formatRupiah($data->total_fee_transport) }}</td>
            <td>{{ formatRupiah($data->total) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>