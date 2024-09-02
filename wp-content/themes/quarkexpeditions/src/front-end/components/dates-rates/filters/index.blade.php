@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="dates-rates__filters-container">
	<h2 class="dates-rates__filters-heading">{{ __( 'Filters', 'qrk' ) }}</h2>
	<div class="dates-rates__filters">
		<x-dates-rates.filters-chips>
			<x-dates-rates.filter-chip drawer_id="dates-rates-filters" title="Region & Season" accordion_id="filters-accordion-region-season" />
			<x-dates-rates.filter-chip drawer_id="dates-rates-filters" title="Expedition" accordion_id="filters-accordion-expedition" />
			<x-dates-rates.filter-chip drawer_id="dates-rates-filters" title="Adventure Options" accordion_id="filters-accordion-adevnture-options" />
			<x-dates-rates.filter-chip drawer_id="dates-rates-filters" title="Departure Month" accordion_id="filters-accordion-departure-month" />
			<x-dates-rates.filter-chip drawer_id="dates-rates-filters" title="Duration" accordion_id="filters-accordion-duration" />
			<x-dates-rates.filter-chip drawer_id="dates-rates-filters" title="Ship" accordion_id="filters-accordion-ship" />
		</x-dates-rates.filters-chips>

		<x-dates-rates.filter-currency>
			<x-form.select>
				<x-form.option value="USD" label="$ USD" selected="yes">{{ __( '$ USD', 'qrk' ) }}</x-form.option>
				<x-form.option value="CAD" label="$ CAD">{{ __( '$ CAD', 'qrk' ) }}</x-form.option>
				<x-form.option value="AUD" label="$ AUD">{{ __( '$ AUD', 'qrk' ) }}</x-form.option>
				<x-form.option value="GBP" label="£ GBP">{{ __( '£ GBP', 'qrk' ) }}</x-form.option>
				<x-form.option value="EUR" label="€ EUR">{{ __( '€ EUR', 'qrk' ) }}</x-form.option>
			</x-form.select>
		</x-dates-rates.filter-currency>
	</div>

	<x-dates-rates.sticky-filters>
		<x-dates-rates.filter-chip drawer_id="dates-rates-filters" title="Filter" accordion_id="filters-accordion-region-season" />
		<x-dates-rates.filter-chip drawer_id="dates-rates-filters-currency" title="Currency: USD" />
	</x-dates-rates.sticky-filters>

	<x-drawer id="dates-rates-filters-currency" animation_direction="up" class="dates-rates__drawer-currency">
		<x-drawer.header>
			<h3>Currency</h3>
		</x-drawer.header>

		<x-drawer.body>
			<x-form.field-group>
				<x-form.radio name="currency" value="USD" label="$ USD" />
				<x-form.radio name="currency" value="CAD" label="$ CAD" />
				<x-form.radio name="currency" value="AUD" label="$ AUD" />
				<x-form.radio name="currency" value="GBP" label="$ GBP" />
				<x-form.radio name="currency" value="EUR" label="$ EUR" />
			</x-form.field-group>
		</x-drawer.body>
	</x-drawer>

	<x-drawer id="dates-rates-filters" animation_direction="up" class="dates-rates__drawer">
		<x-drawer.header>
			<h3>Filters</h3>
		</x-drawer.header>

		<x-drawer.body>
			<x-accordion>
				<x-accordion.item id="filters-accordion-region-season">
					<x-accordion.item-handle title="Region & Season" />
					<x-accordion.item-content>
						<x-form.field-group :validation="[ 'checkbox-group-required' ]">
							<x-form.checkbox name="" label="English" />
							<x-form.checkbox name="" label="Italian" />
							<x-form.checkbox name="" label="Arabic" />
							<x-form.checkbox name="" label="Chinese, Mandarin" />
							<x-form.checkbox name="" label="Japanese" />
							<x-form.checkbox name="" label="Hindi" />
							<x-form.checkbox name="" label="French" />
							<x-form.checkbox name="" label="Korean" />
							<x-form.checkbox name="" label="Portuguese" />
							<x-form.checkbox name="" label="German" />
							<x-form.checkbox name="" label="Russian" />
							<x-form.checkbox name="" label="Other" />
							<x-form.checkbox name="" label="Spanish" />
							<x-form.checkbox name="" label="Bengali" />
						</x-form.field-group>
					</x-accordion.item-content>
				</x-accordion.item>
				<x-accordion.item id="filters-accordion-expedition">
					<x-accordion.item-handle title="Expedition" />
					<x-accordion.item-content>
						<x-form.field-group :validation="[ 'checkbox-group-required' ]">
							<x-form.checkbox name="" label="Item 1" />
							<x-form.checkbox name="" label="Item 2" />
							<x-form.checkbox name="" label="Item 3" />
							<x-form.checkbox name="" label="Item 4" />
							<x-form.checkbox name="" label="Item 5" />
							<x-form.checkbox name="" label="Item 6" />
							<x-form.checkbox name="" label="Item 7" />
						</x-form.field-group>
					</x-accordion.item-content>
				</x-accordion.item>
				<x-accordion.item id="filters-accordion-adevnture-options">
					<x-accordion.item-handle title="Adventure Options (with availability)" />
					<x-accordion.item-content>
						<x-form.field-group :validation="[ 'checkbox-group-required' ]">
							<x-form.checkbox name="" label="Item 1" />
							<x-form.checkbox name="" label="Item 2" />
							<x-form.checkbox name="" label="Item 3" />
							<x-form.checkbox name="" label="Item 4" />
							<x-form.checkbox name="" label="Item 5" />
							<x-form.checkbox name="" label="Item 6" />
							<x-form.checkbox name="" label="Item 7" />
						</x-form.field-group>
					</x-accordion.item-content>
				</x-accordion.item>
				<x-accordion.item id="filters-accordion-departure-month">
					<x-accordion.item-handle title="Departure Month" />
					<x-accordion.item-content>
						<x-form.field-group :validation="[ 'checkbox-group-required' ]">
							<x-form.checkbox name="" label="Item 1" />
							<x-form.checkbox name="" label="Item 2" />
							<x-form.checkbox name="" label="Item 3" />
							<x-form.checkbox name="" label="Item 4" />
							<x-form.checkbox name="" label="Item 5" />
							<x-form.checkbox name="" label="Item 6" />
							<x-form.checkbox name="" label="Item 7" />
						</x-form.field-group>
					</x-accordion.item-content>
				</x-accordion.item>
				<x-accordion.item id="filters-accordion-duration">
					<x-accordion.item-handle title="Duration of Voyage (days)" />
					<x-accordion.item-content>
						<x-form.field-group :validation="[ 'checkbox-group-required' ]">
							<x-form.checkbox name="" label="Item 1" />
							<x-form.checkbox name="" label="Item 2" />
							<x-form.checkbox name="" label="Item 3" />
							<x-form.checkbox name="" label="Item 4" />
							<x-form.checkbox name="" label="Item 5" />
							<x-form.checkbox name="" label="Item 6" />
							<x-form.checkbox name="" label="Item 7" />
						</x-form.field-group>
					</x-accordion.item-content>
				</x-accordion.item>
				<x-accordion.item id="filters-accordion-ship">
					<x-accordion.item-handle title="Ship" />
					<x-accordion.item-content>
						<x-form.field-group :validation="[ 'checkbox-group-required' ]">
							<x-form.checkbox name="" label="Item 1" />
							<x-form.checkbox name="" label="Item 2" />
							<x-form.checkbox name="" label="Item 3" />
							<x-form.checkbox name="" label="Item 4" />
							<x-form.checkbox name="" label="Item 5" />
							<x-form.checkbox name="" label="Item 6" />
							<x-form.checkbox name="" label="Item 7" />
						</x-form.field-group>
					</x-accordion.item-content>
				</x-accordion.item>
			</x-accordion>
		</x-drawer.body>

		<x-drawer.footer>
			<x-dates-rates.cta-clear-filters />
			<x-button href="#" size="big">View Results (132)</x-button>
		</x-drawer.footer>
	</x-drawer>
</div>
