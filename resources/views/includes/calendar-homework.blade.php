@forelse($homework as $hw)
    {!! $hw->text !!}
@empty
    <p class="text-muted">- нет домашнего задания -</p>
@endforelse