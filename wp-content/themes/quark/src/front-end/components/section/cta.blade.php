@props( [
	'url'        => '',
	'text'       => '',
	'new_window' => false,
] )

<div class="section__cta-button">
	<x-button
		:href="$url"
		:target="! empty( $new_window ) ? '_blank' : ''"
	><x-escape :content="$text" /></x-button>
</div>
