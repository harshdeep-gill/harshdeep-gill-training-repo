@props( [
	'title'    => '',
	'contents' => [],
] )

@php
	if ( empty( $contents ) ) {
		return;
	}
@endphp

<quark-table-of-contents class="table-of-contents">
	@if ( ! empty( $title ) )
		<h3 class="h4 table-of-contents__title">
			<x-content :content="$title" />
		</h3>
	@endif
	<ul class="table-of-contents__list">
		@foreach ( $contents as $content_item )
			<li
				@class( [
					'table-of-contents__list-item',
					'table-of-contents__list-item--active' => $loop->first,
				] )
				data-anchor="#{{ $content_item['anchor'] }}"
			>
				<div class="table-of-contents__content">
					<a class="table-of-contents__list-item-title" href="#{{ $content_item['anchor'] }}">
						<x-escape :content="$content_item['title']"/>
					</a>
				</div>
			</li>
		@endforeach
	</ul>
</quark-table-of-contents>
