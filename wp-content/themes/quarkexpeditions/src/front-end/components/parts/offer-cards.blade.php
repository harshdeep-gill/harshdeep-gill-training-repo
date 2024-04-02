@props( [
	'items' => [],
] )

@php
	if ( empty( $items ) ) {
		return;
	}
@endphp

<x-offer-cards>
	@foreach ( $items as $item )
		<x-offer-cards.card>
			<x-offer-cards.heading> {{ $item['heading'] }}</x-offer-cards.heading>
			<x-offer-cards.content>
				@if ( ! empty( $item['children'] ) )
					@foreach ( $item['children'] as $child )
						@if ( 'title' === $child['type'] )
							<x-offer-cards.title :title="$child['title'] ?? ''" />
						@endif
						@if ( 'promotion' === $child['type'] )
							<x-offer-cards.promotion :text="$child['promotion'] ?? ''" />
						@endif
						@if ( 'cta' === $child['type'] )
							<x-offer-cards.cta>
								{!! $child['slot'] ?? '' !!}
							</x-offer-cards.cta>
						@endif
						@if ( 'help' === $child['type'] )
							<x-offer-cards.help-text>
								{!! $child['slot'] ?? '' !!}
							</x-offer-cards.help-text>
						@endif
					@endforeach
				@endif
			</x-offer-cards.content>
		</x-offer-cards.card>
	@endforeach
</x-offer-cards>
