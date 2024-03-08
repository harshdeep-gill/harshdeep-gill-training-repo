@props( [
	'icon'             => '',
	'text'             => '',
	'background_color' => '',
] )

<x-icon-badge :background_color="$background_color" class="hero__tag" :icon="$icon" :text="$text" />
