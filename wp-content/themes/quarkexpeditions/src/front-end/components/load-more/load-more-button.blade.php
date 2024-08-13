@props( [
	'load_more_text' => '',
] )

<div class="load-more__button-container">
	<x-button size="big" appearance="outline" class="load-more__button" :loading="true">
		<x-escape :content="$load_more_text" />
	</x-button>
</div>
