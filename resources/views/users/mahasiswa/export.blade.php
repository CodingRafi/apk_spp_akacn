<table>
    <thead>
        <tr>
            <th>No</th>
            <th>NIM</th>
            <th>Nama Mahasiswa</th>
            @if ($datas->isNotEmpty())
                @foreach ($datas->first() as $item)
                    <th>Detail {{ $item->nama }}</th>
                    <th>Status {{ $item->nama }}</th>
                @endforeach
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $data)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $data->first()->nim }}</td>
                <td>{{ $data->first()->name }}</td>
                @foreach ($data as $item)
                    <td>
                        Harus dibayar : {{ formatRupiah($item->harus) }} <br>
                        Biaya Tambahan : {{ formatRupiah($item->tambahan) }} <br>
                        Total pembayaran: {{ formatRupiah($item->total_pembayaran) }} <br>
                        Potongan : {{ formatRupiah($item->potongan) }} <br>
                        Sisa : {{ formatRupiah($item->sisa) }}
                    </td>
                    <td>{{ $item->sisa > 0 ? 'Belum Lunas' : 'Lunas' }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
