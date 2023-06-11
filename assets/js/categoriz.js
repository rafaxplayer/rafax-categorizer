

   jQuery(document).on('ready',function($){

        
        $('.select_cat').on('change',  function (e) {
            var self = $(this);
            var cat_id = self.val();
            var hiperlink = self.parent().parent().find('.rafax-categorizer');
            hiperlink.attr('href',hiperlink.attr('href').replace(/cat=\d+/,'cat='+cat_id));
            
        });

        


    });
        
       

