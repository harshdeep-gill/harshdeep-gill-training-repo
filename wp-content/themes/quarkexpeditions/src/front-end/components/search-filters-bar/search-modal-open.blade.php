@props( [
	'label'    => '',
	'placeholder' => __( 'Select', 'qrk' ),
] )

<div type="button" class="search-filters-bar__modal-open-button" selected="false">
	<div class="search-filters-bar__modal-open-button-content">
		<div class="search-filters-bar__modal-open-button-label body-small">{{ __( $label, 'qrk' ) }}</div>
		<div class="search-filters-bar__modal-open-button-placeholder">{{ __( $placeholder, 'qrk' ) }}</div>
	</div>
	<div class="search-filters-bar__modal-open-button-icon">
		<x-svg name="chevron-left" />
	</div>
</div>