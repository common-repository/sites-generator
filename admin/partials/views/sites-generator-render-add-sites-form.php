<?php
function sites_generator_render_add_sites_form() {
	?>
	<div class="sites-generator-add-sites">
		<div class="loader" id="loader">
			<div class="l_main">
				<div class="l_square"><span></span><span></span><span></span></div>
				<div class="l_square"><span></span><span></span><span></span></div>
				<div class="l_square"><span></span><span></span><span></span></div>
				<div class="l_square"><span></span><span></span><span></span></div>
			</div>
		</div>

		<div class="alert alert-warning alert-dismissible fade show" role="alert">
			<div id="alert-data" class="alert-data"></div>
			<button type="button"
                    id="alert-close-btn"
                    class="close"
                    aria-label="<?php _e('Close', SITES_GENERATOR_MENU_SLUG);?>"
            ><span aria-hidden="true">&times;</span>
			</button>
		</div>

		<form id="add-site-form" class="add-site-form container-fluid">
			<h2>Your sites count (<span data-bind="text: sites().length"></span>)</h2>

			<div class="sites" data-bind="foreach: sites">
				<div class="site-item row">
					<div class="site-id col-1"><span data-bind="text: id"></span></div>
					<div class="site-name col-5">
						<p><?php _e( 'Site name', SITES_GENERATOR_MENU_SLUG ); ?></p>
						<input type="text" data-bind="value: name, event: { change: $root.checkEnable() }" />
					</div>
					<div class="site-slug col-4">
						<p><?php _e( 'Site slug', SITES_GENERATOR_MENU_SLUG ); ?></p>
						<input type="text" data-bind="value: slug, event: { change: $root.checkEnable() }" />
					</div>
					<div class="site-remove-btn col-2">
						<a href="#" class="btn btn-danger" data-bind="click: $root.removeSite">
							<span class="glyphicon">&#127;</span><?php _e( 'Remove Site', SITES_GENERATOR_MENU_SLUG ); ?>
						</a></div>
					<div class="pages"  data-bind="template: { name: 'page-template', foreach: pages }"></div>
					<div class="add-page-btn col-12">
						<button
								class="btn btn-primary"
								data-bind="click: $root.addPage, enable: pages().length < 5"
						><?php _e( 'Add page', SITES_GENERATOR_MENU_SLUG ); ?></button>
					</div>
				</div>
				<?php wp_nonce_field( 'wp_ajax_add_sites', 'unique_code' ); ?>
			</div>
			<div class="form-footer">
				<div class="add-site-btn">
					<button class="btn btn-primary" data-bind="click: addSite, enable: sites().length < 5">
						<?php _e( 'Add site to the list', SITES_GENERATOR_MENU_SLUG ); ?>
					</button>
				</div>
				<div class="create-sites-btn">
					<button
							id="add-sites-btn"
							class="btn btn-success"
							data-bind="click: addSiteRequest, enable: $root.checkEnable()"
					><?php _e( 'Add all sites', SITES_GENERATOR_MENU_SLUG ); ?></button>
				</div>
			</div>
		</form>
	</div>

	<script type="text/html" id="page-template">
		<div class="page-item">
			<div class="page-data row">
				<div class="page-name col-12 col-md-6">
					<p><?php _e( 'Name', SITES_GENERATOR_MENU_SLUG ); ?></p>
					<input data-bind="value: pageName, event: { change: $root.checkEnable() }" />
				</div>
				<div class="page-slug col-12 col-md-6">
					<p><?php _e( 'Slug', SITES_GENERATOR_MENU_SLUG ); ?></p>
					<input data-bind="value: pageSlug, event: { change: $root.checkEnable() }" />
				</div>
				<div class="page-content col-12">
					<p><?php _e( 'Content', SITES_GENERATOR_MENU_SLUG ); ?></p>
					<textarea id="post-content" rows="5" type="text" data-bind="value: pageContent" ></textarea>
				</div>
				<div class="page-template-name col-12 col-md-8">
					<p><?php _e( 'Template name', SITES_GENERATOR_MENU_SLUG ); ?></p>
					<select data-bind="options: $root.availableTemplates,
									   optionsValue: 'templatePath',
									   optionsText:  'templateName',
									   value: pageTemplate"
					></select>
				</div>
				<div class="page-remove-btn col-12 col-md-4">
					<a href="#"
					   class="btn text-danger"
					   data-bind="click: function () {$root.removePage($data)}"
					><?php _e( 'Remove Page', SITES_GENERATOR_MENU_SLUG ); ?></a>
				</div>
			</div>
		</div>
	</script>
	<?php
}
