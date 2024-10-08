@props( [
	'title' => __( 'Your recent searches', 'qrk' ),
] )

<div class="expedition-seach__recent-searches">
	<h4 class="h4"><x-escape :content="$title" /></h4>
	<temlate>
		<x-mini-cards-list>
			<x-mini-cards-list.card>
				<x-mini-cards-list.card-image image_id="120" />
				<x-mini-cards-list.card-info>
					<x-mini-cards-list.card-title title="Antarctic Peninsula" />
					<x-mini-cards-list.card-date date="June 2024" />
				</x-mini-cards-list.card-info>
			</x-mini-cards-list.card>
		</x-mini-cards-list>
	</temlate>
</div>
