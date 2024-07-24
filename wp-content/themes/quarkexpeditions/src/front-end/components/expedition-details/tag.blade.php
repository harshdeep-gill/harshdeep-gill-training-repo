@props( [
	'title' => '',
	'url'   => '',
] )

@php
    if ( empty( $title ) ) {
		return;
	}
@endphp


<li class="expedition-details__tag">
    @if ( ! empty( $url ) )
        <a href="{{ $url }}" class="expedition-details__tag-link">
            <x-escape :content="$title"/>
        </a>
    @else
        <x-escape :content="$title"/>
    @endif
</li>
