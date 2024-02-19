@props( [
	'image_id'  => 0,
	'title'     => '',
	'sub_title' => '',
	'immersive' => false,
] )

@php
	if ( empty( $image_id ) ) {
		return;
	}
@endphp

<x-hero :immersive="$immersive">
	<x-hero.image :image_id="$image_id" />
	<x-hero.content>
		@if ( ! empty( $title ) )
			<x-hero.title :title="$title" />
		@endif
		@if ( ! empty( $sub_title ) )
			<x-hero.sub-title :title="$sub_title" />
		@endif
	</x-hero.content>
	<x-hero.form>
		{!! $slot !!}
	</x-hero.form>
</x-hero>
