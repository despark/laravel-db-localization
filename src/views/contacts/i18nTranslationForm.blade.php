@foreach($i18ns as $i18n)
    @if($i18n->locale === \App::getLocale())
        <div role="tabpanel" class="tab-pane active" id="{{$i18n->name}}">
    @else
        <div role="tabpanel" class="tab-pane" id="{{$i18n->name}}">
    @endif
            <div class="form-group">
                {{ Form::label('name', 'Name:') }}
                {{ Form::text("name[name_$i18n->id]",
                    (isset($model) AND isset($model->translate($i18n->locale)->name)) ? $model->translate($i18n->locale)->name : null,
                    ['class' => 'form-control'])
                }}
            </div>
            <div class="form-group">
                {{ Form::label('location', 'Location:') }}
                {{ Form::textarea("location[location_$i18n->id]",
                    (isset($model) AND isset($model->translate($i18n->locale)->location)) ? $model->translate($i18n->locale)->location : null,
                    ['class' => 'form-control'])
                }}
            </div>
        </div>
@endforeach
