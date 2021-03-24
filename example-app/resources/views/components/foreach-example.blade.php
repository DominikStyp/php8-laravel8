@foreach (range(1,10) as $el)
    @if ($loop->first)
        <div>First element is {{ $el }}</div>
    @endif

    @if ($loop->last)
        <div>Last element is {{ $el }}</div>
    @endif

    @continue($el === 3)

    @break($el === 8)
    {{-- usage of parent loop property --}}
    {{-- following comments won't be shown in HTML body --}}
    @foreach(range(1,3) as $insideEl)
        @if($loop->parent->first)
            <div>&nbsp; &nbsp; This is parent's 1 element (nesting level currently: {{ $loop->depth }})</div>
        @endif
    @endforeach

    <div> Current element is {{ $el }} (remaining {{ $loop->remaining }})</div>

@endforeach


<?php $testArr = [] ?>

@forelse ($testArr as $el)
     {{ $el }}
@empty
    <div>
        TestArr is empty!
    </div>
@endforelse
