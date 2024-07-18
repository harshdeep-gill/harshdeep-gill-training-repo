@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<nav class="header__primary-nav">
	<ul class="header__nav-menu">
		<x-content :content="$slot" />
	</ul>
</nav>
