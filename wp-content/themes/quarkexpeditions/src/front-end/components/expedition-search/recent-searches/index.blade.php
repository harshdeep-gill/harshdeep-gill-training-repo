@props( [
	'title' => __( 'Your recent searches', 'qrk' ),
] )

@php
	quark_component_enqueue_assets( 'search-filters-bar' );
@endphp

<quark-expedition-search-recent-searches class="expedition-search__recent-searches">
	<h4 class="h4"><x-escape :content="$title" /></h4>
	<template>
		<x-mini-cards-list.card>
			<x-mini-cards-list.card-image image_id="120" />
			<x-mini-cards-list.card-info>
				<x-mini-cards-list.card-title title="Antarctic Peninsula" />
				<x-mini-cards-list.card-date date="June 2024" />
			</x-mini-cards-list.card-info>
		</x-mini-cards-list.card>
	</template>

	<x-mini-cards-list>
	</x-mini-cards-list>
</quark-expedition-search-recent-searches>
