<!-- todo access check -->
@php
    $teacherParent = \Illuminate\Support\Facades\Auth::user()->role_id == App\User::TEACHER && \Illuminate\Support\Facades\Auth::user()->children()->exists()
    ?'parent'
    :'';
@endphp


@foreach(config('menu.menu') as $item)
    @if(
        in_array(\Illuminate\Support\Facades\Auth::user()->role->name, $item['roles'])
        || in_array($teacherParent, $item['roles'])
        || (\Illuminate\Support\Facades\Auth::user()->curator && in_array('curator', $item['roles']))
        ||  \Illuminate\Support\Facades\Auth::user()->role->name == 'admin'
        ||  \Illuminate\Support\Facades\Auth::user()->admin
        )
        <li>
            <a href="{{ $item['link'] }}" title="{{ $item['title'] }}">
                <i class="fa {{ $item['icon'] }}"></i>
                <span class="nav-label">{{ $item['title'] }}</span>
            </a>
        </li>
    @endif
@endforeach
