@extends('layouts.public')

@section('content')
    <div class="container card">
        <form class="row g-3 card-body" id="check_key">
            <h5 class="card-title">Enter your API Key to start</h5>
            <div class="col-12">
                <label for="api_key" class="form-label">API Key</label>
                <input type="text" class="form-control" id="api_key"
                       placeholder="ex. 8fjdgtrljk5698cf6hg8f43942077c065c6f">
            </div>
            <div class="col-12">
                <button type="submit" id="submit" class="btn btn-primary">Check</button>
            </div>
        </form>
    </div>
@endsection

@section('page-js')
    <script>
        let sessionId = "{{ $sessionId ?? '' }}";

        $(document).ready(function() {
            $('#check_key').submit(function(e) {
                e.preventDefault()
                const url = "{{url('api/v1/check/key')}}";
                let apiKey = $('#api_key').val();
                if (apiKey === '') {
                    toastr.error("API-Key cannot be empty", "Attention!");
                    return
                }
                let data = {apiKey, sessionId};
                $('#submit').prop('disabled', true);
                $.ajax({
                    url,
                    jsonp: 'callback',
                    dataType: "jsonp",
                    data,
                    success: function(response) {
                        if (response.success) {
                            location.href = '{{ route("subscribers.index") }}'
                        }
                        $('#submit').prop('disabled', false);
                    },
                    error: function( e, status, error) {
                        toastr.error(e.responseJSON, 'Error!');
                        $('#submit').prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endsection
