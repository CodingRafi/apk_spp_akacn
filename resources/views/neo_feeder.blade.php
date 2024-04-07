<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
<script>
    // let username = '{{ encryptString(config('services.neo_feeder.USERNAME')) }}';
    // let password = '{{ encryptString(config('services.neo_feeder.PASSWORD')) }}';
    let kode = "{{ base64_encode(config('services.neo_feeder.KEY_ENCRYPT')) }}";
    let url = "{{ getUrlNeoFeeder() }}/ws/live2.php";
    let username = "{{ config('services.neo_feeder.USERNAME') }}";
    let password = "{{ config('services.neo_feeder.PASSWORD') }}";
    let id_pt = "{{ config('services.neo_feeder.ID_PT') }}";

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
        try {
            let response = await $.ajax(settings);
            return response;
        } catch (error) {
            console.error('Error:', error);
            return null;
        }
    }

    function chunkArray(arr, chunkSize) {
        if (arr.length <= chunkSize) {
            return [arr];
        } else {
            return [arr.slice(0, chunkSize), ...chunkArray(arr.slice(chunkSize), chunkSize)];
        }
    }
</script>
