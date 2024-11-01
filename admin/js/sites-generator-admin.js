import '../css/style.scss';

(function( $ ) {
	'use strict';

	$( "#tabs" ).tabs();
	$('.alert').removeClass('show');
	$('.alert').css('display', 'none');
	$('#alert-close-btn').click( () => {
		$('.alert').removeClass('show');
		$('.alert').css('display', 'none');
	});

	$('.quick-edit-btn').click( function () {
		const QUICK_EDIT_ID_PREFIX = 'quickedit-';
		let element = $(this)[0].parentNode.parentNode.parentNode.parentNode;
		let elementId = $(this)[0].parentElement.parentElement.parentElement.parentElement.id;
		let quickEditForm = $('#' + QUICK_EDIT_ID_PREFIX + elementId)[0];
		let elementTitle = $(element).find('.title.column-title .site-title')[0].innerHTML;
		let elementSlug = $(element).find('.slug.column-slug')[0].innerHTML;

		if ( undefined === quickEditForm ) {
			let quickEditFormTpl = `
				<tr id="${QUICK_EDIT_ID_PREFIX + elementId}">
					<form>
						<td></td>
						<td>
							<label for="site_name">Name</label>
							<input type="text" 
								   class="site-name"
								   name="site_name" 
								   value="${elementTitle}" 
							/>
						</td>
						<td>
							<label for="site_slug">Slug</label>
							<input type="text"
								   class="site-slug"
								   name="site_slug" 
								   value="${elementSlug}" 
							/>
						</td>
						<td><a href="#" class="quick-edit save-btn">Save</a> <a href="#" class="quick-edit cancel-btn">Cancel</a></td>
						<td></td>
						<td></td>
					</form>
				</tr>
			`;
			$(quickEditFormTpl).insertBefore(element);
			$(element).hide();

			let cancelBtn = $('#' + QUICK_EDIT_ID_PREFIX + elementId +' .quick-edit.cancel-btn');
			$(cancelBtn).click(function () {
				showTableRow(
					element,
					QUICK_EDIT_ID_PREFIX,
					elementId
				);
				// $('#' + QUICK_EDIT_ID_PREFIX + elementId).remove();
				// $(element).show();
			});

			let saveBtn = $('#' + QUICK_EDIT_ID_PREFIX + elementId +' .quick-edit.save-btn');
			$(saveBtn).click(function () {
				let siteData = {
					'siteId': elementId.replace(/[^\d]/g, ''),
					'siteName': $('#' + QUICK_EDIT_ID_PREFIX + elementId + ' .site-name')[0].value,
					'siteSlug': $('#' + QUICK_EDIT_ID_PREFIX + elementId + ' .site-slug')[0].value,
				};

				$.post(
					Storage.requestUrl,
					{
						action: 'quickedit_site',
						siteData,
					},
					function( data ) {
						let result = JSON.parse( data );
						console.log("REQ", result)
						$( `#site-${result.data.data.blog_id} .site-title` )[0].innerHTML = result.data.data.blogname;
						$( `#site-${result.data.data.blog_id} .url` )[0].innerHTML = result.data.url;
						$( `#site-${result.data.data.blog_id} .slug` )[0].innerHTML = result.data.slug;
						if ( false === result.error ) {
							showTableRow(
								element,
								QUICK_EDIT_ID_PREFIX,
								elementId
							);
						}
					}
				);
			});
		}
	});

	function showTableRow (element, editPrefix, elementId) {
		$('#' + editPrefix + elementId).remove();
		$(element).show();
	}

})( jQuery );