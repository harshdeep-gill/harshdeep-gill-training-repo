<x-layout>
	<x-component-demo :keys="[ 'header' ]">
		<x-lp-header />
	</x-component-demo>

	<x-component-demo :keys="[ 'typography' ]">
		<x-section title="Typography" heading_level="2">
			<h1>Heading 1</h1>
			<h2>Heading 2</h2>
			<h3>Heading 3</h3>
			<h4>Heading 4</h4>
			<h5>Heading 5</h5>
			<h1>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</h1>
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
			<h2>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</h2>
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
			<h3>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</h3>
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
			<h4>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</h4>
			<ul>
				<li>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</li>
				<li>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</li>
				<li>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</li>
				<li>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</li>
				<li>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</li>
			</ul>
			<ol>
				<li>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</li>
				<li>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</li>
				<li>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</li>
				<li>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</li>
				<li>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</li>
			</ol>
		</x-section>
	</x-component-demo>

	<x-component-demo :keys="[ 'buttons' ]">
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

	{{-- <x-component-demo :keys="[ 'reviews-carousel' ]">
		<x-reviews-carousel title="What Guests are Saying">
			<x-reviews-carousel.carousel>
				<x-reviews-carousel.slide
					image_id="18"
					author="John Roth"
					rating="5"
				>
					<p>This was a trip around the world with multiple iconic sites such as Machu Picchu, Easter Island, and the Taj Mahal etc. The company saw to every detail and made travel as easy as possible while providing marvelous activities and hotels. When a problem occurred that was out of their control, they made immediate arrangements to mitigate the situation.</p>
				</x-reviews-carousel.slide>
				<x-reviews-carousel.slide
					image_id="18"
					author="Kristi Lind"
					rating="4"
				>
					<p>There are rare times in life when you set out on a trip and it becomes a transformative journey of discovery of a culture rich in history, with a people who are unfailingly gracious and kind. That was our experience in India thanks to Enchanting Travels.</p>
				</x-reviews-carousel.slide>
				<x-reviews-carousel.slide
					image_id="18"
					author="The Johnsons"
					rating="3"
				>
					<p>There are rare times in life when you set out on a trip and it becomes a transformative journey of discovery of a culture rich in history, with a people who are unfailingly gracious and kind. That was our experience in India thanks to Enchanting Travels.</p>
				</x-reviews-carousel.slide>
				<x-reviews-carousel.slide
					image_id="18"
					author="Ethan and Yara"
					rating="2"
				>
					<p>Through my conversations with Swati and some changes that evolved the trip was perfectly planned and brilliantly executed. Her interest into my goals on this trip, produced an incredible experience.</p>
				</x-reviews-carousel.slide>
				<x-reviews-carousel.slide
					image_id="18"
					author="Ethan and Yara"
					rating="2"
				>
					<p>Through my conversations with Swati and some changes that evolved the trip was perfectly planned and brilliantly executed. Her interest into my goals on this trip, produced an incredible experience.</p>
				</x-reviews-carousel.slide>
			</x-reviews-carousel.carousel>
		</x-reviews-carousel>
	</x-component-demo> --}}

	<x-component-demo :keys="[ 'reviews-carousel', 'two-columns' ]">
		<x-two-columns :border="true">
			<x-two-columns.column>
				<h3>About Quark Expeditions</h3>
				<p>When you venture into the untouched wilds of the Arctic and Antarctic, you need a great team around you. And we have the greatest. You'll join elite guides trained at Quark Academy — which surpasses every standard imaginable for safety and preparedness in the Polar Regions—and interact directly with world-class polar experts. Our philosophy? The team that explores together, discovers more together.</p>
				<ul>
					<li>In nec mi vitae quam posuere aliquet eget sed leo.</li>
					<li>Sed vel nisi ultricies, sodales risus non, ornare augue.</li>
					<li>Nulla facilisi. Maecenas sit amet porta nulla commodo.</li>
				</ul>
			</x-two-columns.column>
			<x-two-columns.column>
				<x-reviews-carousel title="What Our Guests Have To Say" heading_level="3">
					<x-reviews-carousel.carousel>
						<x-reviews-carousel.slide
							review_title="An incredible trip to Antarctica"
							author="Martine S."
							rating="5"
						>
							<p>The whole experience was great. The World Explorer is a beautiful and very comfortable ship, the food was amazing. And of course the landings and cruising the area was a once in a lifetime experience.</p>
						</x-reviews-carousel.slide>
						<x-reviews-carousel.slide
							review_title="An incredible trip to Antarctica"
							author="Kristi Lind"
							rating="4"
						>
							<p>The whole experience was great. The World Explorer is a beautiful and very comfortable ship, the food was amazing. And of course the landings and cruising the area was a once in a lifetime experience.</p>
						</x-reviews-carousel.slide>
						<x-reviews-carousel.slide
							review_title="An incredible trip to Antarctica"
							author="The Johnsons"
							rating="3"
						>
							<p>The whole experience was great. The World Explorer is a beautiful and very comfortable ship, the food was amazing. And of course the landings and cruising the area was a once in a lifetime experience.</p>
						</x-reviews-carousel.slide>
						<x-reviews-carousel.slide
							review_title="An incredible trip to Antarctica"
							author="Ethan and Yara"
							rating="2"
						>
							<p>The whole experience was great. The World Explorer is a beautiful and very comfortable ship, the food was amazing. And of course the landings and cruising the area was a once in a lifetime experience.</p>
						</x-reviews-carousel.slide>
						<x-reviews-carousel.slide
							review_title="An incredible trip to Antarctica"
							author="Ethan and Yara"
							rating="2"
						>
							<p>The whole experience was great. The World Explorer is a beautiful and very comfortable ship, the food was amazing. And of course the landings and cruising the area was a once in a lifetime experience.</p>
						</x-reviews-carousel.slide>
					</x-reviews-carousel.carousel>
				</x-reviews-carousel>
			</x-two-columns.column>
		</x-two-columns>
	</x-component-demo>

	<x-component-demo :keys="[ 'form' ]">
		<x-section title="Form UI Elements" heading_level="2" style="display: flex; flex-wrap: wrap; width: 100%;" class="typography-spacing">
			<div style="display: flex; flex-wrap: wrap; width: 100%; gap: 20px; justify-content: space-between;">
				<x-form style="min-width: 300px; padding: 24px; border: 1px solid var(--color-black); display:flex; flex-wrap: wrap; flex-direction: column; flex-grow: 1;">
					<x-form.field :validation="[ 'required' ]">
						<x-form.input type="text" label="First Name" placeholder="Enter First Name" />
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
						<x-button type="submit">Request a Quote</x-button>
					</x-form.buttons>
				</x-form>

				<x-form
					style="background-color: var(--color-black); min-width: 300px; padding: 24px; border: 1px solid var(--color-black); display:flex; flex-wrap: wrap; flex-direction: column; flex-grow: 1;"
					class="color-context--dark">
					<x-form.field :validation="[ 'required' ]">
						<x-form.input type="text" label="First Name" placeholder="Enter First Name" />
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
						<x-button type="submit">Request a Quote</x-button>
					</x-form.buttons>
				</x-form>
			</div>
		</x-section>
	</x-component-demo>
	<x-section></x-section>
</x-layout>

