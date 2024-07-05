@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<tp-tabs-nav>
	<nav class="tabs__nav">
		{!! $slot !!}
	</nav>
</tp-tabs-nav>
