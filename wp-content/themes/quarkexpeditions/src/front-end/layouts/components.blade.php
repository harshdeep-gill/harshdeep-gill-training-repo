<x-layout>
	<x-header>
		<x-header.site-logo />

		<x-header.primary-nav>
			{{-- // If URL is passed, then dropdown content shouldn't be toggled. --}}
			<x-header.nav-item title="Destinations" icon="" url="">
				<x-header.nav-item-dropdown-content>
					<x-header.nav-item-dropdown-content-column>
						<x-header.nav-item-featured image_id="34">
							<x-header.nav-item-featured-title title="Explore Polar Regions" />
							<x-header.nav-item-featured-subtitle subtitle="Incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis." />
							<x-button href="#" size="big">Explore Polar Regions</x-button>
						</x-header.nav-item-featured>
					</x-header.nav-item-dropdown-content-column>
					<x-header.nav-item-dropdown-content-column>
						<x-two-columns :border="true">
							<x-two-columns.column>
								<x-menu-list title="Antarctic Regions">
									<x-menu-list.item title="Antarctic Peninsula" url="#" />
									<x-menu-list.item title="Falkland Islands" url="#" />
									<x-menu-list.item title="Patagonia" url="#" />
									<x-menu-list.item title="South Georgia" url="#" />
									<x-menu-list.item title="Snow Hill Island" url="#" />
								</x-menu-list>
								<x-thumbnail-cards :is_carousel="false" :full_width="false">
									<x-thumbnail-cards.card size="medium" url="#" orientation="landscape" image_id="29">
										<x-thumbnail-cards.title title="Arctic Expeditions" align="bottom" />
									</x-thumbnail-cards.card>
								</x-thumbnail-cards>
							</x-two-columns.column>
							<x-two-columns.column>
								<x-menu-list title="Arctic Regions">
									<x-menu-list.item title="Canadian High Arctic" url="#" />
									<x-menu-list.item title="Greenland" url="#" />
									<x-menu-list.item title="Svalbard" url="#" />
								</x-menu-list>
								<x-thumbnail-cards :is_carousel="false" :full_width="false">
									<x-thumbnail-cards.card size="medium" url="#" orientation="landscape" image_id="34">
										<x-thumbnail-cards.title title="Patagonia Expeditions" align="bottom" />
									</x-thumbnail-cards.card>
								</x-thumbnail-cards>
							</x-two-columns.column>
						</x-two-columns>
					</x-header.nav-item-dropdown-content-column>
				</x-header.nav-item-dropdown-content>
			</x-header.nav-item>

			<x-header.nav-item title="Expeditions" icon="" url="">
				<x-header.nav-item-dropdown-content>
					<x-header.nav-item-dropdown-content-column>
						<x-header.nav-item-featured image_id="32">
							<x-header.nav-item-featured-title title="Explore Polar Expeditions" />
							<x-header.nav-item-featured-subtitle subtitle="Incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis." />
							<x-button size="big">Explore Polar Expeditions</x-button>
						</x-header.nav-item-featured>
					</x-header.nav-item-dropdown-content-column>
					<x-header.nav-item-dropdown-content-column>
						<x-two-columns :border="true">
							<x-two-columns.column>
								<x-menu-list title="Antarctic Expeditions">
									<x-menu-list.item title="Antarctic Peninsula" url="#" />
									<x-menu-list.item title="Falkland Islands & South Georgia" url="#" />
									<x-menu-list.item title="Patagonia" url="#" />
									<x-menu-list.item title="Snow Hill Island" url="#" />
								</x-menu-list>
								<x-thumbnail-cards :is_carousel="false" :full_width="false">
									<x-thumbnail-cards.card size="medium" url="#" orientation="landscape" image_id="32">
										<x-thumbnail-cards.title title="View All Antarctic Departures" align="bottom" />
									</x-thumbnail-cards.card>
								</x-thumbnail-cards>
							</x-two-columns.column>
							<x-two-columns.column>
								<x-menu-list title="arctic Regions">
									<x-menu-list.item title="Canadian High Arctic" url="#" />
									<x-menu-list.item title="Greenland" url="#" />
									<x-menu-list.item title="Svalbard" url="#" />
								</x-menu-list>
								<x-thumbnail-cards :is_carousel="false" :full_width="false">
									<x-thumbnail-cards.card size="medium" url="#" orientation="landscape" image_id="36">
										<x-thumbnail-cards.title title="View All Antarctic Departures" align="bottom" />
									</x-thumbnail-cards.card>
								</x-thumbnail-cards>
							</x-two-columns.column>
						</x-two-columns>
					</x-header.nav-item-dropdown-content-column>
				</x-header.nav-item-dropdown-content>
			</x-header.nav-item>
			<x-header.nav-item title="Ships" icon="" url="">
				<x-header.nav-item-dropdown-content>
					<x-header.nav-item-dropdown-content-column>
						<x-header.nav-item-featured image_id="32">
							<x-header.nav-item-featured-title title="Advantage of Small Ships" />
							<x-header.nav-item-featured-subtitle subtitle="Incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis." />
							<x-button size="big">View All Ships</x-button>
						</x-header.nav-item-featured>
					</x-header.nav-item-dropdown-content-column>
					<x-header.nav-item-dropdown-content-column>
						<x-two-columns :border="true">
							<x-two-columns.column>
								<x-menu-list title="Ships">
									<x-menu-list.item title="Ocean Adventurer" url="#" />
									<x-menu-list.item title="Ocean Explorer" url="#" />
									<x-menu-list.item title="Ultramarine" url="#" />
									<x-menu-list.item title="World Explorer" url="#" />
								</x-menu-list>
							</x-two-columns.column>
						</x-two-columns>
					</x-header.nav-item-dropdown-content-column>
				</x-header.nav-item-dropdown-content>
			</x-header.nav-item>
			<x-header.nav-item title="Offers" icon="" url="">
				<x-header.nav-item-dropdown-content>
					<x-header.nav-item-dropdown-content-column>
						<x-header.nav-item-featured image_id="31">
							<x-header.nav-item-featured-title title="Explore Arctic: Save Up to 40%" />
							<x-button size="big">View All Offers</x-button>
						</x-header.nav-item-featured>
					</x-header.nav-item-dropdown-content-column>
					<x-header.nav-item-dropdown-content-column>
						<x-two-columns :border="true">
							<x-two-columns.column>
								<x-thumbnail-cards :is_carousel="false" :full_width="false">
									<x-thumbnail-cards.card size="small" url="#" orientation="portrait" image_id="29">
										<x-thumbnail-cards.title title="Arctic Expeditions" align="bottom" />
									</x-thumbnail-cards.card>
									<x-thumbnail-cards.card size="small" url="#" orientation="portrait" image_id="30">
										<x-thumbnail-cards.title title="Antarctic Expeditions" align="bottom" />
									</x-thumbnail-cards.card>
									<x-thumbnail-cards.card size="small" url="#" orientation="portrait" image_id="33">
										<x-thumbnail-cards.title title="Patagonia Expeditions" align="bottom" />
									</x-thumbnail-cards.card>
								</x-thumbnail-cards>
							</x-two-columns.column>
						</x-two-columns>
					</x-header.nav-item-dropdown-content-column>
				</x-header.nav-item-dropdown-content>
			</x-header.nav-item>
			<x-header.nav-item title="More" icon="" url="" class="header__more-menu-item">
				<x-header.nav-item-dropdown-content>
					<x-header.nav-item-dropdown-content-column>
						<x-header.nav-item-featured image_id="34">
							<x-header.nav-item-featured-title title="Why Quark Expeditions" />
							<x-header.nav-item-featured-subtitle subtitle="Incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis." />
							<x-button size="big">Why Quark Expeditions</x-button>
						</x-header.nav-item-featured>
					</x-header.nav-item-dropdown-content-column>
					<x-header.nav-item-dropdown-content-column>
						<x-two-columns :border="true">
							<x-two-columns.column>
								<x-menu-list title="More">
									<x-menu-list.item title="Why Quark Expeditions" url="#" />
									<x-menu-list.item title="Our Expedition History" url="#" />
									<x-menu-list.item title="Our Team" url="#" />
									<x-menu-list.item title="Adventure Options" url="#" />
									<x-menu-list.item title="Sustainability" url="#" />
									<x-menu-list.item title="Blog" url="#" />
									<x-menu-list.item title="Dates and Rates" url="#" />
									<x-menu-list.item title="Travel Advisors" url="#" />
								</x-menu-list>
							</x-two-columns.column>
						</x-two-columns>
					</x-header.nav-item-dropdown-content-column>
				</x-header.nav-item-dropdown-content>
			</x-header.nav-item>
			<x-header.nav-item title="About Us" icon="" url="">
				<x-header.nav-item-dropdown-content>
					<x-header.nav-item-dropdown-content-column>
						<x-header.nav-item-featured image_id="34">
							<x-header.nav-item-featured-title title="Why Quark Expeditions" />
							<x-header.nav-item-featured-subtitle subtitle="Incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis." />
							<x-button size="big">Why Quark Expeditions</x-button>
						</x-header.nav-item-featured>
					</x-header.nav-item-dropdown-content-column>
					<x-header.nav-item-dropdown-content-column>
						<x-two-columns :border="true">
							<x-two-columns.column>
								<x-menu-list title="About Quark Expeditions">
									<x-menu-list.item title="Why Quark Expeditions" url="#" />
									<x-menu-list.item title="Our Expedition History" url="#" />
									<x-menu-list.item title="Our Team" url="#" />
									<x-menu-list.item title="Adventure Options" url="#" />
									<x-menu-list.item title="Sustainability" url="#" />
									<x-menu-list.item title="Blog" url="#" />
								</x-menu-list>
							</x-two-columns.column>
						</x-two-columns>
					</x-header.nav-item-dropdown-content-column>
				</x-header.nav-item-dropdown-content>
			</x-header.nav-item>
		</x-header.primary-nav>

		<x-header.secondary-nav>
			<x-header.nav-item class="header__search-item" title="" icon="search" url="" />
			<x-header.nav-item title="Dates & Prices" icon="" url="/dates-prices" />
			<x-header.nav-item title="Travel Advisors" icon="" url="/travel-advisors" />
		</x-header.secondary-nav>

		<x-header.cta-buttons>
			<x-button class="header__phone-btn" href="tel:+1-877-585-1235" size="big" color="white" appearance="outline">
				<x-svg name="phone" />
				+1-877-585-1235
			</x-button>
			<x-button class="header__request-quote-btn" size="big">Request a Quote</x-button>
		</x-header.cta-buttons>

		<x-drawer.drawer-open drawer_id="header-drawer" class="color-context--dark header__hamburger-menu">
			<x-button type="button" size="big" color="black">
				<x-svg name="hamburger" />
			</x-button>
		</x-drawer.drawer-open>

		<x-drawer id="header-drawer" class="header__drawer">
			<x-drawer.header>
				<x-header.site-logo />
			</x-drawer.header>

			<x-drawer.body>
				<x-accordion title="Quark Expeditions takes you places no one else can!">
					<x-accordion.item>
						<x-accordion.item-handle title="Destinations" />
						<x-accordion.item-content>
							<x-header.nav-item-featured image_id="32" size="small">
								<x-header.nav-item-featured-title title="Explore Polar Regions" />
								<x-header.nav-item-featured-subtitle subtitle="Incididunt ut labore et dolore magna aliqua." />
								<x-button size="big">Explore Polar Regions</x-button>
							</x-header.nav-item-featured>

							<x-menu-list title="Antarctic Regions">
								<x-menu-list.item title="Antarctic Peninsula" url="#" />
								<x-menu-list.item title="Falkland Islands" url="#" />
								<x-menu-list.item title="Patagonia" url="#" />
								<x-menu-list.item title="South Georgia" url="#" />
								<x-menu-list.item title="Snow Hill Island" url="#" />
							</x-menu-list>

							<x-menu-list title="Arctic Regions">
								<x-menu-list.item title="Canadian High Arctic" url="#" />
								<x-menu-list.item title="Greenland" url="#" />
								<x-menu-list.item title="Svalbard" url="#" />
							</x-menu-list>
						</x-accordion.item-content>
					</x-accordion.item>
					<x-accordion.item>
						<x-accordion.item-handle title="Expeditions" />
						<x-accordion.item-content>
							<x-header.nav-item-featured image_id="32" size="small">
								<x-header.nav-item-featured-title title="Explore Polar Regions" />
								<x-header.nav-item-featured-subtitle subtitle="Incididunt ut labore et dolore magna aliqua." />
								<x-button size="big">Explore Polar Regions</x-button>
							</x-header.nav-item-featured>

							<x-menu-list title="Antarctic Regions">
								<x-menu-list.item title="Antarctic Peninsula" url="#" />
								<x-menu-list.item title="Falkland Islands" url="#" />
								<x-menu-list.item title="Patagonia" url="#" />
								<x-menu-list.item title="South Georgia" url="#" />
								<x-menu-list.item title="Snow Hill Island" url="#" />
							</x-menu-list>
						</x-accordion.item-content>
					</x-accordion.item>
					<x-accordion.item>
						<x-accordion.item-handle title="Ships" />
						<x-accordion.item-content>
							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Magnis dis parturient montes nascetur ridiculus mus mauris. Pharetra pharetra massa massa ultricies mi quis hendrerit dolor. Aliquam nulla facilisi cras fermentum odio. Dolor sit amet consectetur adipiscing elit pellentesque habitant.</p>
						</x-accordion.item-content>
					</x-accordion.item>
					<x-accordion.item>
						<x-accordion.item-handle title="Offers" />
						<x-accordion.item-content>
							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Magnis dis parturient montes nascetur ridiculus mus mauris. Pharetra pharetra massa massa ultricies mi quis hendrerit dolor. Aliquam nulla facilisi cras fermentum odio. Dolor sit amet consectetur adipiscing elit pellentesque habitant.</p>
						</x-accordion.item-content>
					</x-accordion.item>
					<x-accordion.item>
						<x-accordion.item-handle title="About Us" />
						<x-accordion.item-content>
							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Magnis dis parturient montes nascetur ridiculus mus mauris. Pharetra pharetra massa massa ultricies mi quis hendrerit dolor. Aliquam nulla facilisi cras fermentum odio. Dolor sit amet consectetur adipiscing elit pellentesque habitant.</p>
						</x-accordion.item-content>
					</x-accordion.item>
				</x-accordion>

				<ul class="header__drawer-quick-links">
					<li><a href="#">Dates & Rates</a></li>
					<li><a href="#">Travel Advisors</a></li>
					<li><a href="tel:+1-877-585-1235">Call Now to Book : +1 (866) 253-3145</a></li>
				</ul>

				<x-button class="header__drawer-request-quote-btn" size="big">Request a Quote</x-button>
			</x-drawer.body>
		</x-drawer>
	</x-header>

	<x-component-demo :keys="[ 'sidebar-grid', 'lp-header', 'hero-refactor' ]">
		<x-lp-header
			tc_image_id="18"
			phone_number="+1-877-585-1235"
			cta_text="Talk to a Polar Expert"
			:dark_mode="true"
		/>
	</x-component-demo>

	<x-component-demo :keys="[ 'hero', 'hero-refactor' ]">
		<x-hero text_align="left" immersive="all" :overlay_opacity="10">
			<x-hero.image image_id="26" />
			<x-breadcrumbs
				:breadcrumbs="[
					[
						'title' => 'Home',
						'url'   => '#',
					],
					[
						'title' => 'Blog',
						'url'   => '#',
					],
				]"
			/>
			<x-hero.content>
				<x-hero.left>
					<x-hero.title-container>
						<x-hero.overline>Antarctic 2024</x-hero.overline>
						<x-hero.title title="Antarctic Voyages" />
						<x-hero.sub-title title="Choose the Leader in Polar Adventure" />
						<x-hero.description>
							<p>
								This is the description of this hero section. Lorem ipsum dolor sit amet consectetur adipisicing elit. Incidunt odio illum tempora doloremque. Suscipit obcaecati necessitatibus, exercitationem nostrum voluptatibus eligendi laudantium possimus quaerat reiciendis molestiae sit sunt iusto! Ex facere quidem cupiditate ullam dolorum consectetur delectus recusandae. Minima, itaque eaque!
							</p>
						</x-hero.description>
					</x-hero.title-container>
					<x-icon-badge class="hero__tag" background_color="attention-100" icon="alert" text="Limited Cabins Available" />
					<x-hero.form-modal-cta>Get a Digital Brochure</x-hero.form-modal-cta>
				</x-hero.left>
				<x-hero.right>
					<x-hero.form>
						<x-form-two-step
							background_color="white"
							:countries="[
								'IN' => 'India',
								'AU' => 'Australia',
								'US' => 'United States',
								'CA' => 'Canada',
							]"
							:states="[
								'AU' => [
									'ACT' => 'Australian Capital Territory',
									'JBT' => 'Jervis Bay Territory',
								],
								'US' => [
									'AA' => 'Armed Forces Americas',
									'AE' => 'Armed Forces Europe',
								],
								'CA' => [
									'AB' => 'Alberta',
									'BC' => 'British Columbia',
								],
							]"
						/>
					</x-hero.form>
				</x-hero.right>
			</x-hero.content>
		</x-hero>
	</x-component-demo>

	<x-component-demo :keys="[ 'review-cards', 'hero-refactor' ]">
		<x-review-cards>
			<x-review-cards.card>
				<x-review-cards.rating rating="4" />
				<x-review-cards.title title="Falkland, South Georgia and the Antarctic Circle" />
				<x-review-cards.content>
					<p>
						Most exciting and wonderful, and educational experience of my life, thank you to all the experts for sharing so much information on the animals and arctic region, cruise staff and fellow passengers for making this trip one I will remember forever. Most exciting and wonderful, and educational experience of my life, thank you to all the experts for sharing so much information on the animals and arctic region, cruise staff and fellow passengers for making this trip one I will remember forever.
					</p>
				</x-review-cards.content>
				<x-review-cards.author name="Denise P." />
				<x-review-cards.author-details text="South Georgia and Antarctic Peninsula: Penguin Safari South Georgia and Antarctic Peninsula: Penguin Safari" />
			</x-review-cards.card>
			<x-review-cards.card>
				<x-review-cards.rating rating="4" />
				<x-review-cards.title title="Antartica with quark – experience of a lifetime" />
				<x-review-cards.content>
					<p>
						In a phrase, going to Antartica with Quark was “simply amazing”. Antartica is gorgeous and the team at Quark made it possible for us to enjoy every bit of it with their impeccable planning and attention to every detail.
					</p>
				</x-review-cards.content>
				<x-review-cards.author name="Madhuchanda D." />
				<x-review-cards.author-details text="Antarctic Express: Crossing the Circle" />
			</x-review-cards.card>
			<x-review-cards.card>
				<x-review-cards.rating rating="4" />
				<x-review-cards.title title="Falkland, South Georgia and the Antarctic Circle" />
				<x-review-cards.content>
					<p>
						Most exciting and wonderful, and educational experience of my life, thank you to all the experts for sharing so much information on the animals and arctic region, cruise staff and fellow passengers for making this trip one I will remember forever. Most exciting and wonderful, and educational experience of my life, thank you to all the experts for sharing so much information on the animals and arctic region, cruise staff and fellow passengers for making this trip one I will remember forever.
					</p>
				</x-review-cards.content>
				<x-review-cards.author name="Denise P." />
				<x-review-cards.author-details text="South Georgia and Antarctic Peninsula: Penguin Safari" />
			</x-review-cards.card>
			<x-review-cards.card>
				<x-review-cards.rating rating="4" />
				<x-review-cards.title title="Falkland, South Georgia and the Antarctic Circle" />
				<x-review-cards.content>
					<p>
						Most exciting and wonderful, and educational experience of my life, thank you to all the experts for sharing so much information on the animals and arctic region, cruise staff and fellow passengers for making this trip one I will remember forever. Most exciting and wonderful, and educational experience of my life, thank you to all the experts for sharing so much information on the animals and arctic region, cruise staff and fellow passengers for making this trip one I will remember forever.
					</p>
				</x-review-cards.content>
				<x-review-cards.author name="Denise P." />
				<x-review-cards.author-details text="South Georgia and Antarctic Peninsula: Penguin Safari" />
			</x-review-cards.card>
			<x-review-cards.card>
				<x-review-cards.rating rating="4" />
				<x-review-cards.title title="Falkland, South Georgia and the Antarctic Circle" />
				<x-review-cards.content>
					<p>
						Most exciting and wonderful, and educational experience of my life, thank you to all the experts for sharing so much information on the animals and arctic region, cruise staff and fellow passengers for making this trip one I will remember forever. Most exciting and wonderful, and educational experience of my life, thank you to all the experts for sharing so much information on the animals and arctic region, cruise staff and fellow passengers for making this trip one I will remember forever.
					</p>
				</x-review-cards.content>
				<x-review-cards.author name="Denise P." />
				<x-review-cards.author-details text="South Georgia and Antarctic Peninsula: Penguin Safari" />
			</x-review-cards.card>
		</x-review-cards>
	</x-component-demo>
	<x-component-demo :keys="[ 'review-cards-no-carousel' ]">
		<x-section>
			<x-section.heading>
				<x-section.title title="South Georgia Expedition Reviews" />
			</x-section.heading>
			<x-review-cards is_carousel="false">
				<x-review-cards.card>
					<x-review-cards.rating rating="5" />
					<x-review-cards.title title="Falkland, South Georgia and the Antarctic Circle" />
					<x-review-cards.content>
						Most exciting and wonderful, and educational experience of my life, thank you to all the experts for sharing so much information on the animals and arctic region, cruise staff and fellow passengers for making this trip one I will remember forever. Most exciting and wonderful, and educational experience of my life, thank you to all the experts for sharing so much information on the animals and arctic region, cruise staff and fellow passengers for making this trip one I will remember forever.
					</x-review-cards.content>
					<x-review-cards.author name="Denise P." />
					<x-review-cards.author-details text="South Georgia and Antarctic Peninsula: Penguin Safari" />
				</x-review-cards.card>
				<x-review-cards.card>
					<x-review-cards.rating rating="4" />
					<x-review-cards.title title="Antartica with quark – experience of a lifetime" />
					<x-review-cards.content>
						<p>
							In a phrase, going to Antartica with Quark was “simply amazing”. Antartica is gorgeous and the team at Quark made it possible for us to enjoy every bit of it with their impeccable planning and attention to every detail.
						</p>
					</x-review-cards.content>
					<x-review-cards.author name="Madhuchanda D." />
					<x-review-cards.author-details text="Antarctic Express: Crossing the Circle" />
				</x-review-cards.card>
				<x-review-cards.card>
					<x-review-cards.rating rating="4" />
					<x-review-cards.title title="Falkland, South Georgia and the Antarctic Circle" />
					<x-review-cards.content>
						<p>
							Most exciting and wonderful, and educational experience of my life, thank you to all the experts for sharing so much information on the animals and arctic region, cruise staff and fellow passengers for making this trip one I will remember forever. Most exciting and wonderful, and educational experience of my life, thank you to all the experts for sharing so much information on the animals and arctic region, cruise staff and fellow passengers for making this trip one I will remember forever.
						</p>
					</x-review-cards.content>
					<x-review-cards.author name="Denise P." />
					<x-review-cards.author-details text="South Georgia and Antarctic Peninsula: Penguin Safari" />
				</x-review-cards.card>
			</x-review-cards>
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'hero', 'hero-refactor' ]">
		<x-hero text_align="center" immersive="none">
			<x-hero.image image_id="26" />
			<x-hero.content>
				<x-hero.left>
					<x-hero.title-container>
						<x-hero.overline>Antarctic 2024</x-hero.overline>
						<x-hero.title title="Antarctic Voyages" />
						<x-hero.sub-title title="Choose the Leader in Polar Adventure" />
					</x-hero.title-container>
					<x-icon-badge class="hero__tag" background_color="attention-100" icon="alert" text="Limited Cabins Available" />
					<x-hero.form-modal-cta>Get a Digital Brochure</x-hero.form-modal-cta>
				</x-hero.left>
				<x-hero.right>
					<x-hero.form>
						<x-form-two-step-compact
							:countries="[
								'IN' => 'India',
								'AU' => 'Australia',
								'US' => 'United States',
								'CA' => 'Canada',
							]"
							:states="[
								'AU' => [
									'ACT' => 'Australian Capital Territory',
									'JBT' => 'Jervis Bay Territory',
								],
								'US' => [
									'AA' => 'Armed Forces Americas',
									'AE' => 'Armed Forces Europe',
								],
								'CA' => [
									'AB' => 'Alberta',
									'BC' => 'British Columbia',
								],
							]"
						/>
					</x-hero.form>
				</x-hero.right>
			</x-hero.content>
		</x-hero>
	</x-component-demo>

	<x-component-demo :keys="[ 'hero', 'hero-updated' ]">
		<x-hero text_align="left">
			<x-hero.image image_id="26" />
			<x-breadcrumbs
				:breadcrumbs="[
					[
						'title' => 'Home',
						'url'   => '#',
					],
					[
						'title' => 'Blog',
						'url'   => '#',
					],
				]"
			/>
			<x-hero.content>
				<x-hero.left>
					<x-hero.title-container>
						<x-hero.overline>FEATURED - 4 mins read</x-hero.overline>
						<x-hero.title title="How to see Polar Bears in Svalbard" />
					</x-hero.title-container>
					<x-button size="big" href="#">Read Post</x-button>
				</x-hero.left>
			</x-hero.content>
		</x-hero>
	</x-component-demo>

	<x-component-demo :keys="[ 'global', 'color-palette' ]">
		<x-section>
			<x-section.heading>
				<x-section.title title="Color Palette" heading_level="2" />
			</x-section.heading>
			<x-global-styles-demo.color-palette />
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'global', 'typography' ]">
		<x-section>
			<x-section.heading>
				<x-section.title title="Typography" heading_level="2" />
			</x-section.heading>
			<x-global-styles-demo.typography />
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'global', 'buttons' ]">
		<x-section>
			<x-section.heading>
				<x-section.title title="Buttons & Links" heading_level="2" />
			</x-section.heading>
			<h3>Links</h3>

			<div style="display: flex; flex-wrap: wrap; width: 100%;" class="typography-spacing">
				<div style="width: 50%; padding: 24px; border: 1px solid var(--color-black);">
					<p><a href="#">I am a link, roll over me!</a></p>
				</div>
				<div style="width: 50%; padding: 24px; background-color: var(--color-black);" class="color-context--dark">
					<p><a href="#">I am a link, roll over me!</a></p>
				</div>
			</div>

			<h3>Button Blocks</h3>

			<div class="wp-block-buttons" style="display: flex; gap: 16px;">
				<div class="wp-block-button"><a href="#" class="wp-block-button__link">Default Button</a></div>
				<div class="wp-block-button is-style-outline"><a href="#" class="wp-block-button__link">Solid Button</a></div>
			</div>

			<h3>All Buttons</h3>

			<div style="display: flex; flex-wrap: wrap; width: 100%;" class="typography-spacing">
				<div style="width: 50%; padding: 24px; border: 1px solid var(--color-black);">
					<div class="typography-spacing" style="display: flex; gap: 16px; flex-flow: row wrap;">
						<x-button>Solid button</x-button>
						<x-button appearance="outline">Outline button</x-button>
						<x-button color="black">Solid black button</x-button>
					</div>
					<div class="typography-spacing" style="display: flex; gap: 16px; flex-flow: row wrap;">
						<x-button size="big">Solid button</x-button>
						<x-button size="big" appearance="outline">Outline button</x-button>
						<x-button size="big" color="black">Solid black button</x-button>
					</div>
				</div>
				<div style="width: 50%; padding: 24px; background-color: var(--color-black);" class="color-context--dark">
					<div class="typography-spacing" style="display: flex; gap: 16px; flex-flow: row wrap;">
						<x-button>Solid button</x-button>
						<x-button appearance="outline">Outline button</x-button>
						<x-button color="black">Solid black button</x-button>
					</div>
					<div class="typography-spacing" style="display: flex; gap: 16px; flex-flow: row wrap;">
						<x-button size="big">Solid button</x-button>
						<x-button size="big" appearance="outline">Outline button</x-button>
						<x-button size="big" color="black">Solid black button</x-button>
					</div>
				</div>
			</div>
			<div style="display: flex; width: 100%; gap: 16px;" class="typography-spacing">
				<x-button variant="media"><x-svg name="play" /></x-button>
				<x-button variant="media"><x-svg name="pause" /></x-button>
			</div>

		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'form' ]">
		<x-section style="display: flex; flex-wrap: wrap; width: 100%;" class="typography-spacing">
			<x-section.heading>
				<x-section.title title="Form UI Elements" heading_level="2" />
			</x-section.heading>
			<div style="display: flex; flex-wrap: wrap; width: 100%; gap: 20px; justify-content: space-between;">
				<x-form salesforce_object="Webform_Landing_Page__c" style="min-width: 300px; padding: 24px; border: 1px solid var(--color-black); display:flex; flex-wrap: wrap; flex-direction: column; flex-grow: 1;">
					<x-form.field :validation="[ 'required' ]">
						<x-form.input type="text" label="First Name" placeholder="Enter First Name" name="fields[FirstName__c]" />
					</x-form.field>
					<x-form.field :validation="[ 'required' ]">
						<x-form.input type="text" label="Last Name" placeholder="Enter First Name" name="fields[LastName__c]" />
					</x-form.field>
					<x-form.field :validation="[ 'required' ]">
						<x-form.select label="Country">
							<x-form.option value="">Select...</x-form.option>
							<x-form.option value="1" label="Option 1">Option 1</x-form.option>
							<x-form.option value="2" label="Option 2">Option 2</x-form.option>
							<x-form.option value="3" label="Option 3">Option 3</x-form.option>
						</x-form.select>
					</x-form.field>
					<x-form.field>
						<x-form.textarea label="What else would you like us to know?" placeholder="eg Lorem ipsum"></x-form.textarea>
					</x-form.field>
					<x-form.field>
						<x-form.file label="Choose File" />
					</x-form.field>
					<x-form.field>
						<x-form.checkbox label="Checkbox example" />
					</x-form.field>
					<x-form.field>
						<x-form.radio name="radio-example" label="Radio example" />
					</x-form.field>
					<x-form.field>
						<x-form.radio name="radio-example" label="Radio example" />
					</x-form.field>
					<x-form.buttons>
						<x-form.submit>Request a Quote</x-form.submit>
					</x-form.buttons>
				</x-form>

				<x-form
					style="background-color: var(--color-black); min-width: 300px; padding: 24px; border: 1px solid var(--color-black); display:flex; flex-wrap: wrap; flex-direction: column; flex-grow: 1;"
					class="color-context--dark">
					<x-form.field :validation="[ 'required' ]">
						<x-form.input type="text" label="First Name" placeholder="Enter First Name" name="fields[first_name]" />
					</x-form.field>
					<x-form.field :validation="[ 'required' ]">
						<x-form.select label="Country" name="fields[country]">
							<x-form.option value="">Select...</x-form.option>
							<x-form.option value="1" label="Option 1">Option 1</x-form.option>
							<x-form.option value="2" label="Option 2">Option 2</x-form.option>
							<x-form.option value="3" label="Option 3">Option 3</x-form.option>
						</x-form.select>
					</x-form.field>
					<x-form.field>
						<x-form.textarea label="What else would you like us to know?" placeholder="eg Lorem ipsum" name="fields[comments]"></x-form.textarea>
					</x-form.field>
					<x-form.field>
						<x-form.file label="Choose File" />
					</x-form.field>
					<x-form.field>
						<x-form.checkbox label="Checkbox example" />
					</x-form.field>
					<x-form.field>
						<x-form.radio name="radio-example" label="Radio example" />
					</x-form.field>
					<x-form.field>
						<x-form.radio name="radio-example" label="Radio example" />
					</x-form.field>
					<x-form.buttons>
						<x-form.submit>Request a Quote</x-form.submit>
					</x-form.buttons>
				</x-form>
			</div>
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'field-group' ]">
		<x-section>
			<x-form>
				<x-form.field-group title="Example field group. Radio" :validation="[ 'radio-group-required' ]">
					<x-form.radio name="radio-example" label="Radio example" />
					<x-form.radio name="radio-example" label="Radio example" />
					<x-form.radio name="radio-example" label="Radio example" />
					<x-form.radio name="radio-example" label="Radio example" />
					<x-form.radio name="radio-example" label="Radio example" />
					<x-form.radio name="radio-example" label="Radio example" />
					<x-form.radio name="radio-example" label="Radio example" />
					<x-form.radio name="radio-example" label="Radio example" />
					<x-form.radio name="radio-example" label="Radio example" />
					<x-form.radio name="radio-example" label="Radio example" />
					<x-form.radio name="radio-example" label="Radio example" />
					<x-form.radio name="radio-example" label="Radio example" />
					<x-form.radio name="radio-example" label="Radio example with text that is longer than usual" />
					<x-form.radio name="radio-example" label="Radio example" />
					<x-form.radio name="radio-example" label="Radio example" />
					<x-form.radio name="radio-example" label="Radio example" />
				</x-form.field-group>
				<x-form.field-group title="Example field group. Checkbox" :validation="[ 'checkbox-group-required' ]">
					<x-form.checkbox name="checkbox-example" label="Radio example" />
					<x-form.checkbox name="checkbox-example" label="Radio example" />
					<x-form.checkbox name="checkbox-example" label="Radio example" />
					<x-form.checkbox name="checkbox-example" label="Radio example" />
					<x-form.checkbox name="checkbox-example" label="Radio example" />
					<x-form.checkbox name="checkbox-example" label="Radio example" />
					<x-form.checkbox name="checkbox-example" label="Radio example" />
					<x-form.checkbox name="checkbox-example" label="Radio example" />
					<x-form.checkbox name="checkbox-example" label="Radio example" />
					<x-form.checkbox name="checkbox-example" label="Radio example" />
					<x-form.checkbox name="checkbox-example" label="Radio example" />
					<x-form.checkbox name="checkbox-example" label="Radio example" />
					<x-form.checkbox name="checkbox-example" label="Radio example with text that is longer than usual" />
					<x-form.checkbox name="checkbox-example" label="Radio example" />
					<x-form.checkbox name="checkbox-example" label="Radio example" />
					<x-form.checkbox name="checkbox-example" label="Radio example" />
				</x-form.field-group>
				<x-form.buttons>
					<x-form.submit>Request a Quote</x-form.submit>
				</x-form.buttons>
			</x-form>
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'lp-header' ]">
		<x-lp-header
			tc_image_id="18"
			phone_number="+1-877-585-1235"
			cta_text="Talk to a Polar Expert"
		/>
	</x-component-demo>

	<x-component-demo :keys="[ 'modal' ]">
		<x-section>
			<x-section.heading>
				<x-section.title title="Flexible Multipurpose modal" />
			</x-section.heading>
			<x-modal.modal-open modal_id="multipurpose-modal-demo">
				<x-button type="button">
					Open a sample modal
					<x-button.sub-title title="It is dynamic" />
				</x-button>
			</x-modal.modal-open>
			<x-modal id="multipurpose-modal-demo" >
				<x-modal.header>
					<h3>Lorem ipsum dolor sit amet.</h3>
					<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur voluptate dolorum alias officiis minima nemo asperiores maxime velit itaque sapiente?</p>
				</x-modal.header>

				<x-modal.body>
					Lorem ipsum dolor, sit amet consectetur adipisicing elit. Esse
					excepturi blanditiis cum eum perspiciatis dignissimos dolorum minus
					est, necessitatibus enim, quisquam quibusdam porro architecto nostrum
					dolorem vero sed vel facere exercitationem soluta assumenda omnis,
					voluptate non natus! Tenetur a deleniti recusandae. Molestiae nobis
					quis odit optio dolorum facilis distinctio deleniti perferendis odio
					commodi veniam voluptate provident pariatur voluptatum debitis
					exercitationem asperiores reiciendis aperiam excepturi magni quae
					cumque necessitatibus, cupiditate ipsum. Natus doloribus ullam
					porro ad corporis minus expedita repellat temporibus earum.
					Earum vero ea nostrum tenetur blanditiis commodi sed a id modi
					minus iusto pariatur architecto odit non molestias rerum enim
					tempora aspernatur porro nam unde, quas laboriosam facere. Aut,
					porro labore molestias aperiam modi velit fugit vel sunt earum
					harum tempora autem dolor aspernatur optio. Suscipit, eum ipsum
					rem nisi qui ullam distinctio molestias modi ratione aut molestiae
					laborum beatae iusto debitis magni quaerat eos ea deserunt commodi
					quas fugiat provident. Quod, quidem deleniti. Totam, necessitatibus
					mollitia veritatis assumenda dolorem reprehenderit esse fuga?
					Eius explicabo in, animi quas, deleniti laboriosam voluptas hic dolore
					ea incidunt totam saepe. Lorem ipsum dolor, sit amet consectetur
					adipisicing elit. Esse excepturi blanditiis cum eum perspiciatis dignissimos
					dolorum minus est, necessitatibus enim, quisquam quibusdam porro architecto nostrum
					dolorem vero sed vel facere exercitationem soluta assumenda omnis,
					voluptate non natus! Tenetur a deleniti recusandae. Molestiae nobis
					quis odit optio dolorum facilis distinctio deleniti perferendis odio
					commodi veniam voluptate provident pariatur voluptatum debitis
					exercitationem asperiores reiciendis aperiam excepturi magni quae
					cumque necessitatibus, cupiditate ipsum. Natus doloribus ullam
					porro ad corporis minus expedita repellat temporibus earum.
					Earum vero ea nostrum tenetur blanditiis commodi sed a id modi
					minus iusto pariatur architecto odit non molestias rerum enim
					tempora aspernatur porro nam unde, quas laboriosam facere. Aut,
					porro labore molestias aperiam modi velit fugit vel sunt earum
					harum tempora autem dolor aspernatur optio. Suscipit, eum ipsum
					rem nisi qui ullam distinctio molestias modi ratione aut molestiae
					laborum beatae iusto debitis magni quaerat eos ea deserunt commodi
					quas fugiat provident. Quod, quidem deleniti. Totam, necessitatibus
					mollitia veritatis assumenda dolorem reprehenderit esse fuga?
					Eius explicabo in, animi quas, deleniti laboriosam voluptas hic dolore
					ea incidunt totam saepe.
				</x-modal.body>

				<x-modal.footer>
					<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis, rem?</p>
				</x-modal.footer>
			</x-modal>
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'drawer' ]">
		<x-section title="Flexible Multipurpose drawer">
			<style>
				.multipurpose-drawer-sample .drawer__content {
					max-width: 768px;
					padding: var(--spacing-5);
					gap: var(--spacing-4);
				}

				.multipurpose-drawer-sample .drawer__footer {
					border-top: solid 1px var(--color-gray-20);
					padding-inline: var(--spacing-5);
					margin-inline: calc(-1 * var(--spacing-5));
				}
			</style>
			<x-drawer.drawer-open drawer_id="multipurpose-drawer-sample">
				<x-button type="button" size="big">
					Open a sample drawer
				</x-button>
			</x-drawer.drawer-open>
			<x-drawer id="multipurpose-drawer-sample" animation_direction="up" class="multipurpose-drawer-sample">
				<x-drawer.header>
					<h3>Lorem ipsum dolor sit amet.</h3>
					<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur voluptate dolorum alias officiis minima nemo asperiores maxime velit itaque sapiente?</p>
				</x-drawer.header>

				<x-drawer.body>
					<p>
						Lorem ipsum dolor, sit amet consectetur adipisicing elit. Esse
						excepturi blanditiis cum eum perspiciatis dignissimos dolorum minus
						est, necessitatibus enim, quisquam quibusdam porro architecto nostrum
						dolorem vero sed vel facere exercitationem soluta assumenda omnis,
						voluptate non natus! Tenetur a deleniti recusandae. Molestiae nobis
						quis odit optio dolorum facilis distinctio deleniti perferendis odio
						commodi veniam voluptate provident pariatur voluptatum debitis
						exercitationem asperiores reiciendis aperiam excepturi magni quae
						cumque necessitatibus, cupiditate ipsum. Natus doloribus ullam
						porro ad corporis minus expedita repellat temporibus earum.
						Earum vero ea nostrum tenetur blanditiis commodi sed a id modi
						minus iusto pariatur architecto odit non molestias rerum enim
						tempora aspernatur porro nam unde, quas laboriosam facere. Aut,
						porro labore molestias aperiam modi velit fugit vel sunt earum
						harum tempora autem dolor aspernatur optio. Suscipit, eum ipsum
						rem nisi qui ullam distinctio molestias modi ratione aut molestiae
						laborum beatae iusto debitis magni quaerat eos ea deserunt commodi
						quas fugiat provident. Quod, quidem deleniti. Totam, necessitatibus
						mollitia veritatis assumenda dolorem reprehenderit esse fuga?
						Eius explicabo in, animi quas, deleniti laboriosam voluptas hic dolore
						ea incidunt totam saepe. Lorem ipsum dolor, sit amet consectetur
						adipisicing elit.
					</p>
					<p>
						Lorem ipsum dolor, sit amet consectetur adipisicing elit. Esse
						excepturi blanditiis cum eum perspiciatis dignissimos dolorum minus
						est, necessitatibus enim, quisquam quibusdam porro architecto nostrum
						dolorem vero sed vel facere exercitationem soluta assumenda omnis,
						voluptate non natus! Tenetur a deleniti recusandae. Molestiae nobis
						quis odit optio dolorum facilis distinctio deleniti perferendis odio
						commodi veniam voluptate provident pariatur voluptatum debitis
						exercitationem asperiores reiciendis aperiam excepturi magni quae
						cumque necessitatibus, cupiditate ipsum. Natus doloribus ullam
						porro ad corporis minus expedita repellat temporibus earum.
						Earum vero ea nostrum tenetur blanditiis commodi sed a id modi
						minus iusto pariatur architecto odit non molestias rerum enim
						tempora aspernatur porro nam unde, quas laboriosam facere. Aut,
						porro labore molestias aperiam modi velit fugit vel sunt earum
						harum tempora autem dolor aspernatur optio. Suscipit, eum ipsum
						rem nisi qui ullam distinctio molestias modi ratione aut molestiae
						laborum beatae iusto debitis magni quaerat eos ea deserunt commodi
						quas fugiat provident. Quod, quidem deleniti. Totam, necessitatibus
						mollitia veritatis assumenda dolorem reprehenderit esse fuga?
						Eius explicabo in, animi quas, deleniti laboriosam voluptas hic dolore
						ea incidunt totam saepe. Lorem ipsum dolor, sit amet consectetur
						adipisicing elit.
					</p>
				</x-drawer.body>

				<x-drawer.footer>
					<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis, rem?</p>
				</x-drawer.footer>
			</x-drawer>
			<br><br>

			<x-drawer.drawer-open drawer_id="multipurpose-drawer-sample-2">
				<x-button type="button" size="big">
					Open another drawer
				</x-button>
			</x-drawer.drawer-open>
			<x-drawer id="multipurpose-drawer-sample-2" animation_direction="up" class="multipurpose-drawer-sample">
				<x-drawer.header>
					<h3>Lorem ipsum dolor sit amet.</h3>
					<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur voluptate dolorum alias officiis minima nemo asperiores maxime velit itaque sapiente?</p>
				</x-drawer.header>

				<x-drawer.body>
					<p>
						Lorem ipsum dolor, sit amet consectetur adipisicing elit. Esse
						excepturi blanditiis cum eum perspiciatis dignissimos dolorum minus
						est, necessitatibus enim, quisquam quibusdam porro architecto nostrum
						dolorem vero sed vel facere exercitationem soluta assumenda omnis,
						voluptate non natus! Tenetur a deleniti recusandae. Molestiae nobis
						quis odit optio dolorum facilis distinctio deleniti perferendis odio
						commodi veniam voluptate provident pariatur voluptatum debitis
						exercitationem asperiores reiciendis aperiam excepturi magni quae
						cumque necessitatibus, cupiditate ipsum. Natus doloribus ullam
						porro ad corporis minus expedita repellat temporibus earum.
						Earum vero ea nostrum tenetur blanditiis commodi sed a id modi
						minus iusto pariatur architecto odit non molestias rerum enim
						tempora aspernatur porro nam unde, quas laboriosam facere. Aut,
						porro labore molestias aperiam modi velit fugit vel sunt earum
						harum tempora autem dolor aspernatur optio. Suscipit, eum ipsum
						rem nisi qui ullam distinctio molestias modi ratione aut molestiae
						laborum beatae iusto debitis magni quaerat eos ea deserunt commodi
						quas fugiat provident. Quod, quidem deleniti. Totam, necessitatibus
						mollitia veritatis assumenda dolorem reprehenderit esse fuga?
						Eius explicabo in, animi quas, deleniti laboriosam voluptas hic dolore
						ea incidunt totam saepe. Lorem ipsum dolor, sit amet consectetur
						adipisicing elit.
					</p>
				</x-drawer.body>

				<x-drawer.footer>
					<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis, rem?</p>
				</x-drawer.footer>
			</x-drawer>
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'reviews-carousel', 'two-columns' ]">
		<x-section>
			<x-two-columns :border="true">
				<x-two-columns.column>
					<h3>About Quark Expeditions</h3>
					<p>When you venture into the untouched wilds of the Arctic and Antarctic, you need a great team around you. And we have the greatest. You'll join elite guides trained at Quark Academy — which surpasses every standard imaginable for safety and preparedness in the Polar Regions—and interact directly with world-class polar experts. Our philosophy? The team that explores together, discovers more together.</p>
					<ul>
						<li>In nec mi vitae quam posuere aliquet eget sed leo.</li>
						<li>Sed vel nisi ultricies, sodales risus non, ornare augue.</li>
						<li>Nulla facilisi. Maecenas sit amet porta nulla commodo.</li>
					</ul>
					<x-logo-grid size="large">
						<x-logo-grid.logo image_id="25" size="large"/>
						<x-logo-grid.logo image_id="24" size="large"/>
						<x-logo-grid.logo image_id="21" size="large"/>
						<x-logo-grid.logo image_id="20" size="large"/>
						<x-logo-grid.logo image_id="17" size="large"/>
					</x-logo-grid>
				</x-two-columns.column>
				<x-two-columns.column>
					<h3>What Our Guests Have To Say</h3>
					<x-reviews-carousel>
						<x-reviews-carousel.carousel>
							<x-reviews-carousel.slide
								title="9 Day Spitsbergen Polar Bear Safari."
								author="Carolyn T"
								rating="4"
							>
								<p>Most exciting and wonderful, and educational experience of my life, thank
									you to all the experts for sharing so much information on the animals and
									arctic region, cruise staff and fellow passengers for making this trip one I
									will remember forever. Most exciting and wonderful, and educational experience of my life, thank you to all the experts for sharing so much information on the animals and arctic region, cruise staff and fellow passengers for making this trip one I will remember forever.</p>
							</x-reviews-carousel.slide>
							<x-reviews-carousel.slide
								title="An incredible trip to Antarctica"
								author="Martine S."
								rating="5"
							>
								<p>The whole experience was great. The World Explorer is a beautiful and very comfortable ship, the food was amazing. And of course the landings and cruising the area was a once in a lifetime experience. Most exciting and wonderful, and educational experience of my life, thank you to all the experts for sharing so much information on the animals and arctic region, cruise staff and fellow passengers for making this trip one I will remember forever.</p>
							</x-reviews-carousel.slide>
							<x-reviews-carousel.slide
								title="9 Day Spitsbergen Polar Bear Safari"
								author="Carolyn T"
								rating="4"
							>
								<p>Most exciting and wonderful, and educational experience of my life, thank
									you to all the experts for sharing so much information on the animals and
									arctic region, cruise staff and fellow passengers for making this trip one I
									will remember forever. Most exciting and wonderful, and educational experience of my life, thank you to all the experts for sharing so much information on the animals and arctic region, cruise staff and fellow passengers for making this trip one I will remember forever.</p>
							</x-reviews-carousel.slide>
							<x-reviews-carousel.slide
								title="An incredible trip to Antarctica"
								author="Martine S."
								rating="3.5"
							>
								<p>The whole experience was great. The World Explorer is a beautiful and very comfortable ship, the food was amazing. And of course the landings and cruising the area was a once in a lifetime experience.</p>
							</x-reviews-carousel.slide>
						</x-reviews-carousel.carousel>
					</x-reviews-carousel>
				</x-two-columns.column>
			</x-two-columns>
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'review-cards' ]">
		<x-review-cards>
			<x-review-cards.card
				title="Falkland, South Georgia and the Antarctic Circle"
				author="Denise P."
				rating="4"
			>
				<p>Most exciting and wonderful, and educational experience of my life, thank you to all the experts for sharing so much information on the animals and arctic region, cruise staff and fellow passengers for making this trip one I will remember forever. Most exciting and wonderful, and educational experience of my life, thank you to all the experts for sharing so much information on the animals and arctic region, cruise staff and fellow passengers for making this trip one I will remember forever.</p>
			</x-review-cards.card>
			<x-review-cards.card
				title="An incredible trip to Antarctica"
				author="Martine S."
				rating="5"
			>
				<p>The whole experience was great. The World Explorer is a beautiful and very comfortable ship, the food was amazing. And of course the landings and cruising the area was a once in a lifetime experience. Most exciting and wonderful, and educational experience of my life, thank you to all the experts for sharing so much information on the animals and arctic region, cruise staff and fellow passengers for making this trip one I will remember forever.</p>
			</x-review-cards.card>
			<x-review-cards.card
				title="Wonderful Antarctic Trip"
				author="Roger C."
				rating="4"
			>
				<p>Most exciting and wonderful, and educational experience of my life, thank you to all the experts for sharing so much information on the animals and arctic region.</p>
			</x-review-cards.card>
			<x-review-cards.card
				title="Falkland, South Georgia and Antarctica: Explorers and Kings"
				author="Martine S."
				rating="4"
			>
				<p>The whole experience was great. The World Explorer is a beautiful and very comfortable ship, the food was amazing. And of course the landings and cruising the area was a once in a lifetime experience.</p>
			</x-review-cards.card>
			<x-review-cards.card
				title="9 Day Spitsbergen Polar Bear Safari"
				author="Carolyn T"
				rating="4"
			>
				<p>Most exciting and wonderful, and educational experience of my life, thank you to all the experts for sharing so much information on the animals and arctic region, cruise staff and fellow passengers for making this trip one I will remember forever. Most exciting and wonderful, and educational experience of my life, thank you to all the experts for sharing so much information on the animals and arctic region, cruise staff and fellow passengers for making this trip one I will remember forever.</p>
			</x-review-cards.card>
			<x-review-cards.card
				title="An incredible trip to Antarctica"
				author="Martine S."
				rating="5"
			>
				<p>The whole experience was great. The World Explorer is a beautiful and very comfortable ship, the food was amazing. And of course the landings and cruising the area was a once in a lifetime experience. Most exciting and wonderful, and educational experience of my life, thank you to all the experts for sharing so much information on the animals and arctic region, cruise staff and fellow passengers for making this trip one I will remember forever.</p>
			</x-review-cards.card>
		</x-review-cards>
	</x-component-demo>

	<x-component-demo :keys="['tabs']">
		<x-section>
			<x-tabs current_tab="ultramarine" update_url="yes">
				<x-tabs.header>
					<x-tabs.nav
						id="ultramarine"
						title="Ultramarine"
					/>
					<x-tabs.nav
						id="ocean-explorer"
						title="Ocean Explorer"
					/>
				</x-tabs.header>

				<x-tabs.content>
					<x-tabs.tab id="ultramarine">
						<h3>Ultramarine</h3>
						<p>The 199-guest Ultramarine is equipped with two twin-engine helicopters, 20 quick-launching Zodiacs, spacious suites, wellness amenities, and numerous outdoor wildlife viewing spaces. This 420 ft long ship can cruise at 16 knots in open water and includes four fully enclosed lifeboats.</p>
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aspernatur consequuntur ea ratione sequi? Aperiam asperiores beatae debitis doloribus dolorum earum eveniet excepturi exercitationem ipsum nisi perspiciatis, praesentium provident qui vitae.</p>
					</x-tabs.tab>
					<x-tabs.tab id="ocean-explorer">
						<h3>Ocean Explorer</h3>
						<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).</p>
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aspernatur consequuntur ea ratione sequi? Aperiam asperiores beatae debitis doloribus dolorum earum eveniet excepturi exercitationem ipsum nisi perspiciatis, praesentium provident qui vitae.</p>
					</x-tabs.tab>
				</x-tabs.content>
			</x-tabs>
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'icon-info-columns' ]">
		<x-section :background="true">
			<x-section.heading>
				<x-section.title title="Why Quark Expeditions?" />
			</x-section.heading>
			<x-icon-info-columns>
				<x-icon-info-columns.column>
					<x-icon-info-columns.icon icon="star" />
					<x-icon-info-columns.title title="The Best Expedition Team" />
					<x-icon-info-columns.info>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat</p>
					</x-icon-info-columns.info>
				</x-icon-info-columns.column>
				<x-icon-info-columns.column>
					<x-icon-info-columns.icon icon="compass" />
					<x-icon-info-columns.title title="We Take You Deeper" />
					<x-icon-info-columns.info>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
					</x-icon-info-columns.info>
				</x-icon-info-columns.column>
				<x-icon-info-columns.column>
					<x-icon-info-columns.icon icon="itinerary" />
					<x-icon-info-columns.title title="Most Innovative Itineraries" />
					<x-icon-info-columns.info>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
					</x-icon-info-columns.info>
				</x-icon-info-columns.column>
				<x-icon-info-columns.column>
					<x-icon-info-columns.icon icon="mountains" />
					<x-icon-info-columns.title title="Most Adventure Options Most Adventure" />
					<x-icon-info-columns.info>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
					</x-icon-info-columns.info>
				</x-icon-info-columns.column>
				<x-icon-info-columns.column>
					<x-icon-info-columns.icon icon="ship" />
					<x-icon-info-columns.title title="Small Ships" />
					<x-icon-info-columns.info>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
					</x-icon-info-columns.info>
				</x-icon-info-columns.column>
			</x-icon-info-columns>
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'icon-columns' ]">
		<x-section>
			<x-section.heading>
				<x-section.title title="Icon columns" />
			</x-section.heading>
			<x-icon-columns>
				<x-icon-columns.column>
					<x-icon-columns.icon icon="duotone/person-check" />
					<x-icon-columns.title title="Polar Specialists" />
				</x-icon-columns.column>
				<x-icon-columns.column>
					<x-icon-columns.icon icon="duotone/person-compass" />
					<x-icon-columns.title title="The Best Expedition Team" />
				</x-icon-columns.column>
				<x-icon-columns.column>
					<x-icon-columns.icon icon="duotone/small-ship" />
					<x-icon-columns.title title="Small Ship Experience" />
				</x-icon-columns.column>
				<x-icon-columns.column>
					<x-icon-columns.icon icon="duotone/hiker" />
					<x-icon-columns.title title="The Most Adventure Options" />
				</x-icon-columns.column>
				<x-icon-columns.column>
					<x-icon-columns.icon icon="duotone/stars" />
					<x-icon-columns.title title="4.7 Customer Rating" />
				</x-icon-columns.column>
			</x-icon-columns>
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'video-icons-card' ]">
		<x-section>
			<x-video-icons-card
				url="https://quarkexpeditions.wistia.com/medias/p0k52ec113"
				image_id="26"
				title="The Quark Experience"
				variant="dark"
			>
				<x-video-icons-card.icons>
					<x-icon-columns variant="dark">
						<x-icon-columns.column>
							<x-icon-columns.icon icon="duotone/person-check" />
							<x-icon-columns.title title="Polar Specialists" />
						</x-icon-columns.column>
						<x-icon-columns.column>
							<x-icon-columns.icon icon="duotone/person-compass" />
							<x-icon-columns.title title="The Best Expedition Team" />
						</x-icon-columns.column>
						<x-icon-columns.column>
							<x-icon-columns.icon icon="duotone/small-ship" />
							<x-icon-columns.title title="Small Ship Experience" />
						</x-icon-columns.column>
						<x-icon-columns.column>
							<x-icon-columns.icon icon="duotone/hiker" />
							<x-icon-columns.title title="The Most Adventure Options" />
						</x-icon-columns.column>
						<x-icon-columns.column>
							<x-icon-columns.icon icon="duotone/stars" />
							<x-icon-columns.title title="4.7 Customer Rating" />
						</x-icon-columns.column>
					</x-icon-columns>
				</x-video-icons-card.icons>
			</x-video-icons-card>
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="['itinerary-details']">
		<x-section>
			<x-itinerary-details current_tab="tab-1">
				<x-itinerary-details.tabs-nav>
					<x-itinerary-details.tabs-nav-item id="tab-1">
						<x-itinerary-details.tabs-nav-item-title title="11 days" />
						<x-itinerary-details.tabs-nav-item-subtitle subtitle="From Ushuaia, Argentinian Ultramarine" />
					</x-itinerary-details.tabs-nav-item>
					<x-itinerary-details.tabs-nav-item id="tab-2">
						<x-itinerary-details.tabs-nav-item-title title="12 days" />
						<x-itinerary-details.tabs-nav-item-subtitle subtitle="From Buenos Aires, Argentinian Ultramarine" />
					</x-itinerary-details.tabs-nav-item>
					<x-itinerary-details.tabs-nav-item id="tab-3">
						<x-itinerary-details.tabs-nav-item-title title="12 days" />
						<x-itinerary-details.tabs-nav-item-subtitle subtitle="From Buenos Aires, Argentinian Ultramarine" />
					</x-itinerary-details.tabs-nav-item>
				</x-itinerary-details.tabs-nav>

				<x-itinerary-details.tabs>
					<x-itinerary-details.tab id="tab-1">
						<x-itinerary-details.header title="From Buenos Aires, 12 days, on Ultramarine" />

						<x-itinerary-details.body>
							<x-itinerary-details.summary>
								<x-itinerary-details.summary-content>
									<dl>
										<dt>Duration</dt>
										<dd>11 Days</dd>

										<dt>Departing from</dt>
										<dd>Ushuaia, Argentina</dd>

										<dt>Ship</dt>
										<dd>
											Ultramarine
											<br>
											<a href="#">Learn more about the ship</a>
										</dd>

										<dt>Starting from</dt>
										<dd>$ X,XXX USD per person</dd>
									</dl>
									<x-itinerary-details.download-button url="#" />
								</x-itinerary-details.summary-content>
								<x-itinerary-details.map-lightbox name="map-lightbox" image_id="26" />
							</x-itinerary-details.summary>
							<x-itinerary-details.details>
								Accordion will add here later.<br><br>
								<h5>Day 1: Arrive in Punta Arenas, Chile</h5>
								<p>Lorem ipsum dolor sit amet consectetur. Imperdiet sed quam quis morbi ipsum sed odio. Ut dui mi in sed amet quis porttitor nibh. Ac phasellus sit facilisis vestibulum. Quis luctus ornare tortor justo commodo elementum.</p>
							</x-itinerary-details.details>
						</x-itinerary-details.body>

						<x-itinerary-details.footer>
							<x-itinerary-details.cta>
								<x-button size="big" href="#">Request a Quote</x-button>
								<x-itinerary-details.download-button url="#" />
							</x-itinerary-details.cta>
						</x-itinerary-details.footer>
					</x-itinerary-details.tab>

					<x-itinerary-details.tab id="tab-2">
						<x-itinerary-details.header title="From Buenos Aires, 12 days, on Ultramarine" />

						<x-itinerary-details.body>
							<x-itinerary-details.summary>
								<x-itinerary-details.summary-content>
									<dl>
										<dt>Duration</dt>
										<dd>12 Days</dd>

										<dt>Departing from</dt>
										<dd>Ushuaia, Argentina</dd>

										<dt>Ship</dt>
										<dd>
											Ultramarine
											<br>
											<a href="#">Learn more about the ship</a>
										</dd>

										<dt>Starting from</dt>
										<dd>$ X,XXX USD per person</dd>
									</dl>
									<x-itinerary-details.download-button url="#" />
								</x-itinerary-details.summary-content>
								<x-itinerary-details.map-lightbox name="map-lightbox" image_id="26" />
							</x-itinerary-details.summary>
							<x-itinerary-details.details>
								Accordion will add here later.<br><br>
								<h5>Day 1: Arrive in Punta Arenas, Chile</h5>
								<p>Lorem ipsum dolor sit amet consectetur. Imperdiet sed quam quis morbi ipsum sed odio. Ut dui mi in sed amet quis porttitor nibh. Ac phasellus sit facilisis vestibulum. Quis luctus ornare tortor justo commodo elementum.</p>

							</x-itinerary-details.details>
						</x-itinerary-details.body>

						<x-itinerary-details.footer>
							<x-itinerary-details.cta>
								<x-button size="big" href="#">Request a Quote</x-button>
								<x-itinerary-details.download-button url="#" />
							</x-itinerary-details.cta>
						</x-itinerary-details.footer>
					</x-itinerary-details.tab>

					<x-itinerary-details.tab id="tab-3">
						<x-itinerary-details.header title="From Buenos Aires, 12 days, on Ultramarine" />

						<x-itinerary-details.body>
							<x-itinerary-details.summary>
								<x-itinerary-details.summary-content>
									<dl>
										<dt>Duration</dt>
										<dd>12 Days</dd>

										<dt>Departing from</dt>
										<dd>Ushuaia, Argentina</dd>

										<dt>Ship</dt>
										<dd>
											Ultramarine
											<br>
											<a href="#">Learn more about the ship</a>
										</dd>

										<dt>Starting from</dt>
										<dd>$ X,XXX USD per person</dd>
									</dl>
									<x-itinerary-details.download-button url="#" />
								</x-itinerary-details.summary-content>
								<x-itinerary-details.map-lightbox name="map-lightbox" image_id="26" />
							</x-itinerary-details.summary>
							<x-itinerary-details.details>
								Accordion will add here later.<br><br>
								<h5>Day 1: Arrive in Punta Arenas, Chile</h5>
								<p>Lorem ipsum dolor sit amet consectetur. Imperdiet sed quam quis morbi ipsum sed odio. Ut dui mi in sed amet quis porttitor nibh. Ac phasellus sit facilisis vestibulum. Quis luctus ornare tortor justo commodo elementum.</p>

							</x-itinerary-details.details>
						</x-itinerary-details.body>

						<x-itinerary-details.footer>
							<x-itinerary-details.cta>
								<x-button size="big" href="#">Request a Quote</x-button>
								<x-itinerary-details.download-button url="#" />
							</x-itinerary-details.cta>
						</x-itinerary-details.footer>
					</x-itinerary-details.tab>
				</x-itinerary-details.tabs>
			</x-itinerary-details>
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="['simple-cards']">
		<x-section :background="true" background_color="black">
			<x-section.heading>
				<x-section.title title="Off Ship Adventure" align="left" />
			</x-section.heading>
			<x-simple-cards>
				<x-simple-cards.card image_id="36" title="Camping" url="#" />
				<x-simple-cards.card image_id="34" title="Flightseeing" />
				<x-simple-cards.card image_id="31" title="Heli-hiking" url="#" />
				<x-simple-cards.card image_id="32" title="Sea Kayaking" url="#" />
				<x-simple-cards.card image_id="33" title="Zodiac Cruising" url="#" />
				<x-simple-cards.card image_id="35" title="Wildlife Photography" url="#" />
			</x-simple-cards>
			<x-section.cta class="color-context--dark" text="Learn More" url="#" color="black" />
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="['media-content-card']">
			<x-section>
				<x-section.heading>
					<x-section.title title="Media Content Card 2-column" />
				</x-section.heading>
				<x-media-content-card>
					<x-media-content-card.image image_id="33"/>
					<x-media-content-card.content>
						<x-media-content-card.content-column>
							<h4>Start Your Adventure</h4>
							<p>Call us and one of our Polar Travel Advisors will secure the offer for you.</p>
						</x-media-content-card.content-column>
						<x-media-content-card.content-column>
							<x-media-content-card.content-info
								label="North America (Toll Free)"
								value="+1 (866) 257-3345"
								url="tel:+1 (866) 257-3345"
							/>
							<x-media-content-card.content-info
								label="U.K. (Toll Free)"
								value="0808 134 9986"
								url="tel:0808 134 9986"
							/>
							<x-media-content-card.content-info
								label="Australia (Toll Free)"
								value="+61 1800 959 390"
								url="tel:+61 1800 959 390"
							/>
							<x-media-content-card.content-info
								label="France (Toll Free)"
								value="08 05 08 66 46"
								url="tel:08 05 08 66 46"
							/>
						</x-media-content-card.content-column>
					</x-media-content-card.content>
				</x-media-content-card>
			</x-section>
			<x-section>
				<x-section.heading>
					<x-section.title title="Media Content Card 1-column" />
				</x-section.heading>
				<x-section.description>Call us and one of our Polar Travel Advisors will secure the offer for you.</x-section.description>
				<x-media-content-card :is_compact="true" >
					<x-media-content-card.image image_id="33"/>
					<x-media-content-card.content>
						<x-media-content-card.content-column>
							<h4>Start Your Adventure</h4>
							<p>Call us and one of our Polar Travel Advisors will secure the offer for you.</p>
						</x-media-content-card.content-column>
						<x-media-content-card.content-column>
							<x-media-content-card.content-info label="North America (Toll Free)" value="+1 (866) 257-3345" />
							<x-media-content-card.content-info label="U.K. (Toll Free)" value="0808 134 9986" />
							<x-media-content-card.content-info label="Australia (Toll Free)" value="+61 1800 959 390" />
							<x-media-content-card.content-info label="France (Toll Free)" value="08 05 08 66 46" />
						</x-media-content-card.content-column>
					</x-media-content-card.content>
				</x-media-content-card>
			</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'collage' ]">
		<x-collage name="lp-collage">
			<x-collage.video
				size="medium"
				image_id="33"
				video_url="https://www.youtube.com/embed/KhVseF5ZD5g"
				title="Video Caption"
			/>
			<x-collage.image size="small" image_id="26" title="Image 2" />
			<x-collage.image size="small" image_id="32" title="Image 3" />
			<x-collage.image size="small" image_id="36" title="Image 4" />
			<x-collage.image size="large" image_id="31" title="Image 5" />
			<x-collage.image size="medium" image_id="35" title="Image 6" />
			<x-collage.image size="medium" image_id="30" title="Image 7" />
			<x-collage.image size="x-large" image_id="34" title="Image 8" />
			<x-collage.image size="small" image_id="32" />
			<x-collage.image size="medium" image_id="33" />
			<x-collage.image size="small" image_id="31" title="Image 11" />
		</x-collage>
	</x-component-demo>

	<x-component-demo :keys="[ 'logo-grid', 'feel-safe' ]">
		<x-section :narrow="true">
			<h3 style="text-align: center;">Feel safe with a globally accredited company</h3>
			<p style="text-align: center;">Quark Expeditions is a member of the United States Tour Operators Association and other international accreditation organizations. As a result, you can travel with compete peace of mind since your trip is financially protected.</p>
			<div style="display: flex; flex-direction: column; gap: 50px;">
				<x-logo-grid size="large" alignment="center">
					<x-logo-grid.logo image_id="14" size="large"/>
					<x-logo-grid.logo image_id="13" size="large"/>
				</x-logo-grid>
			</div>
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'expedition-details' ]">
		<x-section>
			<x-two-columns :border="false">
				<x-two-columns.column>
					<x-expedition-details>
						<x-expedition-details.overline
							region="Antarctic"
							duration="11"
							from_price="$ X,XXX USD"
						/>
						<x-expedition-details.title title="Crossing the Circle" />
						<x-expedition-details.regions
							:regions="[
								'Drake Passage',
								'South Shetland Islands',
								'Antarctic Peninsula',
								'Antarctic Circle'
							]"
						/>
						<x-expedition-details.row>
							<x-expedition-details.starting-from
								:starting_from="[
									'Buenos Aires/Ushuaia',
									'Argentina',
								]"
							/>
							<x-expedition-details.ships
								:ships="[
									'Ocean Explorer',
									'Ultramarine',
								]"
							/>
						</x-expedition-details.row>

						<x-expedition-details.row>
							<x-expedition-details.departures
								total_departures="20"
								from_date="November 2024"
								to_date="March 2026"
							/>
						</x-expedition-details.row>

						<x-expedition-details.cta>
							<x-button size="big" color="black">View all Departures</x-button>
						</x-expedition-details.cta>
					</x-expedition-details>
				</x-two-columns.column>

				<x-two-columns.column>
					<x-fancy-video
						url="https://www.youtube.com/embed/0fRAL7xROZg"
						image_id="35"
						title="Interact with fellow travellers in Tundra to Table: Inuit Culinary Experience"
					/>
				</x-two-columns.column>
			</x-two-columns>
		</x-section>

		<x-section full_width="true" seamless="true" background="true" background_color="black">
			<x-two-columns :border="false">
				<x-two-columns.column>
					<x-expedition-details appearance="dark">
						<x-expedition-details.overline
							region="Antarctic"
							duration="11"
							from_price="$ X,XXX USD"
						/>
						<x-expedition-details.title title="Crossing the Circle" />
						<x-expedition-details.regions
							:regions="[
								'Drake Passage',
								'South Shetland Islands',
								'Antarctic Peninsula',
								'Antarctic Circle'
							]"
						/>
						<x-expedition-details.row>
							<x-expedition-details.starting-from
								:starting_from="[
									'Buenos Aires/Ushuaia',
									'Argentina',
								]"
							/>
							<x-expedition-details.ships
								:ships="[
									'Ocean Explorer',
									'Ultramarine',
								]"
							/>
						</x-expedition-details.row>

						<x-expedition-details.row>
							<x-expedition-details.departures
								total_departures="20"
								from_date="November 2024"
								to_date="March 2026"
							/>
						</x-expedition-details.row>

						<x-expedition-details.cta>
							<x-button size="big" color="black">View all Departures</x-button>
						</x-expedition-details.cta>
					</x-expedition-details>
				</x-two-columns.column>

				<x-two-columns.column>
					<x-fancy-video
						url="https://www.youtube.com/embed/0fRAL7xROZg"
						image_id="35"
						title="Interact with fellow travellers in Tundra to Table: Inuit Culinary Experience"
					/>
				</x-two-columns.column>
			</x-two-columns>
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'contact-cover-card' ]">
		<x-section background="true" background_color="black">
			<x-contact-cover-card>
				<x-contact-cover-card.image image_id="34" />
				<x-contact-cover-card.content>
					<x-contact-cover-card.title title="How To Book" />
					<x-contact-cover-card.description>
						<p>Call us and one of our Polar Travel<br /> Advisors will secure the offer for you.</p>
					</x-contact-cover-card.description>
					<x-contact-cover-card.contact-info>
						<x-contact-cover-card.contact-info-item
							label="North America (Toll Free)"
							value="+1 (866) 257-3345"
							url="tel:+1 (866) 257-3345"
						/>
						<x-contact-cover-card.contact-info-item
							label="U.K. (Toll Free)"
							value="0808 134 9986"
							url="tel:0808 134 9986"
						/>
						<x-contact-cover-card.contact-info-item
							label="Australia (Toll Free)"
							value="+61 1800 959 390"
							url="tel:+61 1800 959 390"
						/>
						<x-contact-cover-card.contact-info-item
							label="France (Toll Free)"
							value="08 05 08 66 46"
							url="tel:08 05 08 66 46"
						/>
					</x-contact-cover-card.contact-info>
				</x-contact-cover-card.content>
			</x-contact-cover-card>
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'toast' ]">
		<x-section>
			<x-toast-message message="Lorem ipsum dolor sit ipsum dolor now" :visible="true" />
			<x-toast-message type="error" message="Fields marked with an asterisk (*) are required" :visible="true" />
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'product-departures-card' ]">
		<x-section background="true" background_color="black">
				<x-section.heading>
					<x-section.title title="Upgrade Your Cabin for Freeon select Antarctic 2024 voyages" heading_level="2" />
				</x-section.heading>
			<x-product-departures-card>
				<x-product-departures-card.images :image_ids="[ 32, 34]">
					<x-product-departures-card.badge-cta text="Free Cabin Upgrade" />
				</x-product-departures-card.images>

				<x-product-departures-card.content>
					<x-product-departures-card.title title="Antarctic Explorer: Discovering the 7th Continent" />
					<x-product-departures-card.cta>
						<x-button size="big" icon="phone">Book: +1 (866) 220-1915</x-button>
					</x-product-departures-card.cta>
					<x-product-departures-card.departures>
						<x-product-departures-card.overline text="Departure Dates" />
						<x-product-departures-card.dates>
							<x-product-departures-card.departure-dates>
								<p>Nov 22, 2024</p>
								<p>Nov 23, 2024</p>
							</x-product-departures-card.departure-dates>
							<x-product-departures-card.offer offer="30% Off" offer_text="Save up to $3,700 USD" />
						</x-product-departures-card.dates>
						<x-product-departures-card.dates>
							<x-product-departures-card.departure-dates>
								<p>Nov 16, 2024</p>
							</x-product-departures-card.departure-dates>
							<x-product-departures-card.offer offer="30% Off" offer_text="Save up to $3,700 USD" :sold_out="true" />
						</x-product-departures-card.dates>
					</x-product-departures-card.departures>
				</x-product-departures-card.content>
			</x-product-departures-card>

			<x-product-departures-card>
				<x-product-departures-card.images :image_ids="[ 32, 34]">
					<x-product-departures-card.badge-cta text="Free Cabin Upgrade" />
				</x-product-departures-card.images>

				<x-product-departures-card.content>
					<x-product-departures-card.title title="Antarctic Explorer: Discovering the 7th Continent" />
					<x-product-departures-card.cta>
						<x-button size="big" icon="phone">Book: +1 (866) 220-1915</x-button>
					</x-product-departures-card.cta>
					<x-product-departures-card.departures>
						<x-product-departures-card.overline text="Departure Dates" />
						<x-product-departures-card.dates>
							<x-product-departures-card.departure-dates>
								<p>Nov 22, 2024</p>
								<p>Nov 23, 2024</p>
							</x-product-departures-card.departure-dates>
							<x-product-departures-card.offer offer="30% Off" offer_text="Save up to $3,700 USD" />
						</x-product-departures-card.dates>
						<x-product-departures-card.dates>
							<x-product-departures-card.departure-dates>
								<p>Nov 22, 2024</p>
								<p>Nov 23, 2024</p>
							</x-product-departures-card.departure-dates>
							<x-product-departures-card.offer offer="30% Off" offer_text="Save up to $3,700 USD" />
						</x-product-departures-card.dates>
						<x-product-departures-card.dates>
							<x-product-departures-card.departure-dates>
								<p>Nov 16, 2024</p>
							</x-product-departures-card.departure-dates>
							<x-product-departures-card.offer offer="30% Off" offer_text="Save up to $3,700 USD" :sold_out="true" />
						</x-product-departures-card.dates>
					</x-product-departures-card.departures>
				</x-product-departures-card.content>
			</x-product-departures-card>
		</x-section>
	</x-component-demo>
	<x-component-demo :keys="[ 'fancy-video' ]">
		<x-section>
			<x-two-columns :border="false">
				<x-two-columns.column>
					<x-fancy-video
						url="https://www.youtube.com/embed/0fRAL7xROZg"
						image_id="32"
						title="Hear from fellow solo traveler Charlotte"
					/>
				</x-two-columns.column>

				<x-two-columns.column>
					<x-fancy-video
						url="https://www.youtube.com/embed/0fRAL7xROZg"
						image_id="35"
						title="Interact with fellow travellers in Tundra to Table: Inuit Culinary Experience"
					/>
				</x-two-columns.column>
			</x-two-columns>
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'season-highlights' ]">
		<x-section>
				<x-section.heading>
					<x-section.title title="Best Time to See" align="left" />
				</x-section.heading>
			<x-season-highlights>
				<x-season-highlights.season title="October">
					<x-season-highlights.item title="Penguin Breeding Cycle" :light="true">
						<x-season-highlights.highlight
							icon="court"
							title="Courting"
						/>
					</x-season-highlights.item>
					<x-season-highlights.item title="Highlights">
						<x-season-highlights.highlight
							icon="seal"
							title="Seals"
						/>
						<x-season-highlights.highlight
							icon="elephant-seal"
							title="Elephant Seals"
						/>
						<x-season-highlights.highlight
							icon="seabird"
							title="Seabirds"
						/>
						<x-season-highlights.highlight
							icon="glacier"
							title="Pristine Glaciers"
						/>
					</x-season-highlights.item>
				</x-season-highlights.season>
			</x-season-highlights>
		</x-section>

		<x-section>
				<x-section.heading>
					<x-section.title title="Best Time to See" align="left" />
				</x-section.heading>
			<x-season-highlights>
				<x-season-highlights.season title="October">
					<x-season-highlights.item title="Penguin Breeding Cycle" :light="true">
						<x-season-highlights.highlight
							icon="court"
							title="Courting"
						/>
					</x-season-highlights.item>
					<x-season-highlights.item title="Highlights">
						<x-season-highlights.highlight
							icon="seal"
							title="Seals"
						/>
						<x-season-highlights.highlight
							icon="elephant-seal"
							title="Elephant Seals"
						/>
						<x-season-highlights.highlight
							icon="seabird"
							title="Seabirds"
						/>
						<x-season-highlights.highlight
							icon="glacier"
							title="Pristine Glaciers"
						/>
					</x-season-highlights.item>
				</x-season-highlights.season>
				<x-season-highlights.season title="November">
					<x-season-highlights.item title="Penguin Breeding Cycle" :light="true">
						<x-season-highlights.highlight
							icon="nest"
							title="Nesting"
						/>
					</x-season-highlights.item>
					<x-season-highlights.item title="Highlights">
						<x-season-highlights.highlight
							icon="seal"
							title="Seals"
						/>
						<x-season-highlights.highlight
							icon="elephant-seal"
							title="Elephant Seals"
						/>
						<x-season-highlights.highlight
							icon="seabird"
							title="Seabirds"
						/>
						<x-season-highlights.highlight
							icon="glacier"
							title="Pristine Glaciers"
						/>
					</x-season-highlights.item>
				</x-season-highlights.season>
			</x-season-highlights>
		</x-section>

		<x-section>
				<x-section.heading>
					<x-section.title title="Best Time to See" align="left" />
				</x-section.heading>
			<x-season-highlights>
				<x-season-highlights.season title="October">
					<x-season-highlights.item title="Penguin Breeding Cycle" :light="true">
						<x-season-highlights.highlight
							icon="court"
							title="Courting"
						/>
					</x-season-highlights.item>
					<x-season-highlights.item title="Highlights">
						<x-season-highlights.highlight
							icon="seal"
							title="Seals"
						/>
						<x-season-highlights.highlight
							icon="elephant-seal"
							title="Elephant Seals"
						/>
						<x-season-highlights.highlight
							icon="seabird"
							title="Seabirds"
						/>
						<x-season-highlights.highlight
							icon="glacier"
							title="Pristine Glaciers"
						/>
					</x-season-highlights.item>
				</x-season-highlights.season>
				<x-season-highlights.season title="November">
					<x-season-highlights.item title="Penguin Breeding Cycle" :light="true">
						<x-season-highlights.highlight
							icon="nest"
							title="Nesting"
						/>
					</x-season-highlights.item>
					<x-season-highlights.item title="Highlights">
						<x-season-highlights.highlight
							icon="seal"
							title="Seals"
						/>
						<x-season-highlights.highlight
							icon="elephant-seal"
							title="Elephant Seals"
						/>
						<x-season-highlights.highlight
							icon="seabird"
							title="Seabirds"
						/>
						<x-season-highlights.highlight
							icon="glacier"
							title="Pristine Glaciers"
						/>
					</x-season-highlights.item>
				</x-season-highlights.season>
				<x-season-highlights.season title="December">
					<x-season-highlights.item title="Penguin Breeding Cycle" :light="true">
						<x-season-highlights.highlight
							icon="hatch"
							title="Hatching"
						/>
					</x-season-highlights.item>
					<x-season-highlights.item title="Highlights">
						<x-season-highlights.highlight
							icon="seal"
							title="Seals"
						/>
						<x-season-highlights.highlight
							icon="elephant-seal"
							title="Elephant Seals"
						/>
						<x-season-highlights.highlight
							icon="seabird"
							title="Seabirds"
						/>
						<x-season-highlights.highlight
							icon="glacier"
							title="Pristine Glaciers"
						/>
					</x-season-highlights.item>
				</x-season-highlights.season>
			</x-season-highlights>
		</x-section>

		<x-section>
				<x-section.heading>
					<x-section.title title="Best Time to See" align="left" />
				</x-section.heading>
			<x-season-highlights>
				<x-season-highlights.season title="October">
					<x-season-highlights.item title="Penguin Breeding Cycle" :light="true">
						<x-season-highlights.highlight
							icon="court"
							title="Courting"
						/>
					</x-season-highlights.item>
					<x-season-highlights.item title="Highlights">
						<x-season-highlights.highlight
							icon="seal"
							title="Seals with the longest possible length"
						/>
						<x-season-highlights.highlight
							icon="elephant-seal"
							title="Elephant Seals"
						/>
						<x-season-highlights.highlight
							icon="seabird"
							title="Seabirds"
						/>
						<x-season-highlights.highlight
							icon="glacier"
							title="Pristine Glaciers"
						/>
					</x-season-highlights.item>
				</x-season-highlights.season>
				<x-season-highlights.season title="November">
					<x-season-highlights.item title="Penguin Breeding Cycle" :light="true">
						<x-season-highlights.highlight
							icon="nest"
							title="Nesting"
						/>
					</x-season-highlights.item>
					<x-season-highlights.item title="Highlights">
						<x-season-highlights.highlight
							icon="seal"
							title="Seals"
						/>
						<x-season-highlights.highlight
							icon="elephant-seal"
							title="Elephant Seals"
						/>
						<x-season-highlights.highlight
							icon="seabird"
							title="Seabirds"
						/>
						<x-season-highlights.highlight
							icon="glacier"
							title="Pristine Glaciers"
						/>
					</x-season-highlights.item>
				</x-season-highlights.season>
				<x-season-highlights.season title="December">
					<x-season-highlights.item title="Penguin Breeding Cycle" :light="true">
						<x-season-highlights.highlight
							icon="hatch"
							title="Hatching"
						/>
					</x-season-highlights.item>
					<x-season-highlights.item title="Highlights">
						<x-season-highlights.highlight
							icon="seal"
							title="Seals"
						/>
						<x-season-highlights.highlight
							icon="elephant-seal"
							title="Elephant Seals"
						/>
						<x-season-highlights.highlight
							icon="seabird"
							title="Seabirds"
						/>
						<x-season-highlights.highlight
							icon="glacier"
							title="Pristine Glaciers"
						/>
					</x-season-highlights.item>
				</x-season-highlights.season>
				<x-season-highlights.season title="January-March">
					<x-season-highlights.item title="Penguin Breeding Cycle" :light="true">
						<x-season-highlights.highlight
							icon="penguin-chicks"
							title="Penguin Chicks"
						/>
					</x-season-highlights.item>
					<x-season-highlights.item title="Highlights">
						<x-season-highlights.highlight
							icon="seal"
							title="Seals"
						/>
						<x-season-highlights.highlight
							icon="seabird"
							title="Seabirds"
						/>
						<x-season-highlights.highlight
							icon="whale"
							title="Whales"
						/>
					</x-season-highlights.item>
				</x-season-highlights.season>
			</x-season-highlights>
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'offer-cards' ]">
		<x-offer-cards>
			<x-offer-cards.card>
				<x-offer-cards.heading>BIGGEST SALE OF THE YEAR</x-offer-cards.heading>
				<x-offer-cards.content>
					<x-offer-cards.title title="Up to 50% Off" />
					<x-offer-cards.promotion text="Save up to $14,000" />
					<x-offer-cards.cta>
						<x-button size="big" color="black">View Offers</x-button>
					</x-offer-cards.cta>
					<x-offer-cards.help-text>
						<p>Select Antarctic 2023-24 voyages and Arctic 2024 voyages</p>
					</x-offer-cards.help-text>
				</x-offer-cards.content>
			</x-offer-cards.card>
		</x-offer-cards>

		<x-offer-cards>
			<x-offer-cards.card>
				<x-offer-cards.heading>BIGGEST SALE OF THE YEAR</x-offer-cards.heading>
				<x-offer-cards.content>
					<x-offer-cards.title title="Up to 50% Off" />
					<x-offer-cards.promotion text="Save up to $14,000" />
					<x-offer-cards.cta>
						<x-button size="big" color="black">View Offers</x-button>
					</x-offer-cards.cta>
					<x-offer-cards.help-text>
						<p>Select Antarctic 2023-24 voyages and Arctic 2024 voyages</p>
					</x-offer-cards.help-text>
				</x-offer-cards.content>
			</x-offer-cards.card>
			<x-offer-cards.card>
				<x-offer-cards.heading>BIGGEST SALE OF THE YEAR</x-offer-cards.heading>
				<x-offer-cards.content>
					<x-offer-cards.title title="Free Cabin Upgrades" />
					<x-offer-cards.promotion text="Save up to $3,700" />
					<x-offer-cards.cta>
						<x-button size="big" color="black">View Offers</x-button>
					</x-offer-cards.cta>
					<x-offer-cards.help-text>
						<p>Select Antarctic 2024-25 season"</p>
					</x-offer-cards.help-text>
				</x-offer-cards.content>
			</x-offer-cards.card>
		</x-offer-cards>

		<x-offer-cards>
			<x-offer-cards.card>
				<x-offer-cards.heading>BIGGEST SALE OF THE YEAR</x-offer-cards.heading>
				<x-offer-cards.content>
					<x-offer-cards.title title="Up to 50% Off" />
					<x-offer-cards.promotion text="Save up to $14,000" />
					<x-offer-cards.cta>
						<x-button size="big" color="black">View Offers</x-button>
					</x-offer-cards.cta>
					<x-offer-cards.help-text>
						<p>Select Antarctic 2023-24 voyages and Arctic 2024 voyages</p>
					</x-offer-cards.help-text>
				</x-offer-cards.content>
			</x-offer-cards.card>
			<x-offer-cards.card>
				<x-offer-cards.heading>BIGGEST SALE OF THE YEAR</x-offer-cards.heading>
				<x-offer-cards.content>
					<x-offer-cards.title title="Free Cabin Upgrades" />
					<x-offer-cards.promotion text="Save up to $3,700" />
					<x-offer-cards.cta>
						<x-button size="big" color="black">View Offers</x-button>
					</x-offer-cards.cta>
					<x-offer-cards.help-text>
						<p>Select Antarctic 2024-25 season"</p>
					</x-offer-cards.help-text>
				</x-offer-cards.content>
			</x-offer-cards.card>
			<x-offer-cards.card>
				<x-offer-cards.heading>BIGGEST SALE OF THE YEAR</x-offer-cards.heading>
				<x-offer-cards.content>
					<x-offer-cards.title title="Up to 50% Off" />
					<x-offer-cards.promotion text="Save up to $14,000" />
					<x-offer-cards.cta>
						<x-button size="big" color="black">View Offers</x-button>
					</x-offer-cards.cta>
					<x-offer-cards.help-text>
						<p>Select Antarctic 2023-24 voyages and Arctic 2024 voyages</p>
					</x-offer-cards.help-text>
				</x-offer-cards.content>
			</x-offer-cards.card>
		</x-offer-cards>
	</x-component-demo>
	<x-component-demo :keys="[ 'media-text-cta' ]">
		<x-media-text-cta>
			<x-media-text-cta.image image_id="32">
				<x-media-text-cta.badge text="Featured Expedition" />
			</x-media-text-cta.image>

			<x-media-text-cta.content>
				<h2>South Georgia and Antarctic Peninsula: Penguin Safari</h2>
				<p>This is the fastest way to visit both the Antarctic Peninsula and remote, wildlife rich South Georgia, where the beaches are teaming with King penguins and elephant seals. From here you sail south where Antarctica awaits with its soaring peaks and staggering expanse.</p>
				<x-media-text-cta.secondary-text text="16/18 days | Starting from $12,946 USD" />
				<x-media-text-cta.cta>
					<x-button size="big" color="black">Request a Quote</x-button>
				</x-media-text-cta.cta>
			</x-media-text-cta.content>
		</x-media-text-cta>

		<x-media-text-cta media_align="right">
			<x-media-text-cta.video>
				<x-fancy-video url="https://www.youtube.com/embed/0fRAL7xROZg" image_id="32" />
			</x-media-text-cta.video>

			<x-media-text-cta.content>
				<h2>Falklands, South Georgia, and Antarctica: Explorers & Kings</h2>
				<p>The quickest way to get to the rarely visited Falkland Islands and South Georgia before stepping foot on the 7th Continent. Both islands are known as meccas for wildlife with epic displays of animals congregating by the thousands. Then, explore the stunning Antarctic Peninsula and enter a world of ice, snow and natural wonders.</p>
				<x-media-text-cta.secondary-text text="20 days | Starting From $14,621 USD" />
				<x-media-text-cta.cta>
					<x-button size="big" color="black">Request a Quote</x-button>
				</x-media-text-cta.cta>
			</x-media-text-cta.content>
		</x-media-text-cta>

		<x-media-text-cta>
			<x-media-text-cta.image image_id="32">
				<x-media-text-cta.badge text="Featured Expedition" />
			</x-media-text-cta.image>

			<x-media-text-cta.content>
				<h2>Epic Antarctica: Crossing the Circle via Falklands & South Georgia</h2>
				<p>Our Epic Antarctica voyage is called so for a reason—it includes all of the major Antarctic highlights you can imagine. </p>
				<x-media-text-cta.secondary-text text="23 days | Starting From $26,979 USD" />
				<x-media-text-cta.cta>
					<x-button size="big" color="black">Request a Quote</x-button>
				</x-media-text-cta.cta>
			</x-media-text-cta.content>
		</x-media-text-cta>
	</x-component-demo>

	<x-component-demo :keys="[ 'lp-footer', 'logo-grid' ]">
		<x-lp-footer>
			<x-lp-footer.row>
				<x-lp-footer.column url="tel:+1(866)241-1602">
					<x-lp-footer.icon name="call" />
					<p>Need help planning? Call Us.</p>
					<h5>+1 (866) 241-1602</h5>
				</x-lp-footer.column>
				<x-lp-footer.column url="https://www.quarkexpeditions.com/brochures">
					<x-lp-footer.icon name="brochure" />
					<p>Get Quark Expeditions</p>
					<h5>Arctic & Antarctic Brochures</h5>
				</x-lp-footer.column>
				<x-lp-footer.column url="https://www.quarkexpeditions.com/subscribe-to-our-newsletter">
					<x-lp-footer.icon name="mail" />
					<p>Sign up for our</p>
					<h5>Newsletters & Offers</h5>
				</x-lp-footer.column>
			</x-lp-footer.row>
			<x-lp-footer.row>
				<x-lp-footer.column>
					<h5>Featured on:</h5>
					<x-logo-grid alignment="center">
						<x-logo-grid.logo image_id="22"/>
						<x-logo-grid.logo image_id="23"/>
						<x-logo-grid.logo image_id="19"/>
					</x-logo-grid>
				</x-lp-footer.column>
				<x-lp-footer.column>
					<ul>
						<li><a href="#">Terms of Use</a></li>
						<li><a href="#">Privacy Policy</a></li>
						<li>All rights reserved @ 2024</li>
					</ul>
				</x-lp-footer.column>
				<x-lp-footer.column>
					<p>Quark Expeditions</p>
					<p>112 Merton St, Toronto ON, Canada.</p>
					<x-lp-footer.social-links>
						<x-lp-footer.social-link type="facebook" url="#" />
						<x-lp-footer.social-link type="instagram" url="#" />
						<x-lp-footer.social-link type="twitter" url="#" />
						<x-lp-footer.social-link type="youtube" url="#" />
					</x-lp-footer.social-links>
				</x-lp-footer.column>
			</x-lp-footer.row>
		</x-lp-footer>
	</x-component-demo>

	<x-component-demo :keys="[ 'lp-offer-masthead' ]">
		<x-lp-offer-masthead>
			<x-lp-offer-masthead.image image_id="35" />
			<x-lp-offer-masthead.content>
				<x-lp-offer-masthead.logo image_id="48" />
				<x-lp-offer-masthead.offer-image image_id="47" />
				<x-lp-offer-masthead.caption>
					<p>New voyages added and Black Friday Sale extended to December 5th, 2023!</p>
				</x-lp-offer-masthead.caption>

				<x-lp-offer-masthead.inner-content>
					<x-offer-cards>
						<x-offer-cards.card>
							<x-offer-cards.heading>BIGGEST SALE OF THE YEAR</x-offer-cards.heading>
							<x-offer-cards.content>
								<x-offer-cards.title title="Up to 50% Off" />
								<x-offer-cards.promotion text="Save up to $14,000" />
								<x-offer-cards.cta>
									<x-button size="big" color="black">View Offers</x-button>
								</x-offer-cards.cta>
								<x-offer-cards.help-text>
									<p>Select Antarctic 2023-24 voyages and Arctic 2024 voyages</p>
								</x-offer-cards.help-text>
							</x-offer-cards.content>
						</x-offer-cards.card>
						<x-offer-cards.card>
							<x-offer-cards.heading>BIGGEST SALE OF THE YEAR</x-offer-cards.heading>
							<x-offer-cards.content>
								<x-offer-cards.title title="Free Cabin Upgrades" />
								<x-offer-cards.promotion text="Save up to $3,700" />
								<x-offer-cards.cta>
									<x-button size="big" color="black">View Offers</x-button>
								</x-offer-cards.cta>
								<x-offer-cards.help-text>
									<p>Select Antarctic 2024-25 season"</p>
								</x-offer-cards.help-text>
							</x-offer-cards.content>
						</x-offer-cards.card>
					</x-offer-cards>
				</x-lp-offer-masthead.inner-content>
			</x-lp-offer-masthead.content>
		</x-lp-offer-masthead>

		<x-section background="true" background_color="black">
			<x-section.heading>
				<x-section.title title="Our Biggest Savings! 50% off these Antarctic 2024 Voyages" />
			</x-section.heading>
			<x-product-cards>
				<x-product-cards.card url="#">
					<x-product-cards.image
						image_id="29"
					>
						<x-product-cards.badge-cta text="Save 50%" />
					</x-product-cards.image>
					<x-product-cards.reviews
						total_reviews="19 Reviews"
						review_rating="5"
					/>
					<x-product-cards.itinerary departure_date="Departing May 20, 2024" duration="10 Days" />
					<x-product-cards.title title="Introduction to Spitsbergen" />
					<x-product-cards.subtitle title="Fjords, Glaciers, and Wildlife of Svalbard" />
					<x-product-cards.description>
						<p>This fascinating expedition provides a taste of everything Spitsbergen has to offer!</p>
					</x-product-cards.description>
					<x-product-cards.price
						original_price="$7,395 USD"
						discounted_price="$6,171 USD"
					/>
					<x-product-cards.buttons :columns="2">
						<x-button size="big">Request a Quote</x-button>
						<x-button size="big" appearance="outline">Learn More</x-button>
					</x-product-cards.buttons>
				</x-product-cards.card>

				<x-product-cards.card url="#">
					<x-product-cards.image
						image_id="36"
						:is_immersive="false"
					>
						<x-product-cards.badge-cta text="Save 50%" />
						<x-product-cards.badge-time text="Just Added" />
					</x-product-cards.image>
					<x-product-cards.reviews
						total_reviews="9999 Reviews"
						review_rating="3"
					/>
					<x-product-cards.itinerary departure_date="Departing May 28, 2024" duration="12 Days" />
					<x-product-cards.title title="Spitsbergen Explorer" />
					<x-product-cards.subtitle title="Wildlife Capital of the Arctic" />
					<x-product-cards.description>
						<p>Witness the remarkable array of creatures who call this spectacular environment home.</p>
					</x-product-cards.description>
					<x-product-cards.price
						original_price="$9,095 USD"
						discounted_price="$7,361 USD"
					/>
					<x-product-cards.buttons :columns="2">
						<x-button size="big" icon="phone">Book: +1 (866) 220-1915</x-button>
					</x-product-cards.buttons>
				</x-product-cards.card>

				<x-product-cards.card url="#">
					<x-product-cards.image
						image_id="32"
						:is_immersive="false"
					>
						<x-product-cards.badge-time text="Just Added" />
					</x-product-cards.image>
					<x-product-cards.reviews
						total_reviews="100 Reviews"
						review_rating="4"
					/>
					<x-product-cards.itinerary departure_date="Departing July 14, 2024" duration="11 Days" />
					<x-product-cards.title title="Gems of West Greenland" />
					<x-product-cards.subtitle title="Fjords, Icebergs, and Culture" />
					<x-product-cards.description>
						<p>Features the best sites of West Greenland & delivers an in-depth experience in just 12 days.</p>
					</x-product-cards.description>
					<x-product-cards.price
						original_price="$9,395 USD"
						discounted_price="$8,571 USD"
					/>
					<x-product-cards.buttons :columns="2">
						<x-button size="big">Request a Quote</x-button>
						<x-button size="big" appearance="outline">Learn More</x-button>
					</x-product-cards.buttons>
				</x-product-cards.card>
			</x-product-cards>
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'sidebar-grid', 'table-of-contents', 'breadcrumbs', 'post-author-info' ]">
		<x-breadcrumbs
			:breadcrumbs="[
				[
					'title' => 'Home',
					'url'   => '#',
				],
				[
					'title' => 'Blog',
					'url'   => '#',
				],
				[
					'title' => 'Antarctica',
					'url'   => '#',
				],
				[
					'title' => 'Can I Travel Antarctica?',
					'url'   => '#',
				],
			]"
		/>
		<x-sidebar-grid>
			<x-sidebar-grid.content>
				<h1>Can I Travel to Antarctica?</h1>
				<x-post-author-info>
					<x-post-author-info.image :image_id="18" />
					<x-post-author-info.info>
						<x-post-author-info.name title="Daven Hafey" />
						<x-post-author-info.read-time duration="11" />
					</x-post-author-info.info>
				</x-post-author-info>
				<p>Antarctica. The 7th Continent. The land mass at the bottom of the globe, completely encased in ice. Its remote wilderness featured on nature documentaries and in our science and history books. And for those reasons, it might seem somewhat abstract and hard to reach. Perhaps even impossible. But it's not.
				When I tell people what I do for a living—working on an expedition ship that takes passengers from all over the world to Antarctica—I often hear a similar response, “I had no idea people can actually go to Antarctica!” And I always reply with: “Yes, people go. And so can you!”</p>
				<x-image
					style="border-radius: 16px;"
					:image_id="33"
					:args="[
						'size' =>       [
							'width'   => 1280,
							'height'  => 720,
						],
					]"
				/>
				<p>Despite its seemingly impossible remoteness, Antarctica has never been so accessible to adventurous travelers. In fact, more than 45,000 people traveled to Antarctica in the 2016-2017 season from all over the world, including the United States, China, Australia, Germany, Canada, the United Kingdom, France, India, and many, many more.
				As the timeless idiom indicates: knowing is half the battle. Throughout this piece, I hope to convey the knowledge that not only can you visit Antarctica from almost anywhere in the world, but just as importantly,how you can do it.</p>
				<h2 id="you-can-travel-antarctica">You can travel to Antarctica! Here's how.</h2>
				<p>
					A common question I hear from people whose excitement is palpable after they've learned how accessible Antarctica can be, is: “How do I even get there?” That answer might not immediately seem straightforward. Maybe some of us have heard of researchers flying on military aircraft from New Zealand, or month long sailing adventures from South Africa or Australia. But the easiest way to get to Antarctica is simple. All you need to do is reach Buenos Aires, Argentina or Punta Arenas, Chile. Both are cosmopolitan cities with international airports and regular service to the rest of the world.
					The majority of Antarctic voyages depart from Ushuaia, Argentina, a three-and-a-half-hour direct flight from Buenos Aires. Throughout the summer, the Port of Ushuaia embarks and disembarks expedition vessels bound for the southern wilderness as seamlessly as any harbor in the Virgin Islands, the Mediterranean, or Alaska.
					Voyages departing from Ushuaia, Argentina access Antarctica by sea. They traverse the infamous Drake Passage, a 600-mile (1,000 kilometer) body of water that separates South America from the Antarctic Peninsula. Depending upon conditions, this crossing often takes a day and a half at sea and is a prime opportunity to view iconic wildlife such as the great wandering albatross.
					Alternatively, travelers preferring to skip the Drake Passage can fly out of Punta Arenas, Chile directly to an airstrip on an island adjacent to the Antarctic Peninsula. From there, they'll board the expedition ship and be standing face to face with glaciers and penguins just a few hours after departing Punta Arenas.
				</p>
				<x-image
					style="border-radius: 16px;"
					:image_id="33"
					:args="[
						'size' =>       [
							'width'   => 1280,
							'height'  => 720,
						],
					]"
				/>
				<h2 id="when-can-travel-antarctica">When can I travel to Antarctica?</h2>
				<p>
					The best time to visit Antarctica is from late spring to early fall, which in the southern hemisphere is from October to March. The first voyages of the season reach Antarctica in late spring (end of October or early November) when the sea ice opens up just enough to allow ships into the pristine glacial landscapes. Voyages operate continually from late October, until the summer comes to an end, and the wonderfully powerful Antarctic autumn begins to arrive by the middle of March.
					Learn more about the unique highlights of visiting Antarctica during its different seasons here.
				</p>
				<h2 id="how-long-antarctic-expedition">How long is an Antarctic expedition?</h2>
				<p>
					When browsing information about your Antarctic expedition, you'll find different types of “itineraries,” or sailing plans. These itineraries aren't concrete, per se, but guides that will shape the direction and the duration of each voyage. Among these, you will find expeditions that travel directly to Antarctica, and others that include the spectacular sub-Antarctic regions of the Falkland Islands (Islas Malvinas) and South Georgia.
					There are a wide range of options for visiting Antarctica that can suit your schedule, from “express” expeditions with flights to the Antarctic Peninsula that get you to the continent and back in as quick as eight days, to epic explorations of sub-Antarctic islands and the continent itself, lasting three weeks or more.
					The most common expeditions last approximately nine to ten days, including five full days of exploration in Antarctica. Rather than fly from South America, these voyages embrace the power and the beauty of the Drake Passage (and its rich and abundant bird life), sailing from Ushuaia. Time spent at sea varies depending on sea conditions and wind, but often take from one and a half to two days at sea, each way. The rest of the voyage is spent in the seemingly endless coastal environment of the Antarctic Peninsula.
					For those with extended holidays and a thirst for a deeper exploration of this remote wilderness, there are expeditions that spend twenty or more days exploring in the Southern Ocean and its unique islands. These extended voyages include visits to the wildlife-rich Falkland Islands and the otherworldly wilderness of South Georgia, in addition to the days spent in the Antarctic Peninsula, making these expeditions the most thorough exploration of the wild environments at the bottom of the globe.
				</p>
				<h2 id="cruise">How is an expedition to Antarctica different from a cruise?</h2>
				<p>
					When browsing information about your Antarctic expedition, you'll find different types of “itineraries,” or sailing plans. These itineraries aren't concrete, per se, but guides that will shape the direction and the duration of each voyage. Among these, you will find expeditions that travel directly to Antarctica, and others that include the spectacular sub-Antarctic regions of the Falkland Islands (Islas Malvinas) and South Georgia.
					There are a wide range of options for visiting Antarctica that can suit your schedule, from “express” expeditions with flights to the Antarctic Peninsula that get you to the continent and back in as quick as eight days, to epic explorations of sub-Antarctic islands and the continent itself, lasting three weeks or more.
					The most common expeditions last approximately nine to ten days, including five full days of exploration in Antarctica. Rather than fly from South America, these voyages embrace the power and the beauty of the Drake Passage (and its rich and abundant bird life), sailing from Ushuaia. Time spent at sea varies depending on sea conditions and wind, but often take from one and a half to two days at sea, each way. The rest of the voyage is spent in the seemingly endless coastal environment of the Antarctic Peninsula.
					For those with extended holidays and a thirst for a deeper exploration of this remote wilderness, there are expeditions that spend twenty or more days exploring in the Southern Ocean and its unique islands. These extended voyages include visits to the wildlife-rich Falkland Islands and the otherworldly wilderness of South Georgia, in addition to the days spent in the Antarctic Peninsula, making these expeditions the most thorough exploration of the wild environments at the bottom of the globe.
				</p>
				<h2 id="what-can-do-antarctica">What can I do while in Antarctica?</h2>
				<p>
					The best time to visit Antarctica is from late spring to early fall, which in the southern hemisphere is from October to March. The first voyages of the season reach Antarctica in late spring (end of October or early November) when the sea ice opens up just enough to allow ships into the pristine glacial landscapes. Voyages operate continually from late October, until the summer comes to an end, and the wonderfully powerful Antarctic autumn begins to arrive by the middle of March.
					Learn more about the unique highlights of visiting Antarctica during its different seasons here.
				</p>
				<h4>Stand-up Paddleboarding in Antarctica</h4>
				<x-image
					style="border-radius: 16px;"
					:image_id="32"
					:args="[
						'size' =>       [
							'width'   => 1280,
							'height'  => 720,
						],
					]"
				/>
				<p>
					Although Antarctica has a reputation for being fiercely cold, in the summertime, the Antarctic Peninsula can be quite inviting! So inviting, that under the right conditions, visitors can navigate icy bays by stand-up paddleboard (SUP). Many people often associate SUP boarding with the tropics, but it can actually be the perfect fit for an intimate Antarctic moment. Paddling through a quiet, isolated cove can be one of the best ways to move the body while taking in the sounds of porpoising penguins and the crackling of bits and pieces of glaciers floating nearby. The waters of Antarctica are teaming with life, and intimate encounters with penguins, whales and seals gliding beneath or near your board are not uncommon.
				</p>
			</x-sidebar-grid.content>
			<x-sidebar-grid.sidebar :sticky="true" :show_on_mobile="false">
				<x-table-of-contents
					title="In this article"
					:contents="[
						[
							'title'  => 'You can travel to Antarctica! Here\'s how.',
							'anchor' => 'you-can-travel-antarctica',
						],
						[
							'title'  => 'When can I travel to Antarctica?',
							'anchor' => 'when-can-travel-antarctica',
						],
						[
							'title'  => 'How long is an Antarctic expedition?',
							'anchor' => 'how-long-antarctic-expedition',
						],
						[
							'title'  => 'How is an expedition to Antarctica different from a cruise?',
							'anchor' => 'cruise',
						],
						[
							'title'  => 'What can I do while in Antarctica?',
							'anchor' => 'what-can-do-antarctica',
						],
						[
							'title'  => 'Are all trips the same? How do I choose the best itinerary for me?',
							'anchor' => 'best-itinerary',
						],
						[
							'title'  => 'Why Visit Antarctica?',
							'anchor' => 'why-visit',
						],
						[
							'title'  => 'Yes, you can visit Antarctica!',
							'anchor' => 'can-visit-antarctica',
						],
					]"
				/>
			</x-sidebar-grid.sidebar>
		</x-sidebar-grid>
		<x-section background="true" background_color="black">
			<x-section.heading>
				<x-section.title title="Our Biggest Savings! 50% off these Antarctic 2024 Voyages" />
			</x-section.heading>
			<x-product-cards>
				<x-product-cards.card url="#">
					<x-product-cards.image
						image_id="29"
					>
						<x-product-cards.badge-cta text="Save 50%" />
					</x-product-cards.image>
					<x-product-cards.reviews
						total_reviews="19 Reviews"
						review_rating="5"
					/>
					<x-product-cards.itinerary departure_date="Departing May 20, 2024" duration="10 Days" />
					<x-product-cards.title title="Introduction to Spitsbergen" />
					<x-product-cards.subtitle title="Fjords, Glaciers, and Wildlife of Svalbard" />
					<x-product-cards.description>
						<p>This fascinating expedition provides a taste of everything Spitsbergen has to offer!</p>
					</x-product-cards.description>
					<x-product-cards.price
						original_price="$7,395 USD"
						discounted_price="$6,171 USD"
					/>
					<x-product-cards.buttons :columns="2">
						<x-button size="big">Request a Quote</x-button>
						<x-button size="big" appearance="outline">Learn More</x-button>
					</x-product-cards.buttons>
				</x-product-cards.card>

				<x-product-cards.card url="#">
					<x-product-cards.image
						image_id="36"
						:is_immersive="false"
					>
						<x-product-cards.badge-cta text="Save 50%" />
						<x-product-cards.badge-time text="Just Added" />
					</x-product-cards.image>
					<x-product-cards.reviews
						total_reviews="9999 Reviews"
						review_rating="3"
					/>
					<x-product-cards.itinerary departure_date="Departing May 28, 2024" duration="12 Days" />
					<x-product-cards.title title="Spitsbergen Explorer" />
					<x-product-cards.subtitle title="Wildlife Capital of the Arctic" />
					<x-product-cards.description>
						<p>Witness the remarkable array of creatures who call this spectacular environment home.</p>
					</x-product-cards.description>
					<x-product-cards.price
						original_price="$9,095 USD"
						discounted_price="$7,361 USD"
					/>
					<x-product-cards.buttons :columns="2">
						<x-button size="big" icon="phone">Book: +1 (866) 220-1915</x-button>
					</x-product-cards.buttons>
				</x-product-cards.card>

				<x-product-cards.card url="#">
					<x-product-cards.image
						image_id="32"
						:is_immersive="false"
					>
						<x-product-cards.badge-time text="Just Added" />
					</x-product-cards.image>
					<x-product-cards.reviews
						total_reviews="100 Reviews"
						review_rating="4"
					/>
					<x-product-cards.itinerary departure_date="Departing July 14, 2024" duration="11 Days" />
					<x-product-cards.title title="Gems of West Greenland" />
					<x-product-cards.subtitle title="Fjords, Icebergs, and Culture" />
					<x-product-cards.description>
						<p>Features the best sites of West Greenland & delivers an in-depth experience in just 12 days.</p>
					</x-product-cards.description>
					<x-product-cards.price
						original_price="$9,395 USD"
						discounted_price="$8,571 USD"
					/>
					<x-product-cards.buttons :columns="2">
						<x-button size="big">Request a Quote</x-button>
						<x-button size="big" appearance="outline">Learn More</x-button>
					</x-product-cards.buttons>
				</x-product-cards.card>
			</x-product-cards>
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'product-cards' ]">
		<x-product-cards align="center">
			<x-product-cards.card url="#">
				<x-product-cards.image
					image_id="29"
					:is_immersive="true"
				>
					<x-product-cards.badge-cta text="Save 50%" />
					<x-product-cards.badge-time text="Just Added" />
				</x-product-cards.image>
				<x-product-cards.reviews
					total_reviews="19 Reviews"
					review_rating="5"
				/>
				<x-product-cards.itinerary departure_date="Departing May 20, 2024" duration="10 Days" />
				<x-product-cards.title title="Introduction to Spitsbergen" />
				<x-product-cards.subtitle title="Fjords, Glaciers, and Wildlife of Svalbard" />
				<x-product-cards.description>
					<p>This fascinating expedition provides a taste of everything Spitsbergen has to offer!</p>
				</x-product-cards.description>
				<x-product-cards.price
					original_price="$7,395 USD"
					discounted_price="$6,171 USD"
				/>
				<x-product-cards.buttons :columns="1">
					<x-button size="big">Request a Quote</x-button>
					<x-button size="big" appearance="outline">Learn More</x-button>
				</x-product-cards.buttons>
			</x-product-cards.card>

			<x-product-cards.card url="#">
				<x-product-cards.image
					image_id="36"
					:is_immersive="false"
				>
					<x-product-cards.badge-sold-out />
					<x-product-cards.badge-time text="Just Added" />
					<x-product-cards.info-ribbon>Additional 10% savings text</x-product-cards.info-ribbon>
				</x-product-cards.image>
				<x-product-cards.reviews
					total_reviews="9999 Reviews"
					review_rating="3"
				/>
				<x-product-cards.itinerary departure_date="Departing May 28, 2024" duration="12 Days" />
				<x-product-cards.title title="Spitsbergen Explorer" />
				<x-product-cards.subtitle title="Wildlife Capital of the Arctic" />
				<x-product-cards.description>
					<p>Witness the remarkable array of creatures who call this spectacular environment home.</p>
				</x-product-cards.description>
				<x-product-cards.price
					original_price="$9,095 USD"
					discounted_price="$7,361 USD"
				/>
				<x-product-cards.buttons :columns="2">
					<x-button size="big" icon="phone">Book: +1 (866) 220-1915</x-button>
				</x-product-cards.buttons>
			</x-product-cards.card>
		</x-product-cards>

		<x-product-cards>
			<x-product-cards.card url="#">
				<x-product-cards.image
					image_id="29"
				>
					<x-product-cards.badge-cta text="Save 50%" />
				</x-product-cards.image>
				<x-product-cards.reviews
					total_reviews="19 Reviews"
					review_rating="5"
				/>
				<x-product-cards.itinerary departure_date="Departing May 20, 2024" duration="10 Days" />
				<x-product-cards.title title="Introduction to Spitsbergen" />
				<x-product-cards.subtitle title="Fjords, Glaciers, and Wildlife of Svalbard" />
				<x-product-cards.description>
					<p>This fascinating expedition provides a taste of everything Spitsbergen has to offer!</p>
				</x-product-cards.description>
				<x-product-cards.price
					original_price="$7,395 USD"
					discounted_price="$6,171 USD"
				/>
				<x-product-cards.buttons :columns="2">
					<x-button size="big">Request a Quote</x-button>
					<x-button size="big" appearance="outline">Learn More</x-button>
				</x-product-cards.buttons>
			</x-product-cards.card>

			<x-product-cards.card url="#">
				<x-product-cards.image
					image_id="36"
					:is_immersive="false"
				>
					<x-product-cards.badge-cta text="Save 50%" />
					<x-product-cards.badge-time text="Just Added" />
				</x-product-cards.image>
				<x-product-cards.reviews
					total_reviews="9999 Reviews"
					review_rating="3"
				/>
				<x-product-cards.itinerary departure_date="Departing May 28, 2024" duration="12 Days" />
				<x-product-cards.title title="Spitsbergen Explorer" />
				<x-product-cards.subtitle title="Wildlife Capital of the Arctic" />
				<x-product-cards.description>
					<p>Witness the remarkable array of creatures who call this spectacular environment home.</p>
				</x-product-cards.description>
				<x-product-cards.price
					original_price="$9,095 USD"
					discounted_price="$7,361 USD"
				/>
				<x-product-cards.buttons :columns="2">
					<x-button size="big" icon="phone">Book: +1 (866) 220-1915</x-button>
				</x-product-cards.buttons>
			</x-product-cards.card>

			<x-product-cards.card url="#">
				<x-product-cards.image
					image_id="32"
					:is_immersive="false"
				>
					<x-product-cards.badge-time text="Just Added" />
				</x-product-cards.image>
				<x-product-cards.reviews
					total_reviews="100 Reviews"
					review_rating="4"
				/>
				<x-product-cards.itinerary departure_date="Departing July 14, 2024" duration="11 Days" />
				<x-product-cards.title title="Gems of West Greenland" />
				<x-product-cards.subtitle title="Fjords, Icebergs, and Culture" />
				<x-product-cards.description>
					<p>Features the best sites of West Greenland & delivers an in-depth experience in just 12 days.</p>
				</x-product-cards.description>
				<x-product-cards.price
					original_price="$9,395 USD"
					discounted_price="$8,571 USD"
				/>
				<x-product-cards.buttons :columns="2">
					<x-button size="big">Request a Quote</x-button>
					<x-button size="big" appearance="outline">Learn More</x-button>
				</x-product-cards.buttons>
			</x-product-cards.card>
		</x-product-cards>
	</x-component-demo>

	<x-component-demo :keys="[ 'section-updated' ]">
		<x-section :background="true" :seamless="true">
			<x-section.heading>
				<x-section.title title="Check out these offers" align="left" />
				<x-section.heading-link url="#">See All</x-section.heading-link>
			</x-section.heading>
			<x-product-cards>
				<x-product-cards.card url="#">
					<x-product-cards.image
						image_id="29"
					>
						<x-product-cards.badge-cta text="Save 50%" />
					</x-product-cards.image>
					<x-product-cards.reviews
						total_reviews="19 Reviews"
						review_rating="5"
					/>
					<x-product-cards.itinerary departure_date="Departing May 20, 2024" duration="10 Days" />
					<x-product-cards.title title="Introduction to Spitsbergen" />
					<x-product-cards.subtitle title="Fjords, Glaciers, and Wildlife of Svalbard" />
					<x-product-cards.description>
						<p>This fascinating expedition provides a taste of everything Spitsbergen has to offer!</p>
					</x-product-cards.description>
					<x-product-cards.price
						original_price="$7,395 USD"
						discounted_price="$6,171 USD"
					/>
					<x-product-cards.buttons :columns="2">
						<x-button size="big">Request a Quote</x-button>
						<x-button size="big" appearance="outline">Learn More</x-button>
					</x-product-cards.buttons>
				</x-product-cards.card>
				<x-product-cards.card url="#">
					<x-product-cards.image
						image_id="36"
						:is_immersive="false"
					>
						<x-product-cards.badge-cta text="Save 50%" />
						<x-product-cards.badge-time text="Just Added" />
					</x-product-cards.image>
					<x-product-cards.reviews
						total_reviews="9999 Reviews"
						review_rating="3"
					/>
					<x-product-cards.itinerary departure_date="Departing May 28, 2024" duration="12 Days" />
					<x-product-cards.title title="Spitsbergen Explorer" />
					<x-product-cards.subtitle title="Wildlife Capital of the Arctic" />
					<x-product-cards.description>
						<p>Witness the remarkable array of creatures who call this spectacular environment home.</p>
					</x-product-cards.description>
					<x-product-cards.price
						original_price="$9,095 USD"
						discounted_price="$7,361 USD"
					/>
					<x-product-cards.buttons :columns="2">
						<x-button size="big" icon="phone">Book: +1 (866) 220-1915</x-button>
					</x-product-cards.buttons>
				</x-product-cards.card>
				<x-product-cards.card url="#">
					<x-product-cards.image
						image_id="32"
						:is_immersive="false"
					>
						<x-product-cards.badge-time text="Just Added" />
					</x-product-cards.image>
					<x-product-cards.reviews
						total_reviews="100 Reviews"
						review_rating="4"
					/>
					<x-product-cards.itinerary departure_date="Departing July 14, 2024" duration="11 Days" />
					<x-product-cards.title title="Gems of West Greenland" />
					<x-product-cards.subtitle title="Fjords, Icebergs, and Culture" />
					<x-product-cards.description>
						<p>Features the best sites of West Greenland & delivers an in-depth experience in just 12 days.</p>
					</x-product-cards.description>
					<x-product-cards.price
						original_price="$9,395 USD"
						discounted_price="$8,571 USD"
					/>
					<x-product-cards.buttons :columns="2">
						<x-button size="big">Request a Quote</x-button>
						<x-button size="big" appearance="outline">Learn More</x-button>
					</x-product-cards.buttons>
				</x-product-cards.card>
			</x-product-cards>
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'thumbnail-cards' ]">
		<x-section title="Thumbnail Cards: Small Portrait">
			<x-thumbnail-cards :is_carousel="false">
				<x-thumbnail-cards.card size="small" url="#" orientation="portrait" image_id="29">
					<x-thumbnail-cards.title title="Arctic Expeditions" align="top" />
				</x-thumbnail-cards.card>
				<x-thumbnail-cards.card size="small" url="#" orientation="portrait" image_id="30">
					<x-thumbnail-cards.title title="Antarctic Expeditions" align="bottom" />
				</x-thumbnail-cards.card>
				<x-thumbnail-cards.card size="small" url="#" orientation="portrait" image_id="33">
					<x-thumbnail-cards.title title="Patagonia Expeditions" align="top" />
				</x-thumbnail-cards.card>
			</x-thumbnail-cards>
		</x-section>
		<x-section title="Thumbnail Cards: Small Landscape">
			<x-thumbnail-cards :is_carousel="false">
				<x-thumbnail-cards.card size="small" url="#" orientation="landscape" image_id="29">
					<x-thumbnail-cards.title title="Arctic Expeditions" align="top" />
				</x-thumbnail-cards.card>
				<x-thumbnail-cards.card size="small" url="#" orientation="landscape" image_id="30">
					<x-thumbnail-cards.title title="Antarctic Expeditions" align="bottom" />
				</x-thumbnail-cards.card>
				<x-thumbnail-cards.card size="small" url="#" orientation="landscape" image_id="33">
					<x-thumbnail-cards.title title="Patagonia Expeditions" align="top" />
				</x-thumbnail-cards.card>
				<x-thumbnail-cards.card size="small" url="#" orientation="landscape" image_id="34">
					<x-thumbnail-cards.title title="Patagonia Expeditions" align="bottom" />
				</x-thumbnail-cards.card>
			</x-thumbnail-cards>
		</x-section>
		<x-section title="Thumbnail Cards: Medium Portrait">
			<x-thumbnail-cards :is_carousel="false">
				<x-thumbnail-cards.card size="medium" url="#" orientation="portrait" image_id="29">
					<x-thumbnail-cards.title title="Arctic Expeditions" align="top" />
				</x-thumbnail-cards.card>
				<x-thumbnail-cards.card size="medium" url="#" orientation="portrait" image_id="30">
					<x-thumbnail-cards.title title="Antarctic Expeditions" align="bottom" />
				</x-thumbnail-cards.card>
				<x-thumbnail-cards.card size="medium" url="#" orientation="portrait" image_id="33">
					<x-thumbnail-cards.title title="Patagonia Expeditions" align="top" />
				</x-thumbnail-cards.card>
				<x-thumbnail-cards.card size="medium" url="#" orientation="portrait" image_id="34">
					<x-thumbnail-cards.title title="Patagonia Expeditions" align="bottom" />
				</x-thumbnail-cards.card>
			</x-thumbnail-cards>
		</x-section>
		<x-section title="Thumbnail Cards: Medium Landscape">
			<x-thumbnail-cards :is_carousel="true">
				<x-thumbnail-cards.card size="medium" url="#" orientation="landscape" image_id="29">
					<x-thumbnail-cards.title title="Arctic Expeditions" align="bottom" />
				</x-thumbnail-cards.card>
				<x-thumbnail-cards.card size="medium" url="#" orientation="landscape" image_id="30">
					<x-thumbnail-cards.title title="Antarctic Expeditions" align="top" />
				</x-thumbnail-cards.card>
				<x-thumbnail-cards.card size="medium" url="#" orientation="landscape" image_id="33">
					<x-thumbnail-cards.title title="Patagonia Expeditions" align="bottom" />
				</x-thumbnail-cards.card>
				<x-thumbnail-cards.card size="medium" url="#" orientation="landscape" image_id="34">
					<x-thumbnail-cards.title title="Patagonia Expeditions" align="top" />
				</x-thumbnail-cards.card>
			</x-thumbnail-cards>
		</x-section>
		<x-section title="Thumbnail Cards: Large">
			<x-thumbnail-cards :is_carousel="true">
				<x-thumbnail-cards.card size="large" url="#" orientation="portrait" image_id="29">
					<x-thumbnail-cards.title title="Arctic Expeditions" align="top" />
				</x-thumbnail-cards.card>
				<x-thumbnail-cards.card size="large" url="#" orientation="portrait" image_id="30">
					<x-thumbnail-cards.title title="Antarctic Expeditions" align="bottom" />
				</x-thumbnail-cards.card>
				<x-thumbnail-cards.card size="large" url="#" orientation="portrait" image_id="33">
					<x-thumbnail-cards.title title="Patagonia Expeditions" align="top" />
				</x-thumbnail-cards.card>
				<x-thumbnail-cards.card size="large" url="#" orientation="portrait" image_id="34">
					<x-thumbnail-cards.title title="Patagonia Expeditions" align="bottom" />
				</x-thumbnail-cards.card>
				<x-thumbnail-cards.card size="large" url="#" orientation="portrait" image_id="35">
					<x-thumbnail-cards.title title="Patagonia Expeditions" align="bottom" />
				</x-thumbnail-cards.card>
			</x-thumbnail-cards>
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'accordion' ]">
		<x-section>
			<x-accordion title="Quark Expeditions takes you places no one else can!">
				<x-accordion.item>
					<x-accordion.item-handle title="Destinations" />
					<x-accordion.item-content>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Magnis dis parturient montes nascetur ridiculus mus mauris. Pharetra pharetra massa massa ultricies mi quis hendrerit dolor. Aliquam nulla facilisi cras fermentum odio. Dolor sit amet consectetur adipiscing elit pellentesque habitant.</p>
					</x-accordion.item-content>
				</x-accordion.item>
				<x-accordion.item>
					<x-accordion.item-handle title="Expeditions" />
					<x-accordion.item-content>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Magnis dis parturient montes nascetur ridiculus mus mauris. Pharetra pharetra massa massa ultricies mi quis hendrerit dolor. Aliquam nulla facilisi cras fermentum odio. Dolor sit amet consectetur adipiscing elit pellentesque habitant.it further to meet your schedule, interests, and budget with one of our expert Travel Consultants.</p>
					</x-accordion.item-content>
				</x-accordion.item>
				<x-accordion.item>
					<x-accordion.item-handle title="Ships" />
					<x-accordion.item-content>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Magnis dis parturient montes nascetur ridiculus mus mauris. Pharetra pharetra massa massa ultricies mi quis hendrerit dolor. Aliquam nulla facilisi cras fermentum odio. Dolor sit amet consectetur adipiscing elit pellentesque habitant.</p>
					</x-accordion.item-content>
				</x-accordion.item>
				<x-accordion.item>
					<x-accordion.item-handle title="Offers" />
					<x-accordion.item-content>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Magnis dis parturient montes nascetur ridiculus mus mauris. Pharetra pharetra massa massa ultricies mi quis hendrerit dolor. Aliquam nulla facilisi cras fermentum odio. Dolor sit amet consectetur adipiscing elit pellentesque habitant.</p>
					</x-accordion.item-content>
				</x-accordion.item>
				<x-accordion.item>
					<x-accordion.item-handle title="About Us" />
					<x-accordion.item-content>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Magnis dis parturient montes nascetur ridiculus mus mauris. Pharetra pharetra massa massa ultricies mi quis hendrerit dolor. Aliquam nulla facilisi cras fermentum odio. Dolor sit amet consectetur adipiscing elit pellentesque habitant.</p>
					</x-accordion.item-content>
				</x-accordion.item>
			</x-accordion>
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'accordion' ]">
		<x-section title="What should I know before booking a polar expedition?" heading_level="2" title_align="left">
			<x-accordion title="Quark Expeditions takes you places no one else can!" :full_border="true">
				<x-accordion.item>
					<x-accordion.item-handle title="What are the Health and Safety requirements for expedition travel?" />
					<x-accordion.item-content>
						<ul>
							<li>Lorem ipsum dolor sit amet consectetur adipisicing elit.</li>
							<li>Temporibus, aperiam. Error provident pariatur explicabo, totam culpa quam dolores quisquam, doloremque perspiciatis consequatur recusandae ipsam a facere eos? Aspernatur.</li>
						</ul>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Magnis dis parturient montes nascetur ridiculus mus mauris. Pharetra pharetra massa massa ultricies mi quis hendrerit dolor. Aliquam nulla facilisi cras fermentum odio. Dolor sit amet consectetur adipiscing elit pellentesque habitant. <a href=>See FAQs for details</a></p>
					</x-accordion.item-content>
				</x-accordion.item>
				<x-accordion.item>
					<x-accordion.item-handle title="What are the Terms & Conditions for booking?" />
					<x-accordion.item-content>
						<ul>
							<li>Lorem ipsum dolor sit amet consectetur adipisicing elit.</li>
							<li>Temporibus, aperiam. Error provident pariatur explicabo, totam culpa quam dolores quisquam, doloremque perspiciatis consequatur recusandae ipsam a facere eos? Aspernatur.</li>
						</ul>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Magnis dis parturient montes nascetur ridiculus mus mauris. Pharetra pharetra massa massa ultricies mi quis hendrerit dolor. Aliquam nulla facilisi cras fermentum odio. Dolor sit amet consectetur adipiscing elit pellentesque habitant.</p>
					</x-accordion.item-content>
				</x-accordion.item>
				<x-accordion.item>
					<x-accordion.item-handle title="What is Quark Expeditions' Protection Promise to make your expedition worry-free?" />
					<x-accordion.item-content>
						<ul>
							<li>Lorem ipsum dolor sit amet consectetur adipisicing elit.</li>
							<li>Temporibus, aperiam. Error provident pariatur explicabo, totam culpa quam dolores quisquam, doloremque perspiciatis consequatur recusandae ipsam a facere eos? Aspernatur.</li>
						</ul>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Magnis dis parturient montes nascetur ridiculus mus mauris. Pharetra pharetra massa massa ultricies mi quis hendrerit dolor. Aliquam nulla facilisi cras fermentum odio. Dolor sit amet consectetur adipiscing elit pellentesque habitant.</p>
					</x-accordion.item-content>
				</x-accordion.item>
			</x-accordion>
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'breadcrumbs' ]">
		<x-breadcrumbs
			:breadcrumbs="[
				[
					'title' => 'Home',
					'url'   => '#',
				],
				[
					'title' => 'Blog',
					'url'   => '#',
				],
				[
					'title' => 'Antarctica',
					'url'   => '#',
				],
				[
					'title' => 'Can I Travel Antarctica?',
					'url'   => '#',
				],
			]"
		/>

		<x-breadcrumbs
			:breadcrumbs="[
				[
					'title' => 'Home',
					'url'   => '#',
				],
				[
					'title' => 'Destinations',
					'url'   => '#',
				],
				[
					'title' => 'Antarctica',
					'url'   => '#',
				],
				[
					'title' => 'Expeditions',
					'url'   => '#',
				],
				[
					'title' => 'Current Expedition',
					'url'   => '#',
				],
				[
					'title' => 'Preparation',
					'url'   => '#',
				],
				[
					'title' => 'Packing List',
					'url'   => '#',
				],
				[
					'title' => 'Arctic Gear',
					'url'   => '#',
				],
				[
					'title' => 'Activities',
					'url'   => '#',
				],
				[
					'title' => 'Iceberg Watching',
					'url'   => '#',
				],
				[
					'title' => 'Wildlife Encounters',
					'url'   => '#',
				],
				[
					'title' => 'Polar Plunge',
					'url'   => '#',
				],
				[
					'title' => 'Travel Guide',
					'url'   => '#',
				],
				[
					'title' => 'Visa Requirements',
					'url'   => '#',
				],
				[
					'title' => 'Health and Safety',
					'url'   => '#',
				],
				[
					'title' => 'Emergency Procedures',
					'url'   => '#',
				],
				[
					'title' => 'Booking',
					'url'   => '#',
				],
				[
					'title' => 'Reservation Form',
					'url'   => '#',
				],
				[
					'title' => 'Payment Options',
					'url'   => '#',
				],
				[
					'title' => 'Contact Us',
					'url'   => '#',
				],
			]"
		/>
	</x-component-demo>

	<x-component-demo :keys="[ 'info-cards' ]">
		<x-section title="Info Cards: Regular">
			<x-info-cards>
				<x-info-cards.card size="big" url="#">
					<x-info-cards.image image_id="29" />
					<x-info-cards.content position="bottom">
						<x-info-cards.overline>3 mins read</x-info-cards.overline>
						<x-info-cards.title title="Chasing Shackleton: Chasing Polar Dreams" />
						<x-info-cards.description>
							<p>
								Antarctica. The 7th Continent. The land mass at the bottom of the globe, completely encased in ice. Its remote wilderness featured on nature documentaries and in our science and history books. And..
							</p>
						</x-info-cards.description>
						<x-info-cards.cta text="Read Post" />
					</x-info-cards.content>
				</x-info-cards.card>
				<x-info-cards.card url="#">
					<x-info-cards.image image_id="30" />
					<x-info-cards.content position="bottom">
						<x-info-cards.overline>8 mins read</x-info-cards.overline>
						<x-info-cards.title title="Wildlife Guide: Emperor Penguin Facts" />
						<x-info-cards.cta text="Read Post" />
					</x-info-cards.content>
				</x-info-cards.card>
				<x-info-cards.card url="#">
					<x-info-cards.image image_id="33" />
					<x-info-cards.content position="bottom">
						<x-info-cards.overline>6 mins read</x-info-cards.overline>
						<x-info-cards.title title="Wildlife Guide: Cormorant Facts" />
						<x-info-cards.cta text="Read Post" />
					</x-info-cards.content>
				</x-info-cards.card>
				<x-info-cards.card url="#">
					<x-info-cards.image image_id="34" />
					<x-info-cards.content position="bottom">
						<x-info-cards.overline>8 mins read</x-info-cards.overline>
						<x-info-cards.title title="Wildlife Guide: Ptarmigan Facts" />
						<x-info-cards.cta text="Read Post" />
					</x-info-cards.content>
				</x-info-cards.card>
				<x-info-cards.card url="#">
					<x-info-cards.image image_id="35" />
					<x-info-cards.content position="bottom">
						<x-info-cards.overline>6 mins read</x-info-cards.overline>
						<x-info-cards.title title="Wildlife Guide: Sperm Whale Facts" />
						<x-info-cards.cta text="Read Post" />
					</x-info-cards.content>
				</x-info-cards.card>
			</x-info-cards>
		</x-section>

		<x-section title="Info Cards: Carousel">
			<x-info-cards layout="carousel">
				<x-info-cards.card size="big" url="#">
					<x-info-cards.image image_id="29" />
					<x-info-cards.content position="bottom">
						<x-info-cards.overline>3 mins read</x-info-cards.overline>
						<x-info-cards.title title="Chasing Shackleton: Chasing Polar Dreams" />
						<x-info-cards.description>
							<p>
								Antarctica. The 7th Continent. The land mass at the bottom of the globe, completely encased in ice. Its remote wilderness featured on nature documentaries and in our science and history books. And..
							</p>
						</x-info-cards.description>
						<x-info-cards.cta text="Read Post" />
					</x-info-cards.content>
				</x-info-cards.card>
				<x-info-cards.card url="#">
					<x-info-cards.image image_id="30" />
					<x-info-cards.content position="bottom">
						<x-info-cards.overline>8 mins read</x-info-cards.overline>
						<x-info-cards.title title="Wildlife Guide: Emperor Penguin Facts" />
						<x-info-cards.cta text="Read Post" />
					</x-info-cards.content>
				</x-info-cards.card>
				<x-info-cards.card url="#">
					<x-info-cards.image image_id="33" />
					<x-info-cards.content position="bottom">
						<x-info-cards.overline>6 mins read</x-info-cards.overline>
						<x-info-cards.title title="Wildlife Guide: Cormorant Facts" />
						<x-info-cards.cta text="Read Post" />
					</x-info-cards.content>
				</x-info-cards.card>
				<x-info-cards.card url="#">
					<x-info-cards.image image_id="34" />
					<x-info-cards.content position="bottom">
						<x-info-cards.overline>8 mins read</x-info-cards.overline>
						<x-info-cards.title title="Wildlife Guide: Ptarmigan Facts" />
						<x-info-cards.cta text="Read Post" />
					</x-info-cards.content>
				</x-info-cards.card>
				<x-info-cards.card url="#">
					<x-info-cards.image image_id="35" />
					<x-info-cards.content position="bottom">
						<x-info-cards.overline>6 mins read</x-info-cards.overline>
						<x-info-cards.title title="Wildlife Guide: Sperm Whale Facts" />
						<x-info-cards.cta text="Read Post" />
					</x-info-cards.content>
				</x-info-cards.card>
			</x-info-cards>
		</x-section>

		<x-section title="Info Cards: Collage">
			<x-info-cards layout="collage" :mobile_carousel="false">
				<x-info-cards.card size="big" url="#">
					<x-info-cards.image image_id="29" />
					<x-info-cards.content position="top">
						<x-info-cards.tag text="webinar"/>
						<x-info-cards.overline>3 mins read</x-info-cards.overline>
						<x-info-cards.title title="Chasing Shackleton: Chasing Polar Dreams" />
						<x-info-cards.description>
							<p>
								Antarctica. The 7th Continent. The land mass at the bottom of the globe, completely encased in ice. Its remote wilderness featured on nature documentaries and in our science and history books. And..
							</p>
						</x-info-cards.description>
						<x-info-cards.cta text="Read Post" />
					</x-info-cards.content>
				</x-info-cards.card>
				<x-info-cards.card url="#">
					<x-info-cards.image image_id="30" />
					<x-info-cards.content position="bottom">
						<x-info-cards.tag text="webinar"/>
						<x-info-cards.overline>8 mins read</x-info-cards.overline>
						<x-info-cards.title title="Wildlife Guide: Emperor Penguin Facts" />
						<x-info-cards.cta text="Read Post" />
					</x-info-cards.content>
				</x-info-cards.card>
				<x-info-cards.card url="#">
					<x-info-cards.image image_id="33" />
					<x-info-cards.content position="bottom">
						<x-info-cards.overline>6 mins read</x-info-cards.overline>
						<x-info-cards.title title="Wildlife Guide: Cormorant Facts" />
						<x-info-cards.cta text="Read Post" />
					</x-info-cards.content>
				</x-info-cards.card>
				<x-info-cards.card url="#">
					<x-info-cards.image image_id="34" />
					<x-info-cards.content position="bottom">
						<x-info-cards.overline>8 mins read</x-info-cards.overline>
						<x-info-cards.title title="Wildlife Guide: Ptarmigan Facts" />
						<x-info-cards.cta text="Read Post" />
					</x-info-cards.content>
				</x-info-cards.card>
				<x-info-cards.card url="#">
					<x-info-cards.image image_id="35" />
					<x-info-cards.content position="bottom">
						<x-info-cards.overline>6 mins read</x-info-cards.overline>
						<x-info-cards.title title="Wildlife Guide: Sperm Whale Facts" />
						<x-info-cards.cta text="Read Post" />
					</x-info-cards.content>
				</x-info-cards.card>
			</x-info-cards>
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'thumbnail-cards' ]">
		<x-section title="Thumbnail Cards: Small Portrait">
			<x-thumbnail-cards :is_carousel="false">
				<x-thumbnail-cards.card size="small" url="#" orientation="portrait" image_id="29">
					<x-thumbnail-cards.title title="Arctic Expeditions" align="top" />
				</x-thumbnail-cards.card>
				<x-thumbnail-cards.card size="small" url="#" orientation="portrait" image_id="30">
					<x-thumbnail-cards.title title="Antarctic Expeditions" align="bottom" />
				</x-thumbnail-cards.card>
				<x-thumbnail-cards.card size="small" url="#" orientation="portrait" image_id="33">
					<x-thumbnail-cards.title title="Patagonia Expeditions" align="top" />
				</x-thumbnail-cards.card>
			</x-thumbnail-cards>
		</x-section>
		<x-section title="Thumbnail Cards: Small Landscape">
			<x-thumbnail-cards :is_carousel="false">
				<x-thumbnail-cards.card size="small" url="#" orientation="landscape" image_id="29">
					<x-thumbnail-cards.title title="Arctic Expeditions" align="top" />
				</x-thumbnail-cards.card>
				<x-thumbnail-cards.card size="small" url="#" orientation="landscape" image_id="30">
					<x-thumbnail-cards.title title="Antarctic Expeditions" align="bottom" />
				</x-thumbnail-cards.card>
				<x-thumbnail-cards.card size="small" url="#" orientation="landscape" image_id="33">
					<x-thumbnail-cards.title title="Patagonia Expeditions" align="top" />
				</x-thumbnail-cards.card>
				<x-thumbnail-cards.card size="small" url="#" orientation="landscape" image_id="34">
					<x-thumbnail-cards.title title="Patagonia Expeditions" align="bottom" />
				</x-thumbnail-cards.card>
			</x-thumbnail-cards>
		</x-section>
		<x-section title="Thumbnail Cards: Medium Portrait">
			<x-thumbnail-cards :is_carousel="false">
				<x-thumbnail-cards.card size="medium" url="#" orientation="portrait" image_id="29">
					<x-thumbnail-cards.title title="Arctic Expeditions" align="top" />
				</x-thumbnail-cards.card>
				<x-thumbnail-cards.card size="medium" url="#" orientation="portrait" image_id="30">
					<x-thumbnail-cards.title title="Antarctic Expeditions" align="bottom" />
				</x-thumbnail-cards.card>
				<x-thumbnail-cards.card size="medium" url="#" orientation="portrait" image_id="33">
					<x-thumbnail-cards.title title="Patagonia Expeditions" align="top" />
				</x-thumbnail-cards.card>
				<x-thumbnail-cards.card size="medium" url="#" orientation="portrait" image_id="34">
					<x-thumbnail-cards.title title="Patagonia Expeditions" align="bottom" />
				</x-thumbnail-cards.card>
			</x-thumbnail-cards>
		</x-section>
		<x-section title="Thumbnail Cards: Medium Landscape">
			<x-thumbnail-cards :is_carousel="true">
				<x-thumbnail-cards.card size="medium" url="#" orientation="landscape" image_id="29">
					<x-thumbnail-cards.title title="Arctic Expeditions" align="bottom" />
				</x-thumbnail-cards.card>
				<x-thumbnail-cards.card size="medium" url="#" orientation="landscape" image_id="30">
					<x-thumbnail-cards.title title="Antarctic Expeditions" align="top" />
				</x-thumbnail-cards.card>
				<x-thumbnail-cards.card size="medium" url="#" orientation="landscape" image_id="33">
					<x-thumbnail-cards.title title="Patagonia Expeditions" align="bottom" />
				</x-thumbnail-cards.card>
				<x-thumbnail-cards.card size="medium" url="#" orientation="landscape" image_id="34">
					<x-thumbnail-cards.title title="Patagonia Expeditions" align="top" />
				</x-thumbnail-cards.card>
			</x-thumbnail-cards>
		</x-section>
		<x-section title="Thumbnail Cards: Large">
			<x-thumbnail-cards :is_carousel="true">
				<x-thumbnail-cards.card size="large" url="#" orientation="portrait" image_id="29">
					<x-thumbnail-cards.title title="Arctic Expeditions" align="top" />
				</x-thumbnail-cards.card>
				<x-thumbnail-cards.card size="large" url="#" orientation="portrait" image_id="30">
					<x-thumbnail-cards.title title="Antarctic Expeditions" align="bottom" />
				</x-thumbnail-cards.card>
				<x-thumbnail-cards.card size="large" url="#" orientation="portrait" image_id="33">
					<x-thumbnail-cards.title title="Patagonia Expeditions" align="top" />
				</x-thumbnail-cards.card>
				<x-thumbnail-cards.card size="large" url="#" orientation="portrait" image_id="34">
					<x-thumbnail-cards.title title="Patagonia Expeditions" align="bottom" />
				</x-thumbnail-cards.card>
				<x-thumbnail-cards.card size="large" url="#" orientation="portrait" image_id="35">
					<x-thumbnail-cards.title title="Patagonia Expeditions" align="bottom" />
				</x-thumbnail-cards.card>
			</x-thumbnail-cards>
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'accordion' ]">
		<x-section>
			<x-accordion title="Quark Expeditions takes you places no one else can!">
				<x-accordion.item>
					<x-accordion.item-handle title="Destinations" />
					<x-accordion.item-content>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Magnis dis parturient montes nascetur ridiculus mus mauris. Pharetra pharetra massa massa ultricies mi quis hendrerit dolor. Aliquam nulla facilisi cras fermentum odio. Dolor sit amet consectetur adipiscing elit pellentesque habitant.</p>
					</x-accordion.item-content>
				</x-accordion.item>
				<x-accordion.item>
					<x-accordion.item-handle title="Expeditions" />
					<x-accordion.item-content>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Magnis dis parturient montes nascetur ridiculus mus mauris. Pharetra pharetra massa massa ultricies mi quis hendrerit dolor. Aliquam nulla facilisi cras fermentum odio. Dolor sit amet consectetur adipiscing elit pellentesque habitant.it further to meet your schedule, interests, and budget with one of our expert Travel Consultants.</p>
					</x-accordion.item-content>
				</x-accordion.item>
				<x-accordion.item>
					<x-accordion.item-handle title="Ships" />
					<x-accordion.item-content>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Magnis dis parturient montes nascetur ridiculus mus mauris. Pharetra pharetra massa massa ultricies mi quis hendrerit dolor. Aliquam nulla facilisi cras fermentum odio. Dolor sit amet consectetur adipiscing elit pellentesque habitant.</p>
					</x-accordion.item-content>
				</x-accordion.item>
				<x-accordion.item>
					<x-accordion.item-handle title="Offers" />
					<x-accordion.item-content>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Magnis dis parturient montes nascetur ridiculus mus mauris. Pharetra pharetra massa massa ultricies mi quis hendrerit dolor. Aliquam nulla facilisi cras fermentum odio. Dolor sit amet consectetur adipiscing elit pellentesque habitant.</p>
					</x-accordion.item-content>
				</x-accordion.item>
				<x-accordion.item>
					<x-accordion.item-handle title="About Us" />
					<x-accordion.item-content>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Magnis dis parturient montes nascetur ridiculus mus mauris. Pharetra pharetra massa massa ultricies mi quis hendrerit dolor. Aliquam nulla facilisi cras fermentum odio. Dolor sit amet consectetur adipiscing elit pellentesque habitant.</p>
					</x-accordion.item-content>
				</x-accordion.item>
			</x-accordion>
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'tooltip' ]">
		<x-section title="Tooltips">
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Magnis dis parturient montes nascetur ridiculus mus mauris. Pharetra pharetra massa massa ultricies mi quis hendrerit dolor. Aliquam nulla facilisi cras fermentum odio. Dolor sit amet consectetur adipiscing elit pellentesque habitant.</p>
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Magnis dis parturient montes nascetur ridiculus mus mauris. Pharetra pharetra massa massa ultricies mi quis hendrerit dolor. Aliquam nulla facilisi cras fermentum odio. Dolor sit amet consectetur adipiscing elit pellentesque habitant.</p>
			<div>
				<strong>What areas do you have work experience in?</strong>
				<x-tooltip icon="info">
					<p>Hiking, kayaking, mountain biking, mountaineering, etc.</p>
				</x-tooltip>
			</div>

			<div>
				<strong>Do you have a university degree (or higher) in any of the following subjects?</strong>
				<x-tooltip icon="info">
					<p>SVOP, RYA or higher</p>
				</x-tooltip>
			</div>
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'accordion' ]">
		<x-section title="What should I know before booking a polar expedition?" heading_level="2" title_align="left">
			<x-accordion title="Quark Expeditions takes you places no one else can!" :full_border="true">
				<x-accordion.item>
					<x-accordion.item-handle title="What are the Health and Safety requirements for expedition travel?" />
					<x-accordion.item-content>
						<ul>
							<li>Lorem ipsum dolor sit amet consectetur adipisicing elit.</li>
							<li>Temporibus, aperiam. Error provident pariatur explicabo, totam culpa quam dolores quisquam, doloremque perspiciatis consequatur recusandae ipsam a facere eos? Aspernatur.</li>
						</ul>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Magnis dis parturient montes nascetur ridiculus mus mauris. Pharetra pharetra massa massa ultricies mi quis hendrerit dolor. Aliquam nulla facilisi cras fermentum odio. Dolor sit amet consectetur adipiscing elit pellentesque habitant. <a href=>See FAQs for details</a></p>
					</x-accordion.item-content>
				</x-accordion.item>
				<x-accordion.item>
					<x-accordion.item-handle title="What are the Terms & Conditions for booking?" />
					<x-accordion.item-content>
						<ul>
							<li>Lorem ipsum dolor sit amet consectetur adipisicing elit.</li>
							<li>Temporibus, aperiam. Error provident pariatur explicabo, totam culpa quam dolores quisquam, doloremque perspiciatis consequatur recusandae ipsam a facere eos? Aspernatur.</li>
						</ul>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Magnis dis parturient montes nascetur ridiculus mus mauris. Pharetra pharetra massa massa ultricies mi quis hendrerit dolor. Aliquam nulla facilisi cras fermentum odio. Dolor sit amet consectetur adipiscing elit pellentesque habitant.</p>
					</x-accordion.item-content>
				</x-accordion.item>
				<x-accordion.item>
					<x-accordion.item-handle title="What is Quark Expeditions' Protection Promise to make your expedition worry-free?" />
					<x-accordion.item-content>
						<ul>
							<li>Lorem ipsum dolor sit amet consectetur adipisicing elit.</li>
							<li>Temporibus, aperiam. Error provident pariatur explicabo, totam culpa quam dolores quisquam, doloremque perspiciatis consequatur recusandae ipsam a facere eos? Aspernatur.</li>
						</ul>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Magnis dis parturient montes nascetur ridiculus mus mauris. Pharetra pharetra massa massa ultricies mi quis hendrerit dolor. Aliquam nulla facilisi cras fermentum odio. Dolor sit amet consectetur adipiscing elit pellentesque habitant.</p>
					</x-accordion.item-content>
				</x-accordion.item>
			</x-accordion>
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'lp-footer' ]">
		<x-lp-footer>
			<x-lp-footer.row>
				<x-lp-footer.column>
					<p>&copy; 2023 Quark Expeditions&reg; Inc.</p>
					<x-lp-footer.social-links>
						<x-lp-footer.social-link type="facebook" url="#" />
						<x-lp-footer.social-link type="instagram" url="#" />
						<x-lp-footer.social-link type="twitter" url="#" />
						<x-lp-footer.social-link type="youtube" url="#" />
					</x-lp-footer.social-links>
				</x-lp-footer.column>
			</x-lp-footer.row>
		</x-lp-footer>
	</x-component-demo>

	<x-component-demo :keys="[ 'footer' ]">
		@php
			$social_links = [
				'facebook'  => 'https://www.facebook.com/',
				'instagram' => 'https://www.instagram.com/',
				'twitter'   => 'https://www.twitter.com/',
				'youtube'   => 'https://www.youtube.com/',
			];
		@endphp

		<x-footer>
			<x-footer.top>
				<x-footer.column url="tel:+1(866)241-1602">
					<x-footer.icon name="call" />
					<p>Need help planning? Call Us.</p>
					<h5>+1 (866) 241-1602</h5>
				</x-footer.column>

				<x-footer.column url="https://www.quarkexpeditions.com/brochures">
					<x-footer.icon name="article" />
					<p>Get Quark Expeditions</p>
					<h5>Arctic & Antarctic Brochures</h5>
				</x-footer.column>

				<x-footer.column url="https://www.quarkexpeditions.com/subscribe-to-our-newsletter">
					<x-footer.icon name="mail" />
					<p>Sign up for our</p>
					<h5>Newsletters & Offers</h5>
				</x-footer.column>
			</x-footer.top>

			<x-footer.middle>
				<x-footer.column>
					<x-footer.logo />
					<x-button href="#" size="big">Request a Quote</x-button>
					<x-footer.social-links :social_links="$social_links" />
				</x-footer.column>

				<x-footer.navigation title="About Us">
					<x-footer.navigation-item title="Expedition History" url="#" />
					<x-footer.navigation-item title="Expedition Team" url="#" />
					<x-footer.navigation-item title="Advantage of Small Ships" url="#" />
					<x-footer.navigation-item title="Sustainability" url="#" />
					<x-footer.navigation-item title="Contact Us" url="#" />
				</x-footer.navigation>

				<x-footer.navigation title="Reservation Resources">
					<x-footer.navigation-item title="Dates & Rates" url="#" />
					<x-footer.navigation-item title="Make a Payment" url="#" />
					<x-footer.navigation-item title="Know Before You Go: FAQs" url="#" />
					<x-footer.navigation-item title="Travel Insurance Plans" url="#" />
					<x-footer.navigation-item title="Photographic Journal" url="#" />
					<x-footer.navigation-item title="Expedition Terms and Conditions" url="#" />
					<x-footer.navigation-item title="Quark Expeditions Protection Promise" url="#" />
				</x-footer.navigation>

				<x-footer.column>
					<x-footer.column-title title="Discover Your Dream Trip" />
					<x-button  href="#" size="big" color="black">View All Expeditions</x-button>

					<x-footer.column-title title="Change Currency" />
					<x-form>
						<x-form.field :validation="[ 'required' ]">
							<select>
								<option value="1" label="$ USD">$ USD</option>
								<option value="2" label="Option 2">Option 2</option>
								<option value="3" label="Option 3">Option 3</option>
							</select>
						</x-form.field>
					</x-form>
				</x-footer.column>

				<x-footer.navigation title="Learn About the Polar Regions">
					<x-footer.navigation-item title="Blog" url="#" />
					<x-footer.navigation-item title="Polar Learning Channel" url="#" />
					<x-footer.navigation-item title="Brochures" url="#" />
				</x-footer.navigation>

				<x-footer.navigation title="Quark Expeditions">
					<x-footer.navigation-item title="Careers" url="#" />
					<x-footer.navigation-item title="Media Center" url="#" />
					<x-footer.navigation-item title="Press Releases" url="#" />
				</x-footer.navigation>

				<x-footer.column>
					<x-footer.column-title title="Book Online Today" />
					<p>25% down will reserve your expedition to the polar regions!</p>
					<x-footer.payment-options />
				</x-footer.column>
			</x-footer.middle>

			<x-footer.bottom>
				<x-footer.navigation>
					<x-footer.navigation-item title="Cookie Policy" url="#" />
					<x-footer.navigation-item title="Do Not Sell My Data" url="#" />
					<x-footer.navigation-item title="Privacy Policy" url="#" />
					<x-footer.navigation-item title="Website Terms of Use" url="#" />
				</x-footer.navigation>

				<x-footer.copyright>
					<p>Quark Expeditions® 2024 is a member of the Travelopia group of companies. All rights reserved.</p>
				</x-footer.copyright>
			</x-footer.bottom>
		</x-footer>
	</x-component-demo>
</x-layout>
