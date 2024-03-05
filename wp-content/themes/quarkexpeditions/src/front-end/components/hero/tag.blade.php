@props( [
	'icon' => '',
	'text' => '',
] )

<div class="hero__tag color-context--dark">
	<span class="hero__tag-icon">
		<x-svg name="{{ $icon }}" />
	</span>
	<span class="hero__tag-description"><x-escape :content="$text" /></span>
</div>
