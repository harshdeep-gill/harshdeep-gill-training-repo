@props( [
	'languages' => [],
] )

@php
	if ( empty( $languages ) || ! is_array( $languages ) ) {
		return;
	}
@endphp

<quark-expedition-search-filter-languages>
	<x-accordion.item>
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
</x-accordion.item>
</quark-expedition-search-filter-languages>
