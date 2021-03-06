@extends('adminpanel.layout')
@section('title','Diagnostics | HospitALL')
@section('content')
<div class="container-fluid px-xl-5">
  	<section class="py-5">
	    <div class="row">
	    	@include('adminpanel.notification')
	      	<div class="col-lg-12">
		        <div class="card">
		          <div class="card-header">
		            <h6 class="text-uppercase mb-0">Diagnostics <a href="{{ route('diagnostics.create') }}" class="btn btn-sm btn-dark float-right">Create New</a></h6>
		          </div>
		          <div class="card-body table-responsive">
		            <table class="table table-striped table-sm card-text" id="diagnostic">
		              <thead class="thead-light">
		                <tr>
		                  <th>#</th>
		                  <th>Diagnostics Name</th>
		                  <th>Description</th>
  		                  <th>Status</th>
		                  <th>Restore</th>
		                  <th>Delete</th>
		                </tr>
		              </thead>
		              <tbody>
		              	@if($diagnostics)
		              		@php $no=1 @endphp
		              		@foreach($diagnostics as $diagnostic)
		                    <tr>
		                      <th scope="row">{{$no++}}</th>
		                      <td>{{ $diagnostic->name }}</td>
		                      <td>{!! str_limit($diagnostic->description,50) !!}</td>
		                      <td>{{ $diagnostic->is_active == 1 ? 'Active' : 'Not Active' }}</td>
		                      <td><a class="restore" data-id="{{ $diagnostic->id }}" data-toggle="tooltip" title="Restore" href="#"><i class="fas fa-undo"></i></a>
                                <form id="restoreForm{{$diagnostic->id}}" method="post" action="{{ route('diagnostic_restore', $diagnostic->id) }}">
                                  @csrf @method('post')
                                </form>
                            </td>
		                      <td>
		                      	<a class="delete" data-id="{{ $diagnostic->id }}" href="#"><i class="fa fa-trash"></i></a>
		                      	<form id="deleteForm{{$diagnostic->id}}" method="post" action="{{ route('diagnostic_per_delete', $diagnostic->id) }}">
									@csrf @method('post')
								</form>
		                      </td>
		                    </tr>
		                	@endforeach
		                @endif
		              </tbody>
		            </table>
		          </div>
		        </div>
	      	</div>
	    </div>
  	</section>
</div>
@endsection
@section('scripts')
<script src="{{ asset('backend/js/sweetalert/sweetalert.js') }}"></script>
<script>
$(document).on('click', '.restore', function(){
    var id = $(this).data('id');
    swal({
        title: "Are you sure?",
        text: "You want to Restore!",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes, Restore it!",
        closeOnConfirm: false,
        showLoaderOnConfirm: true
        },
        function(){
            setTimeout(function () {
        $('#restoreForm'+id).submit();
        swal("Deleted!", "Status has been deleted.", "success");
    }, 2000);
        });
});
</script>
<script>
$(document).on('click', '.delete', function(){
    var id = $(this).data('id');
    swal({
        title: "Are you sure?",
        text: "You want to delete Diagnostic!",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes, delete it!",
        closeOnConfirm: false,
        showLoaderOnConfirm: true
        },
        function(){
            setTimeout(function () {
        $('#deleteForm'+id).submit();
        swal("Deleted!", "Diagnostic has been deleted.", "success");
    }, 2000);
        });
});
</script>
<script>
$(document).ready(function() {
    $('#diagnostic').DataTable();
    setTimeout(function() {
	    $('.alert').fadeOut('slow');
	}, 2000);
});
</script>
@endsection
