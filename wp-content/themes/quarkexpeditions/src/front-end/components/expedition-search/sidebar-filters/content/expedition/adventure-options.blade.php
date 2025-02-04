@props( [
	'adventure_options' => [],
	'is_compact'        => false,
] )

@php
	if ( empty( $adventure_options ) || ! is_array( $adventure_options ) ) {
		return;
	}

	$the_id = 'expedition-search-filter-adventure-options' . ( ! empty( $is_compact ) ? '-compact' : '' );
@endphp

<x-accordion.item id="{{ $the_id }}">
	<quark-expedition-search-filter-adventure-options>
		<x-accordion.item-handle>
			<x-escape :content=" __( 'Adventure Options', 'qrk' ) " /> <span class="expedition-search__filter-count"></span>
		</x-accordion-item.handle>
		<x-accordion.item-content>
			<x-form.field-group>
				@foreach ( $adventure_options as $adventure_option )
					@if ( empty( $adventure_option['label'] ) || empty( $adventure_option['value'] ) || ! isset( $adventure_option['count'] ) )
						@continue
					@endif

					<x-expedition-search.sidebar-filters.checkbox name="adventure-options" :label="$adventure_option['label']" :value="$adventure_option['value']" :count="$adventure_option['count']" />
				@endforeach
			</x-form.field-group>
		</x-accordion.item-content>
	</quark-expedition-search-filter-adventure-options>
</x-accordion.item>
