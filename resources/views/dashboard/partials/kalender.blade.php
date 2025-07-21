<div class="col-md-6">
    <div class="card mt-3">
        <div class="card-header pb-0">
            <h5 class="card-title text-capitalize">{{ $kalenderAkademik->nama }}</h5>
        </div>
        <div class="card-body">
            @if ($kalenderAkademik->KalenderAkademikDetail->count() > 0)
                <table class="table table-bordered" aria-hidden="true">
                    <tbody>
                        @foreach ($kalenderAkademik->KalenderAkademikDetail as $row)
                        <tr>
                            <td style="width: 40%;">{{ $row->tgl }}</td>
                            <td style="width: 60%;">{{ $row->ket }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-warning mt-1" role="alert">
                    Maaf, kalender akademik belum tersedia
                </div>
            @endif
        </div>
    </div>
</div>
