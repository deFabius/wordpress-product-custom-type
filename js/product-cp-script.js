(function($) {
	$(document).ready(function() {

		$('#select_product_images_button').click(function() {
			var image_frame;
			if (image_frame) {
				image_frame.open();
			}

			image_frame = wp.media({
				title: 'Select Pictures',
				multiple: true,
				library: {
					type: 'image'
				}
			});

			image_frame.on('close', function() {
				var selection = image_frame.state().get('selection');
				if (selection.length === 0) return;
				var thumbnailsContainer = $('#media_thumbnails');
				for (var i in selection.models) {
					var thumbnail = selection.models[i].attributes.sizes.thumbnail || selection.models[i].attributes.sizes.medium || selection.models[i].attributes.sizes.large || selection.models[i].attributes.sizes.full;
					var thumbnailContainer = $('<div class="thumbnail-container">');
					thumbnailsContainer.append(thumbnailContainer);
					var delBtn = $('<button class="image-delete">');
					delBtn.append('<span class="dashicons dashicons-trash">');
					thumbnailContainer.append(delBtn);
					thumbnailContainer.append('<img src="' + thumbnail.url + '">');
					thumbnailContainer.append('<input type="hidden" name="product-images[' + i + ']" value="' + selection.models[i].id + '">');
					delBtn.click(deleteImg);
				}
			});

			image_frame.open();
		});

		$('.image-delete').each(function(index, delImgBtn) {
			$(delImgBtn).click(deleteImg);
		});

		function deleteImg() {
			$(this).parent().remove();
		}
	});
}(jQuery));