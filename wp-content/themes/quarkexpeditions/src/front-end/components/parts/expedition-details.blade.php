@props( [
	'appearance'       => 'dark',
	'title'            => '',
	'region'           => '',
	'duration'         => '',
	'from_price'       => '',
	'starting_from'    => [],
	'ships'            => [],
	'tags'             => [],
	'total_departures' => 0,
	'from_date'        => '',
	'to_date'          => '',
] )

<x-expedition-details :appearance="$appearance">
	<x-expedition-details.overline :region="$region" :duration="$duration" :from_price="$from_price"/>
	<x-expedition-details.title :title="$title"/>

	@if ( ! empty( $tags ) )
		<x-expedition-details.tags>
			@foreach ( $tags as $tag )
				<x-expedition-details.tag
					:title="$tag['title'] ?? ''"
					:url="$tag['url'] ?? ''"
				/>
			@endforeach
		</x-expedition-details.tags>
	@endif

	@if( ! empty( $starting_from ) || ! empty( $ship ) )
		<x-expedition-details.row>
			@if ( ! empty( $starting_from ) )
				<x-expedition-details.starting-from>
					@foreach ( $starting_from as $starting_from_item )
						<x-expedition-details.starting-from-item
							:title="$tag['title'] ?? ''"
							:url="$tag['url'] ?? ''"
						/>
					@endforeach
				</x-expedition-details.starting-from>
			@endif

			@if ( ! empty( $ships ) )
				<x-expedition-details.ships>
					@foreach ( $ships as $ship )
						<x-expedition-details.ship
							:title="$ship['title'] ?? ''"
							:url="$ship['url'] ?? ''"
						/>
					@endforeach
				</x-expedition-details.ships>
			@endif
		</x-expedition-details.row>
	@endif

	@if ( ! empty( $total_departures ) || ! empty( $from_date ) || ! empty( $to_date ) )
		<x-expedition-details.row>
			<x-expedition-details.departures
				:total_departures="$total_departures"
				:from_date="$from_date"
				:to_date="$to_date"
			/>
		</x-expedition-details.row>
	@endif

	<x-expedition-details.cta>
		<x-button size="big" color="black" href="#bookings">
			<x-escape :content="__( 'View all Departures', 'qrk' )"/>
		</x-button>
	</x-expedition-details.cta>
</x-expedition-details>
