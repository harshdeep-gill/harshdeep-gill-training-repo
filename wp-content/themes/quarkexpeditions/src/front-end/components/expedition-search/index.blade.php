@php
	if ( empty( $slot ) ) {
		return;
	}

	quark_enqueue_script( 'querystring' );
@endphp

<quark-expedition-search class="expedition-search" loading="false">
	{!! $slot !!}
</quark-expedition-search>
