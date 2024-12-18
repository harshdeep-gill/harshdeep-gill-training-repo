@props( [
	'drawer_id'    => '',
	'drawer_title' => '',
	'label'        => '',
] )

@php
	if ( empty( $slot ) || empty( $drawer_id ) ) {
		return;
	}
@endphp

<div class="departure-cards__transfer-package">
	<div class="departure-cards__transfer-package-header">
		<p class="departure-cards__transfer-package-label">{!! $label ? $label : __( 'Includes Transfer Package', 'qrk' ) !!}</p>

		<x-drawer.drawer-open drawer_id="{{ $drawer_id }}" class="departure-cards__drawer-open">
			<x-svg name="info" />
		</x-drawer.drawer-open>
	</div>

	<x-drawer id="{{ $drawer_id }}" animation_direction="right" class="departure-cards__transfer-package-content">
		@if ( ! empty( $drawer_title ) )
			<x-drawer.header>
				<h3><x-escape :content="$drawer_title" /></h3>
			</x-drawer.header>
		@endif

		<x-drawer.body>
			{!! $slot !!}
		</x-drawer.body>
	</x-drawer>
</div>
