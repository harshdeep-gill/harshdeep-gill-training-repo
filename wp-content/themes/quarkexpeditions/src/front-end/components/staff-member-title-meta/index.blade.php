@props( [
	'name'  => '',
	'roles' => '',
] )

@php
	if ( empty( $name ) ) {
		return;
	}
@endphp

<div class="staff-member-title-meta">
	<h1 class="staff-member-title-meta__title">
		{{ $name }}
	</h1>

	@if ( ! empty( $roles ) )
		<p class="staff-member-title-meta__roles h5">
			{{ $roles ?? '' }}
		</p>
	@endif
</div>
