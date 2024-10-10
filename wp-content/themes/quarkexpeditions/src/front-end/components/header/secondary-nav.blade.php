@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<nav class="header__secondary-nav">
	<ul class="header__nav-menu">
		{!! $slot !!}
	</ul>
</nav>
