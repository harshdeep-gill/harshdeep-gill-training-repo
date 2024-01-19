<x-layout>

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
				<div style="width: 50%; padding: 24px; background-color: var(--color-black);"
					 class="color-context--dark">
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

</x-layout>

