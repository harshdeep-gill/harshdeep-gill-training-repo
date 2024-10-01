@props( [
	'title' => __( 'Your recent searches', 'qrk' ),
] )

{{-- This will be worked upon on this ticket - https://tuispecialist.atlassian.net/browse/QE-675 --}}
<div class="expedition-seach__recent-searches">
	<h4><x-escape :content="$title" /></h4>
	<x-mini-cards-list>
		<x-mini-cards-list.card>
			<x-mini-cards-list.card-image image_id="120" />
			<x-mini-cards-list.card-info>
				<x-mini-cards-list.card-title title="Antarctic Peninsula" />
				<x-mini-cards-list.card-date date="June 2024" />
			</x-mini-cards-list.card-info>
		</x-mini-cards-list.card>
		<x-mini-cards-list.card>
			<x-mini-cards-list.card-image image_id="87" />
			<x-mini-cards-list.card-info>
				<x-mini-cards-list.card-title title="Patagonia" />
				<x-mini-cards-list.card-date date="June 2025" />
			</x-mini-cards-list.card-info>
		</x-mini-cards-list.card>
		<x-mini-cards-list.card>
			<x-mini-cards-list.card-image image_id="108" />
			<x-mini-cards-list.card-info>
				<x-mini-cards-list.card-title title="Svalbard" />
				<x-mini-cards-list.card-date date="January 2025" />
			</x-mini-cards-list.card-info>
		</x-mini-cards-list.card>
	</x-mini-cards-list>
</div>
