@props( [
	'title'    => '',
	'subtitle' => '',
] )

@if ( ! empty( $title ) || ! empty( $subtitle ) )
	<header class="modal__header">
		@if ( ! empty( $title ) )
			<h3>{{ $title }}</h3>
		@endif
		@if ( ! empty( $subtitle ) )
			<p>{{ $subtitle }}</p>
		@endif
	</header>
@endif
