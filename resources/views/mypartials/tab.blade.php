<script>
    $('.f1 fieldset:first').fadeIn('slow');
    $('.f1 input[type="text"], .f1 input[type="password"], .f1 textarea').on('focus', function() {
        $(this).removeClass('input-error');
    });
    // step selanjutnya (ketika klik tombol selanjutnya)
    $('.f1 .btn-next').on('click', function() {
        var parent_fieldset = $(this).parents('fieldset');
        var next_step = $(this).hasClass('btn-selanjutnya-1') ? ($('{{ $form }}').attr(
            'data-status') == 'true' ? true : false) : true;
        var current_active_step = $(this).parents('.f1').find('.f1-step.active');

        if (next_step) {
            parent_fieldset.fadeOut(400, function() {
                // change icons
                current_active_step.removeClass('active').addClass('activated').next()
                    .addClass('active');
                $(this).next().fadeIn();
                $('.label-step').html((parseInt($(this).attr('data-id')) + 1))
            });
        }
    });

    // step sbelumnya (ketika klik tombol sebelumnya)
    $('.f1 .btn-previous').on('click', function() {
        // navigation steps / progress steps
        var current_active_step = $(this).parents('.f1').find('.f1-step.active');

        $(this).parents('fieldset').fadeOut(400, function() {
            current_active_step.removeClass('active').prev().removeClass('activated')
                .addClass('active');
            $(this).prev().fadeIn();
            $('.label-step').html((parseInt($(this).attr('data-id')) - 1))
        });
    });
</script>
