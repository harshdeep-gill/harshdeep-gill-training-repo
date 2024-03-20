@props( [
	'image_id'   => 0,
	'immersive'  => false,
	'text_align' => '',
	'left'       => [
		'overline' => '',
		'title'    => '',
		'subtitle' => '',
		'tag'      => '',
		'cta'      => '',
	],
	'right'      => [
		'form' => '',
	],
] )

@php
	if ( empty( $image_id ) ) {
		return;
	}
@endphp

<x-hero :immersive="$immersive" :text_align="$text_align">
	<x-hero.image :image_id="$image_id" />
	<x-hero.content>
		<x-hero.left>
			<x-hero.title-container>
				@if ( ! empty( $left['overline'] ) )
					<x-hero.overline>{!! $left['overline'] !!}</x-hero.overline>
				@endif
				@if ( ! empty( $left['title'] ) )
					<x-hero.title :title="$left['title']" />
				@endif
				@if ( ! empty( $left['subtitle'] ) )
					<x-hero.sub-title :title="$left['subtitle']" />
				@endif
			</x-hero.title-container>

			@if ( ! empty( $left['tag'] ) )
				{!! $left['tag'] !!}
			@endif

			@if ( ! empty( $left['cta'] ) )
				{!! $left['cta'] !!}
			@endif
		</x-hero.left>
		<x-hero.right>
			@if ( ! empty( $right['form'] ) )
				<x-hero.form>
					{!! $right['form'] !!}
				</x-hero.form>
			@endif
		</x-hero.right>
	</x-hero.content>
</x-hero>
