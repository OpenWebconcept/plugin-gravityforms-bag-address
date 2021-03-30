jQuery(document).ready( function() {

    jQuery('#bag-lookup').on('click', function(e) {
        var button = jQuery(this);
        var isValid = true;
        e.preventDefault();
        var isValid =jQuery('.gform_wrapper input').filter(function() {
            if (!jQuery(this).data('name')) {
                return;
            }
            return jQuery(this).data('name').match('^(zip|homeNumber)$');
        }).filter(function(index, item) {
            if (!jQuery(item).val().trim() ) {
                return;
            }
			return true;
        });

        if ( 2 > isValid.length ) {
            jQuery('.result').html('Vul valide postcode en huisnummer in');
            return;
        }

        jQuery.ajax({
            type : 'post',
            dataType : 'json',
            url : bag_address.ajaxurl,
            data: {
                'action': 'bag_address_lookup',
                'zip': document.querySelector("input[data-name='zip']").value,
                'homeNumber': document.querySelector("input[data-name='homeNumber']").value,
                'homeNumberAddition': document.querySelector("input[data-name='homeNumberAddition']").value
            },
            beforeSend : function ( xhr ) {
                button.val('Zoekende...').prop('disabled', 'disable');
                jQuery('.result').html('');
                document.querySelector("input[data-name='address']").setAttribute('value', '');
                document.querySelector("input[data-name='city']").setAttribute('value', '');
            },
            success: function(response) {
                if(true === response.success) {
                    document.querySelector("input[data-name='address']").setAttribute('value', response.data.results.street ? response.data.results.street : '');
                    document.querySelector("input[data-name='city']").setAttribute('value', response.data.results.city ? response.data.results.city : '');
                    jQuery('.result').html(response.data.message);
                } else {
                    jQuery('.result').html(response.data.message);
                }
            },
            complete: function() {
                button.val('Opzoeken').prop('disabled', false);
            }
        })
    });
})
