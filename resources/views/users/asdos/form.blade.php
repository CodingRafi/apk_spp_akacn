<div id="tab-main">
    <ul class="nav nav-tabs">
        <li class="nav-item" style="white-space: nowrap;">
            <a class="nav-link active a-tab" href="#identitas">Identitas</a>
        </li>
        <li class="nav-item" style="white-space: nowrap;">
            <a class="nav-link a-tab" href="#lainnya">Lainnya</a>
        </li>
    </ul>
</div>

<div class="tab-content py-4 px-1">
    <div class="tab-pane active" id="identitas" role="tabpanel">
        <div class="row">
            <div class="col-md-6">
                @include('users.form_user')
                <div class="mb-3">
                    <label for="login_key" class="form-label">NIDN</label>
                    <input class="form-control @error('login_key') is-invalid @enderror" type="text"
                        value="{{ isset($data) ? $data->login_key : old('login_key') }}" id="login_key"
                        placeholder="NIDN" name="login_key" />
                    @error('login_key')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                @include('users.default.identitas')
            </div>
            <div class="col-md-6">
                @include('users.default.alamat')
                @include('users.default.telp')
            </div>
        </div>
    </div>

    <div class="tab-pane" id="lainnya" role="tabpanel">
        @include('users.default.ngajar')
    </div>
</div>

<div class="d-grid gap-2 d-md-flex justify-content-md-start">
    <button class="btn btn-primary" type="submit">Simpan</button>
</div>
