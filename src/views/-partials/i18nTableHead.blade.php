<ul class="nav nav-tabs" role="tablist">
    @foreach($i18ns as $i18n)
        @if($i18n->locale === \App::getLocale())
            <li role="presentation" class="active">
        @else
            <li role="presentation">
        @endif
        <a href="#{{$i18n->name}}" aria-controls="{{$i18n->name}}" role="tab" data-toggle="tab">{{$i18n->name}}</a></li>
    @endforeach
</ul>
