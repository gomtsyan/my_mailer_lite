@extends('layouts.public')

@section('page-css')
    <link href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css"
          rel="stylesheet"
          crossorigin="anonymous">
@endsection

@section('content')
    <div class="container card">
        <div class="row">
            <div class="col-md-12">
                <div class="card text-left">
                    <div class="card-body">
                        <h4 class="card-title mb-3">Subscribers</h4>
                        <a href="{{url('subscribers/create')}}" class="btn btn-success btn-sm m-1">Create Subscriber</a>
                        <div class="table-responsive">
                            <table id="sample_1" class="display table table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr class="uppercase">
                                    <th>Email</th>
                                    <th>Name</th>
                                    <th>Country</th>
                                    <th>Subscribe Date</th>
                                    <th>Subscribe Time</th>
                                    <th></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--Edit Modal--}}
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Subscriber</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editSubscriber">
                        <div class="mb-3">
                            <label for="name" class="col-form-label">Name:</label>
                            <input type="text" class="form-control" id="name" name="name">
                        </div>
                        <div class="mb-3">
                            <label for="country" class="col-form-label">Country:</label>
                            <input type="text" class="form-control" id="country" name="country">
                        </div>
                        <input type="hidden" id="subscriberId" name="id">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="edit" class="btn btn-primary">Edit</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-js')
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"
            integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ=="
            crossorigin="anonymous"
            referrerpolicy="no-referrer"></script>
    <script>
        let myModal = new bootstrap.Modal(document.getElementById('exampleModal'));
        let tableSelector = $('#sample_1');
        let dTable;
        $(document).ready(function() {
            dTable = tableSelector.DataTable( {
                bStateSave: false,
                processing: true,
                serverSide: true,
                lengthMenu: [
                    [10, 20, 50],
                    [10, 20, 50] // change per page values here
                ],
                ajax: {
                    url: "{{url('api/v1/subscribers')}}?sessionId={{$sessionId ?? 0}}",
                    jsonp: 'callback',
                    dataType: "jsonp",
                    error: function( e, status, error) {
                        toastr.error(e.responseJSON.message, 'Error!');
                    }
                },
                columns: [
                    {data: "email", orderable: true},
                    {data: "name", orderable: true, className: "name-editable"},
                    {data: function (data, type, dataToSet) {
                            let country = '-';
                            if (data?.fields && data?.fields.length > 0) {
                                let countryObj = data?.fields.filter(item => {
                                    return item.key === 'country'
                                });
                                country = countryObj[0].value ? countryObj[0].value : '-'
                            }
                        return  country;
                        }, orderable: false, className: "country-editable"},
                    { data: function (data, type, dataToSet) {
                            return moment(data?.date_created, 'YYYY-MM-DD HH:mm:ss').format('DD/MM/YYYY')
                        }, orderable: false},
                    { data: function (data, type, dataToSet) {
                            return moment.utc(data?.date_created).utcOffset("{{ $timeOffset ?? '+00:00' }}")
                                .format('HH:mm:ss')
                        }, orderable: false},
                    { data: "id", orderable:false, render: function (id) {
                            return '<a class="btn btn-danger deleteItem" href="javascript:;" data-id="'+ id +'">' +
                                'Delete</a>';
                        }
                    }
                ],
                createdRow: function (row, data, dataIndex) {
                    $(row).attr('data-id', data.id);
                }
            });

            $(document).delegate("a.deleteItem", "click", function(e) {
                const subscriberId = $(this).data("id");
                const url = "{{ url('api/v1/subscribers') }}/" + subscriberId + "/delete";
                let sessionId = "{{$sessionId ?? 0}}";
                let data = {sessionId};
                $(this).prop('disabled', true);
                $.ajax({
                    url,
                    jsonp: 'callback',
                    dataType: "jsonp",
                    data,
                    success: function(response) {
                        if (response.status) {
                            dTable.ajax.reload();
                            toastr.success(response.message, 'Success!');
                        }
                    },
                    error: function(e, status, error) {
                        toastr.error(e.responseJSON.message, 'Error!');
                    }
                });
            });

            tableSelector.on('click', 'tbody td:first-child', function (e) {
                e.preventDefault();
                let parentTr = $(this).closest('tr');
                let id = parentTr.data('id');
                let name = parentTr.find('td.name-editable').text();
                let country = parentTr.find('td.country-editable').text();
                $('#name').val(name);
                $('#country').val(country);
                $('#subscriberId').val(id);
                myModal.show();
            });

            $(document).delegate("#edit", "click", function(e) {
                e.preventDefault()
                let editButton = $(this);
                let sessionId = "{{ $sessionId ?? '' }}";
                let id = $('#subscriberId').val();
                const url = "{{url('api/v1/subscribers')}}/" + id + "/edit";
                let fields = {sessionId};
                $.each($('#editSubscriber').serializeArray(), function(i, field) {
                    fields[field.name] = field.value;
                });
                editButton.prop('disabled', true);
                $.ajax({
                    url,
                    jsonp: 'callback',
                    dataType: "jsonp",
                    data: fields,
                    success: function(response) {
                        if (response.status) {
                            myModal.hide();
                            dTable.ajax.reload();
                            $('#name').val('');
                            $('#country').val('');
                            $('#subscriberId').val('');
                            editButton.prop('disabled', false);
                            toastr.success(response.message, 'Success!');
                        }
                    },
                    error: function( e, status, error) {
                        toastr.error(e.responseJSON.message, 'Error!');
                    }
                });
            });
        });
    </script>
@endsection
