@props( [
	'languages'  => [],
	'is_compact' => false,
] )

@php
	if ( empty( $languages ) || ! is_array( $languages ) ) {
		return;
	}

	$the_id = 'expedition-search-filter-languages' . ( ! empty( $is_compact ) ? '-compact' : '' );
@endphp

<x-accordion.item id="{{ $the_id }}">
	<quark-expedition-search-filter-languages>
		<x-accordion.item-handle>
			<x-escape :content=" __( 'Languages', 'qrk' ) " /> <span class="expedition-search__filter-count"></span>
		</x-accordion-item.handle>
		<x-accordion.item-content>
			<x-form.field-group>
				@foreach ( $languages as $language )
					@if ( empty( $language['label'] ) || empty( $language['value'] ) || ! isset( $language['count'] ) )
						@continue
					@endif

					<x-expedition-search.sidebar-filters.checkbox name="languages" :label="$language['label']" :value="$language['value']" :count="$language['count']" />
				@endforeach
			</x-form.field-group>
		</x-accordion.item-content>
	</quark-expedition-search-filter-languages>
</x-accordion.item>
