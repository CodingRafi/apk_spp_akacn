@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="d-flex align-items-center">
                        <a
                            href="{{ route('kelola-presensi.presensi.index') }}"><i
                                class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                        <h5 class="text-capitalize mb-0">Berita Acara {{ request('tahun_ajaran_id') }}</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-matkul" id="table-matkul">
                            <thead>
                                <tr>
                                    <td>Prodi</td>
                                    <td>Mata Kuliah</td>
                                    <td>Rombel</td>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
@endpush
