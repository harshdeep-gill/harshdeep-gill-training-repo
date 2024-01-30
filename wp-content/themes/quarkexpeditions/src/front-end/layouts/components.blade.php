<x-layout>
	<x-component-demo :keys="[ 'global', 'color-palette' ]">
		<x-section title="Color Palette" heading_level="2">
			<x-global-styles-demo.color-palette />
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'global', 'typography' ]">
		<x-section title="Typography" heading_level="2">
			<x-global-styles-demo.typography />
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'global', 'buttons' ]">
		<x-section title="Buttons & Links" heading_level="2">
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
						<x-button color="black" appearance="outline">Solid button</x-button>
					</div>
					<div class="typography-spacing" style="display: flex; gap: 16px; flex-flow: row wrap;">
						<x-button size="big">Solid button</x-button>
						<x-button size="big" color="black" appearance="outline">Solid button</x-button>
					</div>
				</div>
				<div style="width: 50%; padding: 24px; background-color: var(--color-black);" class="color-context--dark">
					<div class="typography-spacing" style="display: flex; gap: 16px; flex-flow: row wrap;">
						<x-button>Solid button</x-button>
						<x-button color="black" appearance="outline">Solid button</x-button>
					</div>
					<div class="typography-spacing" style="display: flex; gap: 16px; flex-flow: row wrap;">
						<x-button size="big">Solid button</x-button>
						<x-button size="big" color="black" appearance="outline">Solid button</x-button>
					</div>
				</div>
			</div>
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'form' ]">
		<x-section title="Form UI Elements" heading_level="2" style="display: flex; flex-wrap: wrap; width: 100%;" class="typography-spacing">
			<div style="display: flex; flex-wrap: wrap; width: 100%; gap: 20px; justify-content: space-between;">
				<x-form style="min-width: 300px; padding: 24px; border: 1px solid var(--color-black); display:flex; flex-wrap: wrap; flex-direction: column; flex-grow: 1;">
					<x-form.field :validation="[ 'required' ]">
						<x-form.input type="text" label="First Name" placeholder="Enter First Name" name="fields[first_name]" />
					</x-form.field>
					<x-form.field :validation="[ 'required' ]">
						<x-form.select label="Country" name="fields[country]">
							<option value="">Select...</option>
							<option value="1">Option 1</option>
							<option value="2">Option 2</option>
							<option value="3">Option 3</option>
						</x-form.select>
					</x-form.field>
					<x-form.field>
						<x-form.textarea label="What else would you like us to know?" placeholder="eg Lorem ipsum" name="fields[comments]"></x-form.textarea>
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
							<option value="">Select...</option>
							<option value="1">Option 1</option>
							<option value="2">Option 2</option>
							<option value="3">Option 3</option>
						</x-form.select>
					</x-form.field>
					<x-form.field>
						<x-form.textarea label="What else would you like us to know?" placeholder="eg Lorem ipsum" name="fields[comments]"></x-form.textarea>
					</x-form.field>
					<x-form.buttons>
						<x-form.submit>Request a Quote</x-form.submit>
					</x-form.buttons>
				</x-form>
			</div>
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'header' ]">
		<x-lp-header
			tc_image_id="18"
			phone_number="+1-877-585-1235"
			cta_text="Talk to a Polar Expert"
		/>
	</x-component-demo>

	<x-component-demo :keys="[ 'hero' ]">
		<x-hero>
			<x-hero.image image_id="26" />
			<x-hero.content>
				<x-hero.title title="Antarctic Voyages" />
				<x-hero.sub-title title="Choose the Leader in Polar Adventure" />
			</x-hero.content>
			<x-hero.form>
				<x-form>
					<x-form.field :validation="[ 'required' ]">
						<x-form.select label="When would you like to travel?" name="fields[where_to_visit]" form="inquiry-form">
							<option value="">- Select -</option>
							<option value="Antarctic Peninsula">Antarctic Peninsula</option>
							<option value="Falklands &amp; South Georgia">Falklands &amp; South Georgia</option>
							<option value="Patagonia">Patagonia</option>
							<option value="Snow Hill Island">Snow Hill Island</option>
						</x-form.select>
					</x-form.field>
					<x-form.field :validation="[ 'required' ]">
						<x-form.select label="The most important factor for you?" name="fields[country]" form="inquiry-form">
							<option value="">- Select -</option>
							<option value="adventure_activities">Adventure Activities</option>
							<option value="budget">Budget</option>
							<option value="region">Destination</option>
							<option value="schedule">Schedule</option>
							<option value="wildlife">Wildlife</option>
						</x-form.select>
					</x-form.field>
					<x-form.field :validation="[ 'required' ]">
						<x-form.select label="How many guests?" name="fields[country]" form="inquiry-form">
							<option value="">- Select -</option>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
						</x-form.select>
					</x-form.field>
					<x-form.field>
						<x-form.select label="Where would you like to go?" name="fields[country]" form="inquiry-form">
							<option value="">- Select -</option>
							<option value="2023-24">Antarctic 2023/24 (Nov '23 - Mar '24)</option>
							<option value="2024-25">Antarctic 2024/25 (Nov '24 - Mar '25)</option>
							<option value="2025-26">Antarctic 2025/26 (Nov '25 - Mar '26)</option>
						</x-form.select>
					</x-form.field>
					<x-form.buttons>
						<x-modal.open-modal modal_id="inquiry-form">
							<x-button type="button">
								Request a Quote
								<x-button.sub-title title="It only takes 2 minutes!" />
							</x-button>
						</x-modal.open-modal>
					</x-form.buttons>
				</x-form>
			</x-hero.form>
		</x-hero>
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
						<x-logo-grid.logo image_id="25"/>
						<x-logo-grid.logo image_id="24"/>
						<x-logo-grid.logo image_id="21"/>
						<x-logo-grid.logo image_id="20"/>
						<x-logo-grid.logo image_id="17"/>
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

	<x-component-demo :keys="[ 'icon-info-columns' ]">
		<x-section title="Why Quark Expeditions?" :background="true">
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

	<x-component-demo :keys="[ 'logo-grid', 'feel-safe' ]">
		<x-section :narrow="true">
			<h3 style="text-align: center;">Feel safe with a globally accredited company</h3>
			<p style="text-align: center;">Quark Expeditions is a member of the United States Tour Operators Association and other international accreditation organizations. As a result, you can travel with compete peace of mind since your trip is financially protected.</p>
			<div style="display: flex; flex-direction: column; gap: 50px;">
				<x-logo-grid size="large" alignment="center">
					<x-logo-grid.logo image_id="14"/>
					<x-logo-grid.logo image_id="13"/>
				</x-logo-grid>
			</div>
		</x-section>
	</x-component-demo>
	<x-component-demo :keys="[ 'modal' ]">
		<x-modal id="header-cta-modal" :full_width_mobile="true" :close_button="false">
			<x-inquiry-form title="Almost there!" subtitle="We just need a bit more info to help personalize your itinerary.">
				<x-modal.close-modal/>
			</x-inquiry-form>
		</x-modal>
		<x-section>
			<x-modal.open-modal modal_id="header-cta-modal">
				<x-button>Request a Quote</x-button>
			</x-modal.open-modal>
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'toast' ]">
		<x-section>
			<x-toast message="Lorem ipsum dolor sit ipsum dolor now"/>
			<x-toast type="error" message="Fields marked with an asterisk (*) are required"/>
		</x-section>
	</x-component-demo>
	<x-component-demo :keys="[ 'lp-footer', 'logo-grid' ]">
		<x-lp-footer>
			<x-lp-footer.columns>
				<x-lp-footer.column>
					<x-lp-footer.featured-on title="Featured on:">
						<x-logo-grid alignment="center">
							<x-logo-grid.logo image_id="22"/>
							<x-logo-grid.logo image_id="23"/>
							<x-logo-grid.logo image_id="19"/>
						</x-logo-grid>
					</x-lp-footer.featured-on>
				</x-lp-footer.column>
				<x-lp-footer.column>
					<x-lp-footer.links>
						<ul>
							<li><a href="#">Terms of Use</a></li>
							<li><a href="#">Privacy Policy</a></li>
							<li>All rights reserved @ 2023</li>
						</ul>
					</x-lp-footer.links>
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
			</x-lp-footer.columns>
		</x-lp-footer>
	</x-component-demo>
</x-layout>

