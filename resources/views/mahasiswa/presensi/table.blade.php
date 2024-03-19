@php
    $mhs_id = isset($mhs_id) ? $mhs_id : null;
@endphp

<div class="row">
    <div class="col-md-4">
        <select name="tahun_semester_id" id="tahun_semester_id" class="form-control mb-3">
            <option value="">Pilih Semester</option>
            @foreach ($tahun_semester as $semester)
                <option value="{{ $semester->id }}">{{ $semester->nama }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th>Matkul</th>
                <td>1</td>
                <td>2</td>
                <td>3</td>
                <td>4</td>
                <td>5</td>
                <td>6</td>
                <td>7</td>
                <td>UTS</td>
                <td>8</td>
                <td>9</td>
                <td>10</td>
                <td>11</td>
                <td>12</td>
                <td>13</td>
                <td>14</td>
                <td>UAS</td>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<script>
    function generate_table(data) {
        let table = '';

        $.each(data, (i, e) => {
            table +=
                `<tr>
                    <td>${e.matkul}</td>`;

            e.presensi.forEach(el => {
                table += `<td>${el.jadwal_id ? (el.status ?? '-') : ''}</td>`;
            })

            table += `</tr>`;
        })

        $('.table tbody').html(table);
    }

    function get_presensi() {
        $('.table tbody').empty();
        $('.table tbody').append(`<tr>
                                        <td colspan="17" class="text-center py-4">
                                            <div class="spinner-border" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </td>
                                    </tr>`);
        $.ajax({
            url: "{{ route('presensi.data') }}",
            dataType: "json",
            data: {
                tahun_semester_id: $('#tahun_semester_id').val(),
                mhs_id: '{{ $mhs_id }}'
            },
            success: function(res) {
                generate_table(res.data)
            },
            error: function(err) {
                console.log('Gagal get presensi');
            }
        })
    }

    $('#tahun_semester_id').on('change', get_presensi)
</script>
