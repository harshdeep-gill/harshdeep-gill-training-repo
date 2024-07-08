@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="header__nav-item-dropdown-content-column">
	{!! $slot !!}
</div>
