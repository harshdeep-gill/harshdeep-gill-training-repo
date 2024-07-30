@props( [
	'drawer_id' => '',
] )

@php
	if ( empty( $slot ) || empty( $drawer_id ) ) {
		return;
	}
@endphp

<div class="departure-cards__transfer-package">
	<div class="departure-cards__transfer-package-header">
		<p class="departure-cards__transfer-package-label">{{ __( 'Includes Transfer Package', 'qrk' ) }}</p>

		<x-drawer.drawer-open drawer_id="{{ $drawer_id }}" class="departure-cards__drawer-open">
			<x-svg name="info" />
		</x-drawer.drawer-open>
	</div>

	<x-drawer id="{{ $drawer_id }}" animation_direction="right" class="departure-cards__transfer-package-content">
		<x-drawer.header>
			<h3>Lorem ipsum dolor sit amet.</h3>
			<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur voluptate dolorum alias officiis minima nemo asperiores maxime velit itaque sapiente?</p>
		</x-drawer.header>

		<x-drawer.body>
			{!! $slot !!}
		</x-drawer.body>
	</x-drawer>
</div>
