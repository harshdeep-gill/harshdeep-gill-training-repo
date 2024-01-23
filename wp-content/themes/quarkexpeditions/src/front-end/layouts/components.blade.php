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
		<x-lp-header />
	</x-component-demo>

	<x-component-demo :keys="[ 'two-columns' ]">
		<x-two-columns :border="true">
			<x-two-columns.column>
				<h3>About Quark Expeditions</h3>
				<p>When you venture into the untouched wilds of the Arctic and Antarctic, you need a great team around you. And we have the greatest. You’ll join elite guides trained at Quark Academy — which surpasses every standard imaginable for safety and preparedness in the Polar Regions—and interact directly with world-class polar experts. Our philosophy? The team that explores together, discovers more together.</p>
				<ul>
					<li>In nec mi vitae quam posuere aliquet eget sed leo.</li>
					<li>Sed vel nisi ultricies, sodales risus non, ornare augue.</li>
					<li>Nulla facilisi. Maecenas sit amet porta nulla commodo.</li>
					<li>Rhoncus purus duis in urna ac lorem sagittis porta.</li>
				</ul>
			</x-two-columns.column>
			<x-two-columns.column>
				<h3>What Our Guests Have To Say</h3>
				<p>When you venture into the untouched wilds of the Arctic and Antarctic, you need a great team around you. And we have the greatest. You’ll join elite guides trained at Quark Academy — which surpasses every standard imaginable for safety and preparedness in the Polar Regions—and interact directly with world-class polar experts. Our philosophy? The team that explores together, discovers more together.</p>
				<ul>
					<li>In nec mi vitae quam posuere aliquet eget sed leo.</li>
					<li>Sed vel nisi ultricies, sodales risus non, ornare augue.</li>
					<li>Nulla facilisi. Maecenas sit amet porta nulla commodo.</li>
					<li>Rhoncus purus duis in urna ac lorem sagittis porta.</li>
				</ul>
			</x-two-columns.column>
		</x-two-columns>
	</x-component-demo>
	<x-component-demo :keys="[ 'logo-grid' ]">
		<x-section title="Logo Grid">
			<div  style="display: flex; flex-direction: column; gap: 50px;">
				<x-logo-grid size="lg">
					<x-logo-grid.logo image_id="15"/>
					<x-logo-grid.logo image_id="15"/>
					<x-logo-grid.logo image_id="16"/>
					<x-logo-grid.logo image_id="16"/>
					<x-logo-grid.logo image_id="17"/>
				</x-logo-grid>


				<div
					style="background-color: var(--color-gray-90); padding-block:20px; padding-inline:10px"
					class="color-context--dark"
				>
					<x-logo-grid alignment="center" size="lg">
						<x-logo-grid.logo image_id="11"/>
						<x-logo-grid.logo image_id="12"/>
						<x-logo-grid.logo image_id="10"/>
					</x-logo-grid>
				</div>

				<x-logo-grid size="lg" alignment="center">
					<x-logo-grid.logo image_id="14"/>
					<x-logo-grid.logo image_id="13"/>
				</x-logo-grid>
			</div>
		</x-section>
		<x-section></x-section>
	</x-component-demo>
</x-layout>

