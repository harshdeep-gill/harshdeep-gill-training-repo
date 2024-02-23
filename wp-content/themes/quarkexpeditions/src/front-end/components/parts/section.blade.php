@props( [
	'id'               => '',
	'title'            => '',
	'title_align'      => '',
	'heading_level'    => '3',
	'description'      => '',
	'background'       => false,
	'background_color' => '',
	'padding'          => false,
	'narrow'           => false,
	'cta_button'       => [],
	'slot'             => '',
] )

<x-section
	:title="$title"
	:title_align="$title_align"
	:heading_level="$heading_level"
	:narrow="$narrow"
	:background="$background"
	:background_color="$background_color"
	:padding="$padding"
>
	@if ( ! empty( $description ) )
		<x-section.description>
			{{ $description }}
		</x-section.description>
	@endif

	{!! $slot !!}

	@if ( ! empty( $cta_button ) )
		<x-section.cta
			:class="$cta_button['class']"
			:url="$cta_button['url']"
			:new_window="$cta_button['new_window']"
			:text="$cta_button['text']"
		/>
	@endif
</x-section>
