@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="header__nav-item-dropdown-content-wrap">
	<div class="header__nav-item-dropdown-content">
		{!! $slot !!}
	</div>
</div>
