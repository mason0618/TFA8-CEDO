@if ($errors->any())
    <div class="error-summary" role="alert">
        <p>Please correct the following:</p>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
