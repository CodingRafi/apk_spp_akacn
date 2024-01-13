<table>
    <thead>
        <tr>
            <th>No</th>
            <th>NIM</th>
            <th>Nama Mahasiswa</th>
            @foreach ($semesters as $semester)
                @if ($semester->publish)
                    <th>Detail {{ $semester->nama }}</th>
                    <th>Status {{ $semester->nama }}</th>
                @endif
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
                    @if ($semester->publish)
                        <td>
                            <p>Bayar: {{ formatRupiah($data->pembayaran[$semester->id]['harus']) }} <br> Sudah dibayar:
                                {{ formatRupiah($data->pembayaran[$semester->id]['bayar']) }} <br> Potongan:
                                {{ formatRupiah($data->pembayaran[$semester->id]['potongan']) }} <br> Kekurangan:
                                {{ formatRupiah(max(0, $data->pembayaran[$semester->id]['harus'] - ($data->pembayaran[$semester->id]['bayar'] + $data->pembayaran[$semester->id]['potongan']))) }}
                            </p>
                        </td>
                        <td>{{ $data->pembayaran[$semester->id]['bayar'] + $data->pembayaran[$semester->id]['potongan'] >= $data->pembayaran[$semester->id]['harus'] ? 'LUNAS' : 'BELUM LUNAS' }}
                        </td>
                    @endif
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
