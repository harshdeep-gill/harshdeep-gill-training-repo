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
    <x-expedition-details.tags :tags="$tags"/>
    <x-expedition-details.row>
        <x-expedition-details.starting-from :starting_from="$starting_from"/>
        <x-expedition-details.ships :ships="$ships"/>
	</x-expedition-details.row>

	<x-expedition-details.row>
		<x-expedition-details.departures :total_departures="$total_departures" :from_date="$from_date" :to_date="$to_date"/>
	</x-expedition-details.row>

	<x-expedition-details.cta>
		<x-button size="big" color="black" href="#bookings">View all Departures</x-button>
	</x-expedition-details.cta>
</x-expedition-details>
