@props( [
	'drawer_id'    => '',
	'drawer_title' => '',
	'label'        => '',
	'promo_code'   => '',
] )

@php
	if ( empty( $drawer_id ) || empty( $label ) || empty( $promo_code ) ) {
		return;
	}
@endphp

<div class="dates-rates__promo-code">
	<div class="dates-rates__promo-code-header">
		<strong class="dates-rates__promo-code-label"><x-escape :content="$label" /></strong>

		<x-drawer.drawer-open drawer_id="{{ $drawer_id }}" class="dates-rates__drawer-open">
			<x-svg name="info" />
		</x-drawer.drawer-open>
	</div>

	<x-drawer id="{{ $drawer_id }}" animation_direction="right" class="dates-rates__promo-code-content dates-rates__drawer">
		@if ( ! empty( $drawer_title ) )
			<x-drawer.header>
				<h3><x-escape :content="$drawer_title" /></h3>
			</x-drawer.header>
		@endif

		<x-drawer.body class="dates-rates__promo-code-content-body">
			<strong>{{ __( 'Terms & Conditions', 'qrk' ) }}</strong>
			<div class="dates-rates__promo-code-tnc">
				<x-content :content="__( 'Indicates the discount percent offer in effect at the time this record that was updated. Each passenger\'s full name is required at time of booking in order to initiate a hold, or to process a confirmed booking. Offer applies to voyage only, not applicable to transfer packages, adventure options, additional hotel accommodations, pre/post tours, insurance, flights, or group bookings. Quark Expeditions® has the right to limit, change or discontinue the promo savings discount offer at any time without notice. If combined with a dollar value discount, this offer will be applied afterwards. Only a hold booking or a confirmed booking can guarantee the offer. No cash value. Brochure Terms & Conditions apply.
', 'qrk' )" />
			</div>
			<strong>{{ __( 'Promo Code: ', 'qrk' ) }} <x-escape :content="$promo_code"/></strong>
		</x-drawer.body>
	</x-drawer>
</div>
