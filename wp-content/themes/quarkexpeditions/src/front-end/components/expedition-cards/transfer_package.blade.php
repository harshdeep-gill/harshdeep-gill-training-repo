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

<div class="expedition-cards__transfer-package">
	<div class="expedition-cards__transfer-package-header">
		<p class="expedition-cards__transfer-package-label">{!! $label ? $label : __( 'Includes Transfer Package', 'qrk' ) !!}</p>

		<x-drawer.drawer-open drawer_id="{{ $drawer_id }}" class="expedition-cards__drawer-open">
			<x-svg name="info" />
		</x-drawer.drawer-open>
	</div>

	<x-drawer id="{{ $drawer_id }}" animation_direction="right" class="expedition-cards__transfer-package-content">
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
