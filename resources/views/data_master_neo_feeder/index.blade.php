@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="text-capitalize mb-0">{{ request('type') }}</h5>
                    <button class="btn btn-primary" onclick="get()">Get Data Neo Feeder</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        let table;
        $(document).ready(function() {
            table = $('.table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route('neo-feeder.data', ['type' => request('type')]) }}',
                },
                columns: [{
                    "data": "nama"
                }],
                pageLength: 25,
                responsive: true,
            });
        });

        function get() {
            $.LoadingOverlay("show");
            $.ajax({
                url: '{{ route('neo-feeder.get', ['type' => request('type')]) }}',
                success: function(res) {
                    showAlert(res.output, 'success')
                    $.LoadingOverlay("hide");
                    table.ajax.reload();
                },
                error: function(err) {
                    $.LoadingOverlay("hide");
                    showAlert(err.responseJSON.output, 'error')
                }
            })
        }
    </script>
@endpush
