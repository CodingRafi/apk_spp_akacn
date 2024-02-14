<div id="tab-main">
    <ul class="nav nav-tabs">
        <li class="nav-item" style="white-space: nowrap;">
            <a class="nav-link active a-tab" href="#identitas">Identitas</a>
        </li>
    </ul>
</div>

<div class="tab-content py-4 px-1">
    <div class="tab-pane active" id="identitas" role="tabpanel">
        <div class="row">
            <div class="col-md-6">
                @include('users.form_user')
                @include('users.default.identitas')
            </div>
            <div class="col-md-6">
                @include('users.default.alamat')
                @include('users.default.telp')
            </div>
        </div>
    </div>
</div>

<div class="d-grid gap-2 d-md-flex justify-content-md-start">
    <button class="btn btn-primary" type="submit">Simpan</button>
</div>
