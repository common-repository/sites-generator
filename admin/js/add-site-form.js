( function ($) {
	$(document).ready( function () {
		/**
		 * -----------------------
		 * View Model
		 *
		 * @constructor
		 * -----------------------
		 */
		function AddSitesViewModel() {
			let self         = this;
			let sitesCounter = ko.observable( 1 );
			let addButtonEnable = ko.observable(false);

			/**
			 * Page templates
			 *
			 * @type {{templateName: string, templatePath: string}[]}
			 */
			self.availableTemplates = Object.entries( Storage.allSitePagesTemplates ).map(
				(value) => {
					return {
						templateName: value[0],
						templatePath: value[1]
					}
				}
			);
			self.availableTemplates.unshift( { templateName: Storage.defaultTemplateName, templatePath: '' } );

			/**
			 * Add site
			 */
			self.sites      = ko.observableArray(
				[
					new SiteModel( 0, "", "", [] )
				]
			);
			self.addSite    = function() {
				let count = 0;
				self.sites.push( new SiteModel( sitesCounter(), "", "", [] ) );
				count = sitesCounter() + 1;
				sitesCounter( count );
			};
			self.removeSite = function(site) { self.sites.remove( site ); }

			/**
			 Add page
			 */
			self.addPage    = function(site) {
				site.pages.push( new PageModel( site.id, "", "", "", self.availableTemplates[0] ) );
			}
			self.removePage = function(page) {
				let key;
				for (key in self.sites()) {
					if ( self.sites()[key].id === page.siteId) {
						self.sites()[key].pages.remove( page );
					}
				}
			}

			self.addSiteRequest = function () {
				let sitesData = getFormattedData( self.sites() );
				let unique_code = $( '#add-site-form #unique_code' )[0].value;

				$.post(
					Storage.requestUrl,
					{
						action: 'add_sites',
						sitesData,
						unique_code
					},
					function(data) {
						let result = JSON.parse( data );
						if ( false === result.error ) {
							sitesAddedCorrectly();
						} else {
							sitesAddedIncorrectly(result);
						}
					}
				);
			};

			self.checkEnable = function () {
				addButtonEnable(true);
				self.sites().map(
					(site) => {
						if (site.name === '' ||  site.slug === '' || hasSpace(site.slug.trim()) ) {
							addButtonEnable(false);
						}
						site.pages().map( page => {
							if (page.pageName === '' || page.pageSlug === '' || hasSpace(page.pageSlug.trim())) {
								addButtonEnable(false);
							}
						})
					}
				);
				return addButtonEnable;
			}
		}
		ko.applyBindings( new AddSitesViewModel() );

		/**
		 * -----------------------
		 * Models
		 * -----------------------
		 */

		/**
		 *
		 * @param {number} id
		 * @param {string} name
		 * @param {string} slug
		 * @param {[]} initialPages
		 * @constructor
		 */
		function SiteModel(id, name, slug, initialPages) {
			let self = this;

			self.id    = id;
			self.name  = name;
			self.slug  = slug;
			self.pages = ko.observableArray( initialPages );
		}

		/**
		 * Page Model
		 *
		 * @param {number} siteId
		 * @param {string} name
		 * @param {string} slug
		 * @param {string} content
		 * @param {[]} initialTemplate
		 * @constructor
		 */
		function PageModel( siteId, name, slug, content, initialTemplate) {
			let self = this;

			self.siteId       = siteId;
			self.pageName     = name;
			self.pageSlug     = slug;
			self.pageTemplate = ko.observable( initialTemplate );
			self.pageContent  = content;
		}

		/**
		 * -----------------------
		 * Functions
		 * -----------------------
		 */

		/**
		 *
		 * @param {array} siteData
		 * @returns {{pages, name: *, id: *, slug}[]}
		 */
		function getFormattedData ( siteData) {
			let data = siteData.map( item => {
					let pagesData = item.pages();
					let pages = pagesData.map(
						innerItem => {
							let template = innerItem.pageTemplate();
							return {
								name: innerItem.pageName,
								content: innerItem.pageContent,
								slug: innerItem.pageSlug,
								site_id: innerItem.siteId,
								template: template
							}
						}
					);
					return {
						id: item.id,
						name: item.name,
						slug: item.slug,
						pages: pages
					}
				}
			);

			return data;
		}

		function hasSpace (str) {
			return /\s/g.test(str);
		}

		function sitesAddedCorrectly () {
			window.location.replace('wp-admin/admin.php?page=sites-generator');
		}

		function sitesAddedIncorrectly ( data ) {
			let html = '';
			let alertBlock =  $('.sites-generator-add-sites #alert-data')[0];
			data.data.errors_details.map((element) => {
				html += `<p>${element[0].error_message}.</p>`;
			} );
			alertBlock.innerHTML = html;

			$('.alert').addClass('show');
			$('.alert').css('display', 'block');
		}
	});
}) (jQuery);