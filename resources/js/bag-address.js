jQuery(document).ready(function () {
	var timer;

	function debounce(func) {
		clearTimeout(timer);
		timer = setTimeout(function () {
			func.apply(this);
		}, 1000);
	}

	function inputIsValid() {
		var container = jQuery('.js-bag-lookup').closest('.gfield');

		var validElements = container
			.find('input')
			.filter(function () {
				return String(jQuery(this).data('name')).match(
					'^(zip|homeNumber)$'
				);
			})
			.filter(function (index, item) {
				return Boolean(jQuery(item).val().trim());
			});

		const isEven = validElements.length % 2;
		return validElements.length >= 2 && isEven == 0;
	}

	jQuery('.ginput_container_bag_address input').on('input', function (e) {
		if (!inputIsValid()) {
			return;
		}

		const container = jQuery(e.target).closest(
			'.ginput_container_bag_address'
		);

		debounce(function (e) {
			container.find('.js-bag-lookup').click();
		}, 300);
	});

	jQuery('.js-bag-lookup').on('click', function (e) {
		e.preventDefault();

		var button = jQuery(this);
		var container = button.closest('.gfield');
		var isValid = inputIsValid();

		jQuery('input').on('keyup', function (e) {
			if (!jQuery(this).data('name')) {
				return;
			}

			if (e.key === 'Enter' || e.keyCode === 13) {
				jQuery(this)
					.closest('.gfield')
					.find('input[type="submit"]')
					.click();
			}
		});

		if (isValid === false) {
			container
				.find('.result')
				.html('Vul een valide postcode en huisnummer in');
			return;
		}

		jQuery.ajax({
			type: 'post',
			dataType: 'json',
			url: bag_address.ajaxurl,
			data: {
				action: 'bag_address_lookup',
				zip: container.find("input[data-name='zip']").val(),
				homeNumber: container
					.find("input[data-name='homeNumber']")
					.val(),
				homeNumberAddition: container
					.find("input[data-name='homeNumberAddition']")
					.val(),
			},
			beforeSend: function (xhr) {
				button.val('Zoekende...').prop('disabled', 'disable');
				container.find('.result').html('');
				container.find("input[data-name='address']").val('');
				container.find("input[data-name='city']").val('');
			},
			success: function (response) {
				if (true === response.success) {
					container
						.find("input[data-name='address']")
						.val(
							response.data.results.street
								? response.data.results.street
								: ''
						);
					container
						.find("input[data-name='city']")
						.val(
							response.data.results.city
								? response.data.results.city
								: ''
						);
					container.find('.result').html(response.data.message);
				} else {
					container.find('.result').html(response.data.message);
				}
			},
			complete: function () {
				button.val('Zoek').prop('disabled', false);
			},
		});
	});
});
