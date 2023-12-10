<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="/assets/"
    data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>SPP | AKACN</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('/img/favicon_io/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />

    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/south-street/jquery-ui.css"
        rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="http://keith-wood.name/css/jquery.signature.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.css">

    {{-- Tambahan Css --}}
    <link rel="stylesheet" href="{{ asset('css/fstdropdown.css') }}">
    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>

    <script src="{{ asset('assets/js/config.js') }}"></script>
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">


    @stack('css')

    <style>
        .swal2-container {
            z-index: 9999 !important;
        }
    </style>

    <style>
        .menu-inner {
            overflow-x: hidden !important;
            overflow-y: auto !important;
        }

        /* Sembunyikan scrollbar dari Chrome, Safari dan Opera */
        .menu-inner::-webkit-scrollbar {
            display: none;
        }

        /* Sembunyikan scrollbar untuk IE, Edge dan Firefox */
        .menu-inner {
            -ms-overflow-style: none;
            /* IE dan Edge */
            scrollbar-width: none;
            /* Firefox */
        }

        body {
            overflow-x: none;
        }

        #summernote,
        .note-editor .note-dropzone {
            z-index: 9999 !important;
        }

        @media(max-width:850px) {
            .text-powered {
                text-align: center !important;
                margin-top: .5rem;
            }

            .div-col-footer {
                justify-content: center !important;
            }
        }
    </style>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('mypartials.aside')
            <!-- Layout container -->
            <div class="layout-page">
                {{-- Navbar --}}
                @include('mypartials.navbar')
                {{-- End Navbar --}}

                <!-- Content wrapper -->
                @yield('container')
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>

    <form action="" class="form-delete" method="POST">
        @csrf
        @method('delete')
    </form>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ asset('/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ asset('/assets/vendor/js/menu.js') }}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('/assets/js/main.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('/assets/js/dashboards-analytics.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>

    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script type="text/javascript" src="http://keith-wood.name/js/jquery.signature.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css"
        integrity="sha512-O03ntXoVqaGUTAeAmvQ2YSzkCvclZEcPQu1eqloPaHfJ5RuNGiS4l+3duaidD801P50J28EHyonCV06CUlTSag=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"
        integrity="sha512-Zq9o+E00xhhR/7vJ49mxFNJ0KQw1E1TMWkPTxrWcnpfEFDEXgUiwJHIKit93EW/XxE31HSI5GEOW06G6BF1AtA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
    <script src="{{ asset('js/fstdropdown.js') }}"></script>
    <script src="//cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('tinymce/tinymce.min.js') }}"></script>
    <script>
        setFstDropdown();
    </script>
    <script>
        function showAlert(message, type) {
            if (type == 'success') {
                iziToast.success({
                    title: 'Success',
                    message: message,
                    position: 'topRight'
                });
            } else {
                iziToast.error({
                    title: 'Failed',
                    message: message,
                    position: 'topRight'
                });
            }
        }
        @if (session()->has('success')) showAlert("{{ session('success') }}", 'success')
        @elseif(session()->has('error')) showAlert("{{ session('error') }}", 'error')
        @endif
    </script>

    <script>
        function deleteData(url){
        Swal.fire({
            title: 'Apakah anda yakin ingin hapus data ini?',
            text: "Data yang terhapus tidak dapat dikembalikan",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'

        }).then((result) => {
            if (result.isConfirmed) {
                $('.form-delete').attr('action', url).submit();
            } 
        })
    }
    </script>
    <script>
        const upload_file = (blobInfo, progress) => new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.withCredentials = false;
        xhr.open('POST', '{{ route("upload_file") }}');

        xhr.upload.onprogress = (e) => {
            progress(e.loaded / e.total * 100);
        };

        xhr.onload = () => {
            if (xhr.status === 403) {
                reject({ message: 'HTTP Error: ' + xhr.status, remove: true });
                return;
            }

            if (xhr.status < 200 || xhr.status >= 300) {
                reject('HTTP Error: ' + xhr.status);
                return;
            }

            const json = JSON.parse(xhr.responseText);

            if (!json || typeof json.location != 'string') {
                reject('Invalid JSON: ' + xhr.responseText);
                return;
            }

            resolve(json.location);
        };

        xhr.onerror = () => {
            reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
        };
        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'))

        const formData = new FormData();
        formData.append('file', blobInfo.blob(), blobInfo.filename());

        xhr.send(formData);
    });

   tinymce.init({
     selector: 'textarea',
     plugins: ` advlist anchor autosave image link lists media searchreplace table template visualblocks wordcount`,
     toolbar: 'undo redo | styles | bold italic underline strikethrough | align | table link image media pageembed | bullist numlist outdent indent | spellcheckdialog a11ycheck code',
     a11ychecker_level: 'aaa',
     convert_urls: false,

     style_formats: [
       {title: 'Heading 1', block: 'h1'},
       {title: 'Heading 2', block: 'h2'},
       {title: 'Paragraph', block: 'p'},
       {title: 'Blockquote', block: 'blockquote'},
       {title: 'Image formats'},
       {title: 'Medium', selector: 'img', classes: 'medium'},
     ],
     object_resizing: false,
     valid_classes: {
       'img': 'medium',
       'div': 'related-content'
     },
     image_caption: true,
     images_upload_url: '{{ route("upload_file") }}',
     images_upload_handler: upload_file,
     templates: [
       {
         title: 'Related content',
         description: 'This template inserts a related content block',
         content: '<div class="related-content"><h3>Related content</h3><p><strong>{$rel_lede}</strong> {$rel_body}</p></div>'
       }
     ],
     template_replace_values: {
       rel_lede: 'Lorem ipsum',
       rel_body: 'dolor sit amet...',
     },
     template_preview_replace_values: {
       rel_lede: 'Lorem ipsum',
       rel_body: 'dolor sit amet...',
     },
     noneditable_class: 'related-content',
     content_langs: [
       {title: 'English (US)', code: 'en_US'},
       {title: 'French', code: 'fr'}
     ],
     branding: false,
     height: 540,
     promotion: false,
     content_style: `
       img {
         height: auto;
         margin: auto;
         padding: 10px;
         display: block;
       }
       img.medium {
         max-width: 25%;
       }
     `
     
   });
    </script>

    @stack('js')

</body>

</html>