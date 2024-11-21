@props( [
	'expedition_details' => '',
	'hero_card_slider'   => '',
] )

<x-section class="expedition-hero" background="true" background_color="black" padding="true" full_width="true">
	<x-two-columns :border="false" :stack_on_tablet="true">
		<x-two-columns.column>
			{!! $expedition_details !!}
		</x-two-columns.column>
		<x-two-columns.column>
			{!! $hero_card_slider !!}
		</x-two-columns.column>
	</x-two-columns>
</x-section>
