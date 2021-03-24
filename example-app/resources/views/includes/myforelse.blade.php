@php
if(!isset($arr)){
    $arr = [];
}
@endphp
@forelse($arr as $el)
    @if(is_array($el)) {{-- following will iterate over $el if it's an array, and put every element of it into 'sub_element' var --}}
        @each('includes.subarray', $el, 'sub_element')
    @else
        {!! $el !!}
    @endif

@empty
    <div>
        Array is empty!
    </div>
@endforelse
