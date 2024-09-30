@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<quark-expedition-search class="expedition-search" loading="false">
	{!! $slot !!}
</quark-expedition-search>
