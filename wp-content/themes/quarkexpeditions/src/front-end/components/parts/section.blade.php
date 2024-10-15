@props( [
	'id'                => '',
	'title'             => '',
	'title_align'       => '',
	'heading_level'     => '2',
	'description'       => '',
	'background'        => false,
	'background_color'  => '',
	'background_image'  => '',
	'gradient_position' => '',
	'gradient_color'    => '',
	'padding'           => false,
	'narrow'            => false,
	'cta_button'        => [],
	'slot'              => '',
	'heading_link'      => [
		'text'       => '',
		'url'        => '',
		'new_window' => false,
	],
	'has_heading_link' => false,
] )

@php
	// Set default values.
	$full_width = false;

	// Check if background image is set.
	if ( ! empty( $background_image ) ) {
		$full_width = true;
	}
@endphp

<x-section
	:narrow="$narrow"
	:background="$background"
	:background_color="$background_color"
	:padding="$padding"
	:id="$id"
	:full_width="$full_width"
>
	@if( ! empty( $background_image ) )
		<x-section.image :image_id="$background_image" :gradient_position="$gradient_position" :gradient_color="$gradient_color" />
	@endif

	@if( ! empty( $title ) )
		<x-section.heading>
			<x-section.title :title="$title" :heading_level="$heading_level" :align="$title_align" />

			@if ( ! empty( $has_heading_link ) && ! empty( $heading_link ) )
				<x-section.heading-link
					:url="$heading_link['url'] ?? ''"
					:new_window="$heading_link['new_window']"
				>
					<x-escape :content="$heading_link['text']" />
				</x-section.heading-link>
			@endif
		</x-section.heading>
	@endif

	@if ( ! empty( $description ) )
		<x-section.description>
			{!! $description !!}
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
