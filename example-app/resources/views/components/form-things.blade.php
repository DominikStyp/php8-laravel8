<div>
    <form action="/PUT_SOMETHING" method="POST">
        {{-- following checks if there is any error on title field --}}
        <label for="title">Title: </label>
        <input id="title" type="text" class="@error('title') is-invalid @enderror">
        @method('PUT') {{-- this indicates that _method field is added --}}
        @csrf
        <button type="submit">Send me!</button>

    </form>
</div>
