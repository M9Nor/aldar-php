<div class="kt-widget kt-widget--user-profile-3">
    <div class="kt-widget__top">
        <div class="kt-widget__media kt-hidden-">
            <img src="{{ $model['image'] }}" alt="image">
        </div>
        <div class="kt-widget__pic kt-widget__pic--danger kt-font-danger kt-font-boldest kt-font-light kt-hidden">
            JM
        </div>
        <div class="kt-widget__content">
            <div class="kt-widget__head">
                <a href="{{ route('UserController@show', ['model' => $model['id']]) }}" class="kt-widget__username">
                    {{ $model['full_name'] }}
                    <i class="flaticon2-correct kt-font-success"></i>
                </a>
                <div class="kt-widget__action">
                    <a href="{{ route('UserController@edit', ['model' => $model['id']]) }}" class="btn btn-label-brand btn-sm btn-upper">{{ __('cms::global.edit') }}</a>
                </div>
            </div>
            <div class="kt-widget__subhead">
                <span>
                    @foreach ($model['roles_array'] as $role)
                        <span class="kt-badge kt-badge--inline" style="color: #fff; background: {{ $role['color'] }}">{{ $role['title'] }}</span>
                    @endforeach
                </span>
                [<a class="p-0" href="mailto:{{ $model['email'] }}">
                    <span>{{ $model['email'] }}</span>
                </a>]
                @if(isset($model['country']))
                    <a href="#"><i class="flaticon2-placeholder"></i> {{ $model['country'] }}</a>
                @endif
            </div>
        </div>
    </div>
</div>
