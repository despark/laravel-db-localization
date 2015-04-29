@extends('laravel-db-localization::layouts.master')

@section('content')
<div class="col-md-12">

<p>{{ link_to_route('localization_example.create', 'Add new contact',array(), array('class' => 'btn btn-info')) }}</p>

<div class="table-responsive">
    <table class="table table-striped table-bordered table-condensed table-hover">
        <thead>
            <tr>
                <th> {{ Form::label('name', 'Name') }} </th>
                <th> {{ Form::label('location', 'Location') }} </th>
                <th> {{ Form::label('fax', 'Fax') }} </th>
                <th> {{ Form::label('phone', 'Phone') }} </th>
                <th colspan="2"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($contacts as $contact)
            <tr>
                <td>{{ (isset($contact->translate(\App::getLocale())->name)) ? $contact->translate(\App::getLocale())->name : NULL }}</td>
                <td>{{ (isset($contact->translate(\App::getLocale())->location)) ? $contact->translate(\App::getLocale())->location : NULL}}</td>
                <td>{{ $contact->phone }}</td>
                <td>{{ $contact->fax }}</td>
                <td>
                    {{ link_to_route('localization_example.edit', 'Edit', array($contact->id), array('class' => 'btn btn-primary')) }}
                </td>
                <td>
                    {{ Form::open(array('method' => 'DELETE', 'route' => array('localization_example.destroy', $contact->id))) }}
                    {{ Form::submit('Delete',array('class' => 'btn btn-danger'))}}
                    {{ Form::close() }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>
@stop
