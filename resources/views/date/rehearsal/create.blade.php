@extends('layouts.app')

@section('title'){{ trans('date.rehearsal_create_title') }}@endsection

@section('additional_js_files')
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDj7PNEdex5u2osEGzmZNlbz0p2bLmeVxU&libraries=places"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <h1>{{ trans('date.rehearsal_create_title') }}</h1>

            <div class="row">
                <div class="col-xs-12">
                    <div class="panel panel-2d">
                        <div class="panel-heading">{{ trans('date.rehearsal_create_title') }}</div>

                        @include('date.rehearsal.form', ['options' => ['url' => route('rehearsal.store'), 'method' => 'POST']])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        autocomplete = new google.maps.places.Autocomplete(document.getElementsByName('place').item(0), {
            types: ['address']
        });
    </script>
@endsection