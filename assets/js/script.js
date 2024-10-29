(function ($) {

    console.log(php_vars.token);

    window.axeptioSettings = {
        clientId: php_vars.client_id,
        axeptioApiUrl: php_vars.api_url,
        axeptioPlatformUrl: php_vars.platform_url,
        token: php_vars.token,

        onChange: function (identifier, accept) {

            var initChck = $('#' + identifier);
            if (initChck) {

                if (initChck.hasClass('wpcf7acceptance')) {
                    //get contact form 7
                    var wpcf7Form = initChck.parents('form.wpcf7-form');
                    var $wpcf7Submit = $('input:submit', wpcf7Form);

                    if (!accept) {
                        console.log('disable');
                        $wpcf7Submit.prop('disabled', true);
                    }

                    wpcf7.toggleSubmit(wpcf7Form);
                }
            }
        }
    };
    
})(jQuery);
