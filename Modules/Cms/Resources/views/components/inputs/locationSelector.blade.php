@if ( isset( $options ) )
    @php
        $opt = array_merge([
            'name'        => 'map',
            'value_lat'   => null,
            'value_lng'   => null,
            'help'        => null,
            'label'       => null,
            'placeholder' => null,
            'min-height'  => '240px',
            'default_lat' => 37.06290234253171,
            'default_lng' => 37.37910934979243,
            'zoom'        => 9.5,
            'zoomControl' => 'true',
            'zoom_position' => 'TOP_LEFT',
            'zoom_style'    => 'SMALL',
            'scripts'     => true,
            'required'    => false,
        ], $options);

        $lng = null;
        $lat = null;
    @endphp

    <div class="form-group form-md-line-input{{ $errors->has( $opt['name'] ) ? ' has-error' : '' }}">
        @if ( ! is_null( $opt['label'] ) )
            <label class="control-label" for="{{ $opt['name'] }}">
                <strong class="text-primary">{!! $opt['label'] . ($opt['required'] ? ' <span class="text-danger">*</span>' : '') !!}</strong>
            </label>
        @else
            <label class="control-label" for="{{ $opt['name'] }}">
                <strong class="text-primary">{!! $opt['name'] . ($opt['required'] ? ' <span class="text-danger">*</span>' : '') !!}</strong>
            </label>
        @endif
        <input id="{{ $opt['name'] }}_lng" name="{{ $opt['name'] }}_lng" value="{{ $opt['value_lng'] }}" type="hidden">
        <input id="{{ $opt['name'] }}_lat" name="{{ $opt['name'] }}_lat" value="{{ $opt['value_lat'] }}" type="hidden">
        <div id="{{ $opt['name'] }}_div" style="min-height: {!! $opt['min-height'] !!}; min-width: 100%;"></div>
        <div class="form-control-focus"> </div>
        @if ( $errors->has( $opt['name'] ) )
            <div class="alert alert-error alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                <strong>{{ __('backend.error') }} </strong> {{ $errors->first($opt['name']) }}
            </div>
        @else
            @if ( ! is_null( $opt['help'] ) )
                <span class="m-form__help">
                    {{ $opt['help'] }}
                </span>
            @endif
        @endif
    </div>
    @push('scripts')

        @if($opt['scripts'])
            <script src="https://maps.google.com/maps/api/js?key={!! config('cms.google_map_api_key') !!}" type="text/javascript"></script>
            <script src="{{ \Module::asset('cms:vendors/custom/gmaps/gmaps_class.js') }}" type="text/javascript"></script>
        @endif
        
        <script>
            var map2;
            $(document).ready(function(){
                var addedMarker = false;
                map2 = new GMaps({
                    el: '#{{ $opt['name'] }}_div',
                    lat: {{ $opt['default_lat'] }},
                    lng: {{ $opt['default_lng'] }},
                    zoom: {{ $opt['zoom'] }},
                    zoomControl : {{ $opt['zoomControl'] }},
                    zoomControlOpt: {
                        style : '{{ $opt['zoom_style'] }}',
                        position: '{{ $opt['zoom_position'] }}'
                    },
                    panControl : false,
                    click: function(event){
                        if ( addedMarker ) {
                            swal({
                                title: '{!! __('cms::base.are_you_sure') !!}',
                                text: '{!! __('cms::base.wont_be_reverted') !!}',
                                type: 'warning',
                                showCancelButton: true,
                                confirmButtonText: '{!! __('cms::base.yes') !!}',
                                cancelButtonText: '{!! __('cms::base.cancel') !!}',
                                reverseButtons: true
                            }).then(function(result){
                                if (result.value) {
                                    map2.removeMarkers();
                                    addedMarker = map2.addMarker({
                                        lat: event.latLng.lat(),
                                        lng: event.latLng.lng(),
                                    });
                                    map2.setCenter( event.latLng.lat(), event.latLng.lng() );
                                    $('#{{ $opt['name'] }}_lng').val( event.latLng.lng() );
                                    $('#{{ $opt['name'] }}_lat').val( event.latLng.lat() );
                                } else {
                                    map2.setCenter( addedMarker.position.lat(), addedMarker.position.lng() );
                                }
                            });
                        }
                        else
                        {
                            map2.removeMarkers();
                            addedMarker = map2.addMarker({
                                lat: event.latLng.lat(),
                                lng: event.latLng.lng(),
                            });
                            map2.setCenter( event.latLng.lat(), event.latLng.lng() );
                            $('#{{ $opt['name'] }}_lng').val( event.latLng.lng() );
                            $('#{{ $opt['name'] }}_lat').val( event.latLng.lat() );
                        }
                    },
                    dragend: function(e){

                    }
                });
                @if ( !is_null( $opt['value_lat'] ) && !is_null( $opt['value_lng'] ) )
                    addedMarker = map2.addMarker({
                        lat: {{ $opt['value_lat'] }},
                        lng: {{ $opt['value_lng'] }},
                    });
                    map2.setCenter( {{ $opt['value_lat'] }}, {{ $opt['value_lng'] }} );
                @endif
            });
        </script>

    @endpush
@endif
