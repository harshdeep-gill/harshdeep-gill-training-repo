@props( [
	'load_more_text' => __( 'Load More', 'qrk' ),
] )

@php
	// Early return.
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<quark-expedition-search-load-more
	class="expedition-search__load-more typography-spacing"
	loading="false"
	load-more-text="{{ $load_more_text }}"
>
	{!! $slot !!}

	<div class="expedition-search__load-more-button-container">
		<x-button
			size="big"
			appearance="outline"
			class="expedition-search__load-more-button"
			:loading="true"
		>
			<x-escape :content="$load_more_text" />
		</x-button>
	</div>
</quark-expedition-search-load-more>
