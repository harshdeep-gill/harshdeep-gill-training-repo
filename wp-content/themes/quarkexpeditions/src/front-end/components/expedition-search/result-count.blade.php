@props( [
	'count' => 0,
] )

@php
	// Build expedition count text to be displayed.
	$expedition_count_text = _n( 'expedition', 'expeditions', $count, 'qrk' );
@endphp

<div class="expedition-search__result-count">
	{{ __( 'Showing', 'qrk' ) }}
	<span class="expedition-search__result-count-value">{{ $count ?? 0 }}</span>
	{{ $expedition_count_text ?? 'expeditions' }}
</div>
