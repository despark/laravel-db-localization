@extends('laravel-db-localization::layouts.master')

@section('content')
    <div class="col-md-4">
        @if(!empty($i18ns))
            @include('laravel-db-localization::-partials.i18nTableHead', array('i18ns' => $i18ns))
            {{ Form::model($contact, array('method' => 'PUT', 'route' => array('translator_example.update', $contact->id))) }}
            <div class="tab-content">
            @include('laravel-db-localization::contacts.i18nTranslationForm', array('i18ns' => $i18ns, 'model' => $contact))
              <div class="form-group">
                  {{ Form::label('fax', 'Fax:') }}
                  {{ Form::text("fax", null, ['class' => 'form-control']) }}
              </div>
              <div class="form-group">
                  {{ Form::label('phone', 'Phone:') }}
                  {{ Form::text("phone", null, ['class' => 'form-control']) }}
              </div>
              {{ Form::submit('Update',array('class' => 'btn btn-success')) }}
            </div>
            {{ Form::close() }}
        @endif
    </div>
@stop
