
(function ($) {

    $(document).on('ready', function () {


        $('.select_cat').on('change', function (e) {
            var self = $(this);
            var cat_id = self.val();
            var hiperlink = self.parent().parent().find('.rafax-categorizer');
            hiperlink.attr('href', hiperlink.attr('href').replace(/cat=\d+/, 'cat=' + cat_id));

        });

        /*  $('#cat_create').on('click', function () {
             var name_cat = $('#cat_name').val();
 
             if (!name_cat.length) {
                 alert('No has indicado un nombre para la categoria');
                 return;
             }
 
             data = {
                 action: 'create_category',
                 nonce: AjaxParams.nonce,
                 cat_name: name_cat
             }
             $.post(AjaxParams.adminAjaxUrl, data, function (response) {
                 var message = response > 0 ? "Categoria creada con exito!" : "Ocurrio un error al crear categoria";
                 var type = response > 0 ? "success" : "error";
                 var format_message = '<div class="notice notice-{type} is-dismissible"><p>{message}</p></div>';
                 document.location.reload();
                             
             });
         }) */


    });
})(jQuery)


