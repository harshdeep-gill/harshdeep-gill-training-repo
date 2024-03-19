<x-layout>
	<x-component-demo :keys="[ 'header', 'hero-immersive' ]">
		<x-lp-header
			tc_image_id="18"
			phone_number="+1-877-585-1235"
			cta_text="Talk to a Polar Expert"
		/>
	</x-component-demo>

	<x-component-demo :keys="[ 'hero', 'hero-immersive' ]">
		<x-hero :immersive="true">
			<x-hero.image image_id="29" />
			<x-hero.content>
				<x-hero.title title="Antarctic Voyages" />
				<x-hero.sub-title title="When you venture into the untouched wilds of the Arctic and Antarctic, you need a great team around you. And we have the greatest. You’ll join elite guides trained at Quark Academy — which surpasses every standard imaginable for safety and preparedness in the Polar Regions—and interact directly with world-class polar experts. Our philosophy? The team that explores together, discovers more together." />
			</x-hero.content>
			<x-hero.form>
				<x-inquiry-form
					form_id="inquiry-form"
					title="Almost there!"
					subtitle="We just need a bit more info to help personalize your itinerary."
					salesforce_object="Webform_Landing_Page__c"
				/>
			</x-hero.form>
		</x-hero>
	</x-component-demo>
	<x-component-demo :keys="[ 'review-cards' ]">
		<x-review-cards>
			<x-review-cards.card
				title="Falkland, South Georgia and the Antarctic Circle"
				author_details="South Georgia and Antarctic Peninsula: Penguin Safari"
				author="Denise P."
				rating="4"
			>
				<p>Most exciting and wonderful, and educational experience of my life, thank you to all the experts for sharing so much information on the animals and arctic region, cruise staff and fellow passengers for making this trip one I will remember forever. Most exciting and wonderful, and educational experience of my life, thank you to all the experts for sharing so much information on the animals and arctic region, cruise staff and fellow passengers for making this trip one I will remember forever.</p>
			</x-review-cards.card>
			<x-review-cards.card
				title="An incredible trip to Antarctica"
				author_details="South Georgia and Antarctic Peninsula: Penguin Safari"
				author="Martine S."
				rating="5"
			>
				<p>The whole experience was great. The World Explorer is a beautiful and very comfortable ship, the food was amazing. And of course the landings and cruising the area was a once in a lifetime experience. Most exciting and wonderful, and educational experience of my life, thank you to all the experts for sharing so much information on the animals and arctic region, cruise staff and fellow passengers for making this trip one I will remember forever.</p>
			</x-review-cards.card>
			<x-review-cards.card
				title="Wonderful Antarctic Trip"
				author_details="South Georgia and Antarctic Peninsula: Penguin Safari"
				author="Roger C."
				rating="4"
			>
				<p>Most exciting and wonderful, and educational experience of my life, thank you to all the experts for sharing so much information on the animals and arctic region.</p>
			</x-review-cards.card>
			<x-review-cards.card
				title="Falkland, South Georgia and Antarctica: Explorers and Kings"
				author_details="South Georgia and Antarctic Peninsula: Penguin Safari"
				author="Martine S."
				rating="4"
			>
				<p>The whole experience was great. The World Explorer is a beautiful and very comfortable ship, the food was amazing. And of course the landings and cruising the area was a once in a lifetime experience.</p>
			</x-review-cards.card>
			<x-review-cards.card
				title="9 Day Spitsbergen Polar Bear Safari"
				author_details="South Georgia and Antarctic Peninsula: Penguin Safari"
				author="Carolyn T"
				rating="4"
			>
				<p>Most exciting and wonderful, and educational experience of my life, thank you to all the experts for sharing so much information on the animals and arctic region, cruise staff and fellow passengers for making this trip one I will remember forever. Most exciting and wonderful, and educational experience of my life, thank you to all the experts for sharing so much information on the animals and arctic region, cruise staff and fellow passengers for making this trip one I will remember forever.</p>
			</x-review-cards.card>
			<x-review-cards.card
				title="An incredible trip to Antarctica"
				author_details="South Georgia and Antarctic Peninsula: Penguin Safari"
				author="Martine S."
				rating="5"
			>
				<p>The whole experience was great. The World Explorer is a beautiful and very comfortable ship, the food was amazing. And of course the landings and cruising the area was a once in a lifetime experience. Most exciting and wonderful, and educational experience of my life, thank you to all the experts for sharing so much information on the animals and arctic region, cruise staff and fellow passengers for making this trip one I will remember forever.</p>
			</x-review-cards.card>
		</x-review-cards>
	</x-component-demo>
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
		<x-section title="Form UI Elements" heading_level="2" style="display: flex; flex-wrap: wrap; width: 100%;" class="typography-spacing">
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
							<option value="">Select...</option>
							<option value="1">Option 1</option>
							<option value="2">Option 2</option>
							<option value="3">Option 3</option>
						</x-form.select>
					</x-form.field>
					<x-form.field>
						<x-form.textarea label="What else would you like us to know?" placeholder="eg Lorem ipsum"></x-form.textarea>
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
				<x-inquiry-form
					form_id="inquiry-form"
					title="Almost there!"
					subtitle="We just need a bit more info to help personalize your itinerary."
					salesforce_object="Webform_Landing_Page__c"
				/>
			</x-hero.form>
		</x-hero>
	</x-component-demo>

	<x-component-demo :keys="[ 'hero', 'hero-new-cta' ]">
		<x-hero layout="column" size="big">
			<x-hero.image image_id="26" />
			<x-hero.content>
				<x-hero.title title="Rethink your bucket list.<br/>Think an Antarctic Expedition." />
				<x-hero.sub-title title="Starting from $12,908" />
			</x-hero.content>
			<x-hero.form-modal-cta>Get a Digital Brochure</x-hero.form-modal-cta>
		</x-hero>
	</x-component-demo>

	<x-component-demo :keys="[ 'modal' ]">
		<x-section title="Flexible Multipurpose modal">
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

	<x-component-demo :keys="[ 'icon-columns' ]">
		<x-section title="Icon Columns">
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

	<x-component-demo :keys="['simple-cards']">
		<x-section title="Off Ship Adventure" :background="true" background_color="black" title_align="left">
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

	<x-component-demo :keys="[ 'toast' ]">
		<x-section>
			<x-toast-message message="Lorem ipsum dolor sit ipsum dolor now" :visible="true" />
			<x-toast-message type="error" message="Fields marked with an asterisk (*) are required" :visible="true" />
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'season-highlights' ]">
		<x-section title="Best Time to See" title_align="left">
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

		<x-section title="Best Time to See" title_align="left">
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

		<x-section title="Best Time to See" title_align="left">
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

		<x-section title="Best Time to See" title_align="left">
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
			{{-- <x-media-text-cta.image image_id="32" /> --}}
			<x-media-text-cta.video name="video" image_id="32" video_url="https://youtu.be/dKXlo1XQpds" />

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
</x-layout>
