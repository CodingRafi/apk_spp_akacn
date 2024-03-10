@php
    $page = isset($page) ? $page : 'form';
    $role = $page == 'form' ? request('role') : getRole()->name;
@endphp
<div id="tab-main">
    <ul class="nav nav-tabs">
        <li class="nav-item" style="white-space: nowrap;">
            <a class="nav-link active a-tab" href="#identitas">Identitas</a>
        </li>
    </ul>
</div>

<div class="tab-content py-4 px-1">
    <div class="tab-pane active" id="identitas" role="tabpanel">
        @include('users.form_user')
        <div class="mb-3">
            <label for="ttd" class="form-label">Tanda Tangan</label>
            <div class="d-flex align-items-center" style="gap: 1rem;">
                <input class="form-control input-pp @error('ttd') is-invalid @enderror" type="file" name="ttd"
                    id="ttd" accept="image/*" />
                @if (Auth::user()->ttd)
                    <a href="{{ asset('storage/' . Auth::user()->ttd) }}" class="btn btn-primary"
                        target="_blank">Lihat</a>
                @endif
            </div>
            @error('ttd')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
</div>

<div class="d-grid gap-2 d-md-flex justify-content-md-start">
    <button class="btn btn-primary" type="submit">Simpan</button>
</div>
