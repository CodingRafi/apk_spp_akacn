@extends('mylayouts.main')

@section('container')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <a href="{{ route('users.index', ['role' => request('role')]) }}"><i
                            class="menu-icon tf-icons bx bx-chevron-left"></i></a>
                    <h5 class="text-capitalize mb-0">Potongan</h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="accordionSemester">
                        @foreach ($semesters as $semester)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#semester-{{ $semester->id }}" aria-expanded="false"
                                        aria-controls="semester-{{ $semester->id }}">
                                        {{ $semester->nama }}
                                    </button>
                                </h2>
                                <div id="semester-{{ $semester->id }}" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionSemester">
                                    <div class="accordion-body">
                                        <button type="button" class="btn btn-primary btn-add-potongan"
                                            data-bs-toggle="modal" data-bs-target="#addPotongan"
                                            data-semester="{{ $semester->id }}">
                                            Set Potongan
                                        </button>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Nama</th>
                                                        <th>Nominal</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($potongans[$semester->id] as $potongan)
                                                        <tr>
                                                            <th>{{ $loop->iteration }}</th>
                                                            <th>{{ $potongan->nama }}</th>
                                                            <th>{{ formatRupiah($potongan->nominal) }}</th>
                                                            <th><button class='btn btn-primary mx-2'
                                                                    onclick='detailPotongan({{ $potongan->id }})'>Detail</button>
                                                            </th>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4">
                                                                <div class="alert alert-info text-center">Tidak ada potongan</div>
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addPotongan" tabindex="-1" aria-labelledby="addPotonganLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('users.potongan.store', ['id' => request('id'), 'role' => request('role')]) }}"
                method="post">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="addPotonganLabel">Set Potongan</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="semester_id">
                        <div class="mb-3">
                            <label for="potongan_id" class="form-label">Potongan</label>
                            <select class="form-select select2 select-potongan" name="potongan_id[]" multiple
                                style="width: 100%;">
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    @include('potongan.js')
    <script>
        $('.btn-add-potongan').on('click', function() {
            let semester = $(this).data('semester')
            $('.select-potongan').empty().trigger('change');

            $.ajax({
                type: "GET",
                url: "{{ route('users.potongan.data', ['role' => request('role'), 'id' => request('id'), 'semester_id' => ':id']) }}"
                    .replace(':id', semester),
                success: function(res) {
                    $.each(res.options, function(i, e) {
                        $('.select-potongan').append(
                            `<option value="${e.id}">${e.nama}</option>`)
                    })

                    $('.select-potongan').val(res.data).trigger('change');
                },
                error: function(err) {
                    alert('Maaf telah terjadi kesalahan!');
                }
            });
        })
    </script>
@endpush
