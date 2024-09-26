@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="text-capitalize mb-0">{{ str_replace('_', ' ', request('type')) }}</h5>
                    <button class="btn btn-primary" onclick="getData()">Get Data Neo Feeder</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" aria-label="{{ request('type') }}">
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
    @if (Auth::user()->hasRole('admin'))
        <script>
            let thisPage = 'neo_feeder';
        </script>
        @include('neo_feeder.raw')
        @include('neo_feeder.index', [
            'type' => request('type'),
            'urlStoreData' => route('neo-feeder.store'),
        ])
        <script>
            $(document).ready(function() {
                if (typeof table !== "undefined") {
                    table.ajax.reload();
                } else {
                    $('.table thead tr').empty();

                    const format = configData.format;
                    format['active'] = 'active';
                    const uniq = configData.unique;

                    for (let key in format) {
                        if (!uniq.includes(key)) {
                            columns.push({
                                data: format[key],
                                title: capitalize(format[key].replace(/_/g, ' ')),
                            });
                        }
                    }

                    for (const i in columns) {
                        $('.table thead tr').append(`<th>${columns[i].title}</th>`);
                    }
                    fetchDataAndUpdateTable()
                }
            })
        </script>
    @endif
@endpush
