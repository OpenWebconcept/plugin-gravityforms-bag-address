jQuery(document).ready( function() {

    jQuery('.js-bag-lookup').on('click', function(e) {
		var button = jQuery(this);
		var container = button.closest('.gfield');
		var isValid = true;

		e.preventDefault();

        var isValid = container.find('input').filter(function() {
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
            container.find('.result').html('Vul valide postcode en huisnummer in');
            return;
        }

        jQuery.ajax({
            type : 'post',
            dataType : 'json',
            url : bag_address.ajaxurl,
            data: {
                'action': 'bag_address_lookup',
                'zip': container.find("input[data-name='zip']").val(),
                'homeNumber': container.find("input[data-name='homeNumber']").val(),
                'homeNumberAddition': container.find("input[data-name='homeNumberAddition']").val()
            },
            beforeSend : function ( xhr ) {
                button.val('Zoekende...').prop('disabled', 'disable');
                container.find('.result').html('');
                container.find("input[data-name='address']").attr('value', '');
                container.find("input[data-name='city']").attr('value', '');
            },
            success: function(response) {
                if(true === response.success) {
                    container.find("input[data-name='address']").attr('value', response.data.results.street ? response.data.results.street : '');
					container.find("input[data-name='city']").attr('value', response.data.results.city ? response.data.results.city : '');
                    container.find('.result').html(response.data.message);
                } else {
                    container.find('.result').html(response.data.message);
                }
            },
            complete: function() {
                button.val('Opzoeken').prop('disabled', false);
            }
        })
    });
})
