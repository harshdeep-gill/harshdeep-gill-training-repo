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
			</div>
			<strong>{{ __( 'Promo Code: ', 'qrk' ) }} <x-escape :content="$promo_code"/></strong>
		</x-drawer.body>
	</x-drawer>
</div>
