@props( [
	'class' => '',
] )

@php
	if( empty( $slot ) ) {
		return;
	}
@endphp

<header @class( [ $class, 'drawer__header' ] )>
	<div class="drawer__header-content">
		{!! $slot !!}
	</div>
</header>
