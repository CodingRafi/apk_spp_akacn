<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal Bayar</th>
            <th>Nominal</th>
            <th>Bukti</th>
            <th>Status</th>
            <th>Keterangan Mahasiswa</th>
            <th>Verifikasi</th>
            <th>Keterangan Verifikasi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $data)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ date("d F Y", strtotime($data->tgl_bayar)) }}</td>
            <td>{{ formatRupiah($data->nominal) }}</td>
            <td><a href="{{ asset('storage/' . $data->bukti) }}">Lihat</a></td>
            <td>{{ $data->status }}</td>
            <td>{!! $data->ket_mhs ?? '-' !!}</td>
            <td>{{ ($data->verify ? $data->verify->name : '-') }}</td>
            <td>{!! $data->ket_verify ?? '-' !!}</td>
        </tr>
        @endforeach
    </tbody>
</table>