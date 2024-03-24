<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
<script>
    // let username = '{{ encryptString(config('services.neo_feeder.USERNAME')) }}';
    // let password = '{{ encryptString(config('services.neo_feeder.PASSWORD')) }}';
    let kode = "{{ base64_encode(config('services.neo_feeder.KEY_ENCRYPT')) }}";
    let url = "{{ $url->value }}/ws/live2.php";
    let username = "{{ config('services.neo_feeder.USERNAME') }}";
    let password = "{{ config('services.neo_feeder.PASSWORD') }}";

    async function getToken() {
        if (!url) {
            showAlert('Url tidak ditemukan', 'error');
            return false;
        }

        var settings = {
            "url": url,
            "method": "POST",
            "timeout": 0,
            "headers": {
                "Content-Type": "application/json"
            },
            "data": JSON.stringify({
                "act": "GetToken",
                "username": username,
                "password": password
            }),
        };

        return $.ajax(settings)
    }

    async function getData(raw) {
        if (!url) {
            showAlert('Url tidak ditemukan', 'error');
        }

        let token = await getToken()

        raw.token = token.data.token;

        var settings = {
            "url": url,
            "method": "POST",
            "timeout": 0,
            "headers": {
                "Content-Type": "application/json"
            },
            "data": JSON.stringify(raw),
        };

        $.ajax(settings).done(function(response) {
            storeData(changeFormatData(response.data))
        });
    }

    function changeFormatData(data) {
        let format = configNeoFeeder.{{ request('type') }}.format;
        let newData = [];

        $.each(data, function(key, value) {
            let newFormat = {};

            for (const key in value) {
                newFormat[format[key]] = value[key];
            }

            newData.push(newFormat)
        })

        return newData;
    }

    function storeData(data) {
        $.ajax({
            url: '{{ route('neo-feeder.store') }}',
            type: 'POST',
            data: {
                tbl: configNeoFeeder.{{ request('type') }}.tbl,
                data
            },
            dataType: 'json',
            success: function(res) {
                showAlert(res.message, 'success')
            },
            error: function(err) {
                console.log(err)
            }
        })
    }
</script>
