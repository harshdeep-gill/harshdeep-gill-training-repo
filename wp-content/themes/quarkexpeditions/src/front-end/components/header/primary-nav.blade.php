@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<nav class="header__primary-nav">
	<quark-header-nav-menu class="header__primary-nav-menu">
		<ul>
			<x-content :content="$slot" />
		</ul>
	</quark-header-nav-menu>
</nav>
