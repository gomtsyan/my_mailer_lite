@extends('layouts.public')

@section('content')
    <div class="container card">
        <div class="breadcrumb">
            <h1>Add new subscribers</h1>
        </div>
        <div class="separator-breadcrumb border-top"></div>
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <form id="createSubscriber">
                            <div class="row">
                                <div class="col-md-5 form-group mb-3">
                                    <label for="name">Name </label>
                                    <input type="text" class="form-control" id="name" placeholder="Enter name"
                                           name="name">
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <label for="email">Email</label>
                                    <input class="form-control" id="email" placeholder="Email" name="email" required>
                                </div>
                                <div class="col-md-5 form-group mb-3">
                                    <label for="country">Country </label>
                                    <input type="text" class="form-control" id="country" placeholder="Enter country name"
                                           name="country">
                                </div>
                                <br>
                                <div class="col-md-12">
                                    <button class="btn btn-primary" id="submit">Submit</button>
                                    <a href="{{url('subscribers')}}" class="btn btn-light" id="cancel"> Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-js')
    <script>
        let sessionId = "{{ $sessionId ?? '' }}";
        $(document).ready(function() {
            $('#createSubscriber').submit(function(e) {
                e.preventDefault()
                const url = "{{url('api/v1/subscribers/add')}}";
                let fields = {sessionId};
                $.each($(this).serializeArray(), function(i, field) {
                    fields[field.name] = field.value;
                });
                $('#submit').prop('disabled', true);
                $.ajax({
                    url,
                    jsonp: 'callback',
                    dataType: "jsonp",
                    data: fields,
                    success: function(response) {
                        if (response.status) {
                            $('#submit').prop('disabled', false);
                            $('#cancel').text('Go to Subscribers');
                            toastr.success(response.message, 'Success!');
                        }
                    },
                    error: function( e, status, error) {
                        $('#submit').prop('disabled', false);
                        toastr.error(e.responseJSON.message, 'Error!');
                    }
                });
            });
        });
    </script>
@endsection
