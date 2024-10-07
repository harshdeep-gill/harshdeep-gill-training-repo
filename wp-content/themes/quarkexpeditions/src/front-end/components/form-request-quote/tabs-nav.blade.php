@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<tp-tabs-nav>
	<nav class="form-request-quote__tabs-nav">
		{!! $slot !!}
	</nav>
</tp-tabs-nav>
