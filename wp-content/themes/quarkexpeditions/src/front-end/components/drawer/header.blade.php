@php
	if( empty( $slot ) ) {
		return;
	}
@endphp

<header class="drawer__header">
	{!! $slot !!}
	<x-drawer.drawer-close />
</header>
