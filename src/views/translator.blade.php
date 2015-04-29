@extends('laravel-db-localization::layouts.master')

@section('content')
    <div class="col-md-4">
        @if(!empty($i18ns))
            @yield('i18nTableHead');
            {{ Form::open(array('route' => 'laravel-db-localization::contacts.store')) }}
            <div class="tab-content">
                @foreach($i18ns as $i18n)
                    @if($i18n->locale === \App::getLocale())
                      <div role="tabpanel" class="tab-pane active" id="{{$i18n->name}}">
                    @else
                        <div role="tabpanel" class="tab-pane" id="{{$i18n->name}}">
                    @endif
                            <div class="form-group">
                              {{ Form::label('name', 'Name:') }}
                              {{ Form::text("name[name_$i18n->id]", (isset($model->name)) ? $model->name : null, ['class' => 'form-control']) }}
                            </div>
                            <div class="form-group">
                                {{ Form::label('description', 'Description:') }}
                                {{ Form::textarea("description[description_$i18n->id]", (isset($model->description)) ? $model->description : null, ['class' => 'form-control']) }}
                            </div>
                        </div>
                @endforeach
                     </div>

              <div class="form-group">
                  {{ Form::label('fax', 'Fax:') }}
                  {{ Form::text("fax", (isset($model->fax)) ? $model->fax : null, ['class' => 'form-control']) }}
              </div>

              <div class="form-group">
                  {{ Form::label('phone', 'Phone:') }}
                  {{ Form::text("phone", (isset($model->phone)) ? $model->phone : null, ['class' => 'form-control']) }}
              </div>

            </div>

            {{ Form::submit('Send') }}
            {{ Form::close() }}
        @endif
    </div>
@stop
