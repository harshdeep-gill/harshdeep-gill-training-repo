@props( [
	'image_id'  => 0,
	'title'     => '',
	'sub_title' => '',
	'immersive' => false,
	'show_form' => true,
] )

@php
	if ( empty( $image_id ) ) {
		return;
	}

	$size   = '';
	$layout = '';

	if ( empty( $show_form ) ) {
		$size   = 'big';
		$layout = 'column';
	}
@endphp

<x-hero :immersive="$immersive" :size="$size" :layout="$layout">
	<x-hero.image :image_id="$image_id" />
	<x-hero.content>
		@if ( ! empty( $title ) )
			<x-hero.title :title="$title" />
		@endif
		@if ( ! empty( $sub_title ) )
			<x-hero.sub-title :title="$sub_title" />
		@endif
	</x-hero.content>
	@if ( ! empty( $show_form ) )
		<x-hero.form>
			{!! $slot !!}
		</x-hero.form>
	@else
		<x-hero.form-modal-cta>
			{!! $slot !!}
		</x-hero.form-modal-cta>
	@endif
</x-hero>
