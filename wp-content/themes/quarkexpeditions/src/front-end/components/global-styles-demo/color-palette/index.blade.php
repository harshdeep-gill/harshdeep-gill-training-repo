@php
	$colors = [
        'primary'   => [
            'black'     => '#232933',
            'white'     => '#fff',
            'yellow'    => '#fdb52b',
            'dark-blue' => '#2a5f8c',
            'blue'      => '#4c8bbf',
            'magenta'   => '#a26792',
        ],
        'grayscale' => [
            'gray-90' => '#383d49',
            'gray-80' => '#454c5b',
            'gray-70' => '#535b6d',
            'gray-60' => '#6c768e',
            'gray-50' => '#868fa3',
            'gray-40' => '#a8aebd',
            'gray-30' => '#c9cdd6',
            'gray-20' => '#dadee5',
            'gray-10' => '#eceef2',
            'gray-5'  => '#f5f7fb',
        ],
        'states'    => [
            'success-100'     => '#3a735d',
            'success-50'      => '#5bb291',
            'success-10'      => '#e6f2ee',
            'attention-100'   => '#c77413',
            'attention-50'    => '#f29b34',
            'attention-10'    => '#ffe5c7',
            'error-100'       => '#bf483b',
            'error-50'        => '#df5748',
            'error-10'        => '#fdddd9',
            'information-100' => '#4b5059',
            'information-50'  => '#808999',
            'information-10'  => '#fafbff',
        ],
    ];

@endphp

<div class="color-palette">
	<div class="color-palette__colors-row typography-spacing">
		<h3>{!! __( 'Primary Solids / Buttons', 'qrk' ) !!}</h3>
		<div class="color-palette__color-set">
			@foreach( $colors[ 'primary' ] as $primary_color => $primary_color_hex )
				<div class="color-palette__color">
					<div class="color-palette__box color-palette__box--{{ $primary_color }}"></div>
					<div class="color-palette__color-title">
						<p class="color-palette__color-text">{{ ucfirst( str_replace( '-', ' ', $primary_color ) ) }}</p>
						<p>{{ $primary_color_hex }}</p>
					</div>
				</div>
			@endforeach

		</div>
	</div>
</div>
<br/>

<div class="color-palette__colors-row typography-spacing">
	<h3>{!! __( 'Grayscale', 'qrk' ) !!}</h3>
	<div class="color-palette__color-set">
		@foreach( $colors[ 'grayscale' ] as $grayscale_color => $grayscale_color_hex)
			<div class="color-palette__color">
				<div class="color-palette__box color-palette__box--{{ $grayscale_color }}"></div>
				<div class="color-palette__color-title">
					<p class="color-palette__color-text">{{ ucfirst( str_replace( '-', ' ', $grayscale_color ) ) }}</p>
					<p>{{ $grayscale_color_hex }}</p>
				</div>
			</div>
		@endforeach
	</div>
</div>

<br/>

<div class="color-palette__colors-row typography-spacing">
	<h3>{!! __( 'State Colors', 'qrk' ) !!}</h3>
	<div class="color-palette__color-set">
		@foreach( $colors[ 'states' ] as $state_color => $state_color_hex )
			<div class="color-palette__color">
				<div class="color-palette__box color-palette__box--{{ $state_color }}"></div>
				<div class="color-palette__color-title">
					<p class="color-palette__color-text">{{ ucfirst( str_replace( '-', ' ', $state_color ) ) }}</p>
					<p>{{ $state_color_hex }}</p>
				</div>
			</div>
		@endforeach
	</div>
</div>
