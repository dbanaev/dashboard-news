$(document).ready(function() {
    $('.chosen-select').chosen();


    $('.ckeditor').each(function () {

        CKEDITOR.timestamp='ABCF';

        var editorId = this.id;
        CKEDITOR.replace(editorId);

        var editor = CKEDITOR.instances[editorId];

        editor.on('blur', function() {

            $('#' + editorId).val(editor.getData());
        });
    });

    $('.gen-short').on('click', function () {

        var fieldId;
        var txtFullId;
        var txtShortId;

        if ($(this).attr('data-fieldid')) {
            fieldId = $(this).data('fieldid');

            txtFullId = 'news-' + fieldId + '-txt_full';
            txtShortId = 'news-' + fieldId + '-txt_short';
        } else {
            txtFullId = 'news-txt_full';
            txtShortId = 'news-txt_short';
        }


        var editor = CKEDITOR.instances[txtFullId];
        var txtFull = editor.getData();

        txtFull = txtFull.replace(/<\/?[^>]+>/gi, '');
        txtFull = txtFull.replace(/&nbsp;/g, ' ');

        var tmp = txtFull.indexOf(' ', 250);
        var txtShort = txtFull.slice(0, tmp) + '...';

        $('#' + txtShortId).val(txtShort);
    });


    $('a.preview').on('click', function(e) {
        e.preventDefault();

        var key = $(this).data('previewkey');

        $.ajax({
            url: '/admin/news/preview',
            type: 'post',
            data: {id: key},
            success: function(data) {
               if (data) {
                   $('#preview' + key).slideToggle();

                   $('#preview' + key + ' td').html(data);

                   CKEDITOR.replace( 'txt_full_' + key );
               }
            }
        });
    });
});