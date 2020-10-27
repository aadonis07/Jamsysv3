@extends ('layouts.it-department.app')
@section ('title')
    Job Request View
@endsection
@section ('styles')
<link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
<link href="{{ asset('assets/css/formplugins/select2/select2.bundle.css') }}" rel="stylesheet" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
<style>
.select2-dropdown {
  z-index: 999999;
}
.help {cursor: help;}
</style>
@endsection
@section('breadcrumbs')
<li class="breadcrumb-item">Job Request</li>
<li class="breadcrumb-item">JR List</li>
<li class="breadcrumb-item active">JR View</li>
@endsection
@section('content')
@php 
    $new_design = countJrProducts($jr->id,'NEW-DESIGN');
    $reupholster = countJrProducts($jr->id,'REUPHOLSTER');
    $floor_plan = countJrProducts($jr->id,'FIT-OUT');
@endphp
<div class="row mb-3 ">
    <div class="col-lg-12 d-flex flex-start w-100 mb-2">
        <div class="mr-2 hidden-md-down">
            <span class="icon-stack icon-stack-lg">
                <i class="base base-6 icon-stack-3x opacity-100 color-primary-500"></i>
                <i class="base base-10 icon-stack-2x opacity-100 color-primary-300 fa-flip-vertical"></i>
            </span>
        </div>
        <div class="row d-flex flex-fill">
            <div class="col-lg-7 flex-fill">
                <span class="h5 mt-0">Job Request View</span>
                <br>
                <p class="mb-0">This is job request view. the products have separated.</p>
            </div>
            <div class="col-lg-5 form-group">
                <!-- <div class="input-group bg-white shadow-inset-2">
                    <input type="search" id="employee-search" class="form-control border-right-0 bg-transparent pr-0" placeholder="Search...">
                    <div class="input-group-append">
                        <span class="input-group-text bg-transparent border-left-0">
                            <i class="fal fa-search"></i>
                        </span>
                    </div>
                </div> -->
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>
                    Details for &nbsp; <b class="text-info">{{$jr->jr_number}}</b>
                </h2>
            </div>
            <div class="panel-container show">
                <div class="panel-content">
                    <div class="row">
                        <table class="table table-borderless">
                            <tr>
                                <th width="12%">JOB REQUEST STATUS</th>
                                <td>{{$jr->status}}</td>
                                <th width="12%">CLIENT</th>
                                <td>{{$jr->client->name}}</td>
                                @if($jr->quotation->status=='REJECTED')
                                <th width="15%">QUOTATION DATE REJECTED</th>
                                <td>{{date('F d,Y',strtotime($jr->quotation->date_rejected))}}</td>
                                @endif
                            </tr>
                            <tr>
                                <th>QUOTATION NUMBER</th>
                                <td>{{$jr->quotation->quote_number}}</td>
                                <th>QUOTATION STATUS</th>
                                <td>{{$jr->quotation->status}}</td>
                                @if($jr->quotation->status=='REJECTED')
                                <th>REJECTED BY</th>
                                <td>$jr->quotation->rejected_by</td>
                                @endif
                            </tr>
                            <tr>
                                <th>SALES EXECUTIVE</th>
                                <td>{{$jr->agent->user->employee->first_name." ".$jr->agent->user->employee->last_name}}</td>
                                <th>TENTATIVE DELIVERY DATE</th>
                                <td>{{date('F d,Y',strtotime($jr->quotation->lead_time))}}</td>
                                @if($jr->quotation->status=='R-REJECT')
                                <th width="15%">HOLD DATE | HOLD BY</th>
                                <td>{{date('F d,Y',strtotime($jr->quotation->hold_date)) ."|".$jr->quotation->hold_by}}</td>
                                @endif
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 p-2" align="right">
        <button class="btn btn-dark" data-toggle="modal" data-target="#add-floor-plan">
            <span class="fa fa-plus"></span>
            Add Floor Plan
        </button>
    </div>
    @if($new_design>0)
    <div class="col-lg-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr bg-fusion-50">
                <h2 class="text-white">
                    New Design 
                </h2>
            </div>
            <div class="panel-container show">
                <div class="panel-content">
                    <div class="table-responsive">
                        <table id="dt-NEW-DESIGN" class="table table-bordered w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 100%;">
                            <thead class="bg-warning-500 text-center">
                                <tr role="row">
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th width="50%">Details</th>
                                    <th width="50%">Revisions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @if($reupholster>0)
    <div class="col-lg-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>
                    Reupholster 
                </h2>
            </div>
            <div class="panel-container show">
                <div class="panel-content">
                    <div class="table-responsive">
                        <table id="dt-REUPHOLSTER" class="table table-bordered w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
                            <thead class="bg-warning-500 text-center">
                                <tr role="row">
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th width="50%">Details</th>
                                    <th width="50%">Revisions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @if($floor_plan>0)
    <div class="col-lg-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr bg-fusion-50">
                <h2 class="text-white">
                    Floor Plan
                </h2>
            </div>
            <div class="panel-container show">
                <div class="panel-content">
                    <div class="table-responsive">
                        <table id="dt-floor-plan" class="table table-bordered w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
                            <thead class="bg-warning-500 text-center">
                                <tr role="row">
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th width="50%">Details</th>
                                    <th width="50%">Revisions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
<!-- ================================================================================ -->
<div id="add-revision" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">
            Add Revision
            <small class="m-0 text-muted">
                Please input accurate deadline.
            </small>
        </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"><i class="fal fa-times"></i></span>
        </button>
    </div>
      <div class="modal-body">
        
            <div class="form-group">
                <label>Type</label>
                <select class="custom-select" name="jr-type" required>
                    <option value=""></option>
                    @foreach($jr_types as $jr_type)
                        <option value="{{$jr_type->id}}">{{$jr_type->name}}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback">Type is Required!</div>
            </div>
            <div class="form-group">
                <label>Deadline Date</label>
                <input type="text" class="form-control" required name="deadline-date" />
            </div>
            <div class="form-group">
                <label>Remarks</label>
                <textarea class="form-control" rows="5" name="revision-remarks" required></textarea>
            </div>
            <input class="form-control" type="hidden" name="jrProductID" required />
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success" id="addRevisionBtn" >Submit</button>
      </div>
    </div>
  </div>
</div>
<!-- ================================================================================ -->
<div id="add-floor-plan" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">
            Add Floor Plan
            <small class="m-0 text-muted">
                Please input accurate deadline.
            </small>
        </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"><i class="fal fa-times"></i></span>
        </button>
    </div>
      <div class="modal-body">
            <div class="form-group">
                <label>Type</label>
                <select class="custom-select" name="fp-type" required>
                    <option value=""></option>
                    @foreach($jr_types as $jr_type)
                        <option value="{{$jr_type->id}}">{{$jr_type->name}}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback">Type is Required!</div>
            </div>
            <div class="form-group">
                <label>Deadline Date</label>
                <input type="text" class="form-control" required name="fp-deadline-date" />
            </div>
            <div class="form-group">
                <label>Remarks</label>
                <textarea class="form-control" rows="5" name="fp-revision-remarks" required></textarea>
            </div>
            <input class="form-control" type="hidden" name="jrId" value="{{encryptor('encrypt',$jr->id)}}" required />
            <input class="form-control" type="hidden" name="jrType" value="FIT-OUT" required />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success" id="addFloorPlanBtn" >Submit</button>
      </div>
    </div>
  </div>
</div>
<!-- ================================================================================ -->
<div id="reason-delete-plan" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">
            Reason to Cancel
            <small class="m-0 text-muted">
                Please input a valid reason.
            </small>
        </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"><i class="fal fa-times"></i></span>
        </button>
    </div>
      <div class="modal-body">
            <div class="form-group">
                <label>Specify the reason :</label>
                <textarea class="form-control" rows="5" name="reason-remarks" required></textarea>
            </div>
            <input class="form-control" type="hidden" name="jrProductId" value="" required />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success" id="reasonSubmitBtn" >Submit</button>
      </div>
    </div>
  </div>
</div>
<!-- ================================================================================ -->
<div id="fix-image-modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">
            Fix Image ?
            <small class="m-0 text-muted">
                Please select the right image for this product.
            </small>
        </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"><i class="fal fa-times"></i></span>
        </button>
    </div>
      <div class="modal-body">
            <form id="update-image-form" method="POST" onsubmit="submitChangeImageBtn.disabled = true;" action="{{ route('job-request-functions',['id' => 'update-product-image']) }}" enctype="multipart/form-data">
            @csrf()
            <div id="image-content" align="center">
                
            </div>
            <div class="form-group">
                <div class="custom-file">
                    <input type="file" name="productsimg" required class="custom-file-input" onChange="readURL(this.id,'jrproduct-previewa','http://placehold.it/454x400')" id="jrproduct-img">
                    <label class="custom-file-label mt-2 bg-success text-white text-left" id="forLabel" for="customFile">Choose file</label>
                </div>
            </div>
            <input class="form-control" type="hidden" name="productId" value="" required />
            </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success" form="update-image-form" id="submitChangeImageBtn" >UPDATE</button>
      </div>
    </div>
  </div>
</div>
<!-- ================================================================================ -->
@endsection
@section('scripts')
<script src="{{  asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
<script src="{{ asset('assets/js/formplugins/select2/select2.bundle.js') }}"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>
<script>
$(function(){
    $('#dt-NEW-DESIGN').DataTable({
        "processing": true,
        "serverSide": true,
		"pageLength": 50,
        "width": "100%",
		"lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
        "ajax":{
            url :"{{ route('job-request-functions',['id' => 'jr-view-serverside']) }}",
            type: "POST",  
            data: {product_type:"NEW-DESIGN"},
            "processing": true,
            "serverSide": true,
            error: function(data){  // error handling
                $('#err').html(JSON.stringify(data));
            }
        },
        columns: [
            { data: 'jr_quotation_product.product_name', name: 'jr_quotation_product.product_name',visible:false},
            { data: 'jr_quotation_product.description', name: 'jr_quotation_product.description',visible:false},
            { data: 'status', name: 'status',visible:false},
            { data: 'deadline_date', name: 'deadline_date',visible:false},
            { data: 'designer_name', name: 'designer_name',visible:false},
            { data: 'details', name: 'details',orderable: false, searchable: false},
            { data: 'revision', name: 'revision',orderable: false, searchable: false},
        ]
    });
    $('#dt-REUPHOLSTER').DataTable({
        "processing": true,
        "serverSide": true,
		"pageLength": 50,
        "width": "100%",
		"lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
        "ajax":{
            url :"{{ route('job-request-functions',['id' => 'jr-view-serverside']) }}",
            type: "POST",  
            data: {product_type:"REUPHOLSTER"},
            "processing": true,
            "serverSide": true,
            error: function(data){  // error handling
                $('#err').html(JSON.stringify(data));
            }
        },
        columns: [
            { data: 'jr_quotation_product.product_name', name: 'jr_quotation_product.product_name',visible:false},
            { data: 'jr_quotation_product.description', name: 'jr_quotation_product.description',visible:false},
            { data: 'status', name: 'status',visible:false},
            { data: 'deadline_date', name: 'deadline_date',visible:false},
            { data: 'designer_name', name: 'designer_name',visible:false},
            { data: 'details', name: 'details',orderable: false, searchable: false},
            { data: 'revision', name: 'revision',orderable: false, searchable: false},
        ]
    });
});
$(document).ready(function(){
    $('select[name="jr-type"]').select2({
		placeholder: "Select Job Request Type",
		allowClear: true,
		width:"100%"
	});
    var date = new Date();
	date.setDate(date.getDate() - 1);
	$('input[name="deadline-date"]')
		.datepicker({
		format: 'yyyy-mm-dd',
		startDate: '+0d',
	});
    $('select[name="fp-type"]').select2({
		placeholder: "Select Job Request Type",
		allowClear: true,
		width:"100%"
	});
    var date = new Date();
	date.setDate(date.getDate() - 1);
	$('input[name="fp-deadline-date"]')
		.datepicker({
		format: 'yyyy-mm-dd',
		startDate: '+0d',
	});
    $(document).on('click','.add-revision',function(){
        var id = $(this).data('id');
        $('input[name="jrProductID"]').val(id);
       $('#add-revision').modal('show');
    });
    $('input[name="deadline-date"]').keyup(function() {
        $(this).attr('val', '');
    });
    $(document).on('click','#addRevisionBtn',function(){
        var jr_type = $('select[name="jr-type"]').find(':selected').val();
        var deadline = $('input[name="deadline-date"]').val();
        var remarks = $('textarea[name="revision-remarks"]').val();
        var id = $('input[name="jrProductID"]').val();

        if($.trim(jr_type)){
            $('select[name="jr-type"]').removeClass("is-invalid");
            $('select[name="jr-type"]').addClass("is-valid");
            if($.trim(deadline)){
                $('input[name="deadline-date"]').removeClass("is-invalid");
                $('input[name="deadline-date"]').addClass("is-valid");
                if($.trim(remarks)){
                    $('textarea[name="revision-remarks"]').removeClass("is-invalid");
                    $('textarea[name="revision-remarks"]').addClass("is-valid");
                    $.post("{{ route('job-request-functions', ['id' => 'add-revision']) }}",
                    {id: id,jr_type:jr_type,deadline:deadline,remarks:remarks},
                    function(data){
                        console.log(data);
                        console.log(data['success']);
                        console.log(data['jrtype']);
                        if(data['success'] == 1){
                            $('#add-revision').modal('toggle');
                            $('select[name="jr-type"]').removeClass("is-valid");
                            $('input[name="deadline-date"]').removeClass("is-valid");
                            $('textarea[name="revision-remarks"]').removeClass("is-valid");
                            $('input[name="deadline-date"]').val("");
                            $('textarea[name="revision-remarks"]').val("");
                            $('select[name="jr-type"]').val('').trigger('change');
                            alert_message("Success",data['message'],'success');
                            if(data['type']=='FIT-OUT'){

                            }else{
                                var tb_type = data['jrtype'];
                                $("#dt-"+tb_type).dataTable().fnDestroy();
                                $('#dt-'+tb_type).DataTable({
                                    "processing": true,
                                    "serverSide": true,
                                    "pageLength": 50,
                                    "width": "100%",
                                    "lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
                                    "ajax":{
                                        url :"{{ route('job-request-functions',['id' => 'jr-view-serverside']) }}",
                                        type: "POST",  
                                        data: {product_type:data['jrtype']},
                                        "processing": true,
                                        "serverSide": true,
                                        error: function(data){  // error handling
                                            $('#err').html(JSON.stringify(data));
                                        }
                                    },
                                    columns: [
                                        { data: 'jr_quotation_product.product_name', name: 'jr_quotation_product.product_name',visible:false},
                                        { data: 'jr_quotation_product.description', name: 'jr_quotation_product.description',visible:false},
                                        { data: 'status', name: 'status',visible:false},
                                        { data: 'deadline_date', name: 'deadline_date',visible:false},
                                        { data: 'designer_name', name: 'designer_name',visible:false},
                                        { data: 'details', name: 'details',orderable: false, searchable: false},
                                        { data: 'revision', name: 'revision',orderable: false, searchable: false},
                                    ]
                                });
                            }

                        }else{
                            alert_message("Failed",data['message'],'danger');
                        }
                    });
                }else{
                    $('textarea[name="revision-remarks"]').addClass("is-invalid");
                }
            }else{
                $('input[name="deadline-date"]').addClass("is-invalid");
            }
        }else{
            $('select[name="jr-type"]').addClass("is-invalid");
        }
    });
    $(document).on('click','.delete-revision',function(){
        var id = $(this).data('id');
        var deltype = $(this).data('deltype');
        $(this).prop('disabled', true);
        Swal.fire({
            title: 'Confirm Delete',
            text: "Are you sure you want to delete this revision ?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes!, Delete This Revision.'
        }).then((result) => {
            if (result.value) {
                $.post("{{ route('job-request-functions', ['id' => 'cancel-revision']) }}",
                {id: id,deltype:deltype},
                function(data){
                    if(data['success']==1){
                        $('#'+id).remove();
                        alert_message("Success",data['message'],'success');
                        $(this).prop('disabled', false);
                        var tb_type = data['jrtype'];
                        $("#dt-"+tb_type).dataTable().fnDestroy();
                        $('#dt-'+tb_type).DataTable({
                            "processing": true,
                            "serverSide": true,
                            "pageLength": 50,
                            "width": "100%",
                            "lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
                            "ajax":{
                                url :"{{ route('job-request-functions',['id' => 'jr-view-serverside']) }}",
                                type: "POST",  
                                data: {product_type:data['jrtype']},
                                "processing": true,
                                "serverSide": true,
                                error: function(data){  // error handling
                                    $('#err').html(JSON.stringify(data));
                                }
                            },
                            columns: [
                                { data: 'jr_quotation_product.product_name', name: 'jr_quotation_product.product_name',visible:false},
                                { data: 'jr_quotation_product.description', name: 'jr_quotation_product.description',visible:false},
                                { data: 'status', name: 'status',visible:false},
                                { data: 'deadline_date', name: 'deadline_date',visible:false},
                                { data: 'designer_name', name: 'designer_name',visible:false},
                                { data: 'details', name: 'details',orderable: false, searchable: false},
                                { data: 'revision', name: 'revision',orderable: false, searchable: false},
                            ]
                        });
                    }else{
                        $(this).prop('disabled', false);
                    }
                });
            }else{
                $(this).prop('disabled', false);
            }
        });
    });
    $(document).on('click','.delete-revision-withwork',function(){
        var id = $(this).data('id');
        $('input[name="jrProductId"]').val(id);
        $('#reason-delete-plan').modal('show');
    });
    $(document).on('click','#reasonSubmitBtn',function(){
        var id = $('input[name="jrProductId"]').val();
        var reason_cancelled = $('textarea[name="reason-remarks"]').val();
        $(this).prop('disabled', true);
        if($.trim(reason_cancelled)){
            $('textarea[name="reason-remarks"]').removeClass('is-invalid');
            $('textarea[name="reason-remarks"]').addClass('is-valid');
            $.post("{{ route('job-request-functions', ['id' => 'cancel-revision']) }}",
            {id: id,reason_cancelled:reason_cancelled,deltype:'1'},
            function(data){
                console.log(data);
                $(this).prop('disabled', false);
                if(data['success']==1){
                    alert_message("Success",data['message'],'success');
                    var tb_type = data['jrtype'];
                    $("#dt-"+tb_type).dataTable().fnDestroy();
                    $('#dt-'+tb_type).DataTable({
                        "processing": true,
                        "serverSide": true,
                        "pageLength": 50,
                        "width": "100%",
                        "lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
                        "ajax":{
                            url :"{{ route('job-request-functions',['id' => 'jr-view-serverside']) }}",
                            type: "POST",  
                            data: {product_type:data['jrtype']},
                            "processing": true,
                            "serverSide": true,
                            error: function(data){  // error handling
                                $('#err').html(JSON.stringify(data));
                            }
                        },
                        columns: [
                            { data: 'jr_quotation_product.product_name', name: 'jr_quotation_product.product_name',visible:false},
                            { data: 'jr_quotation_product.description', name: 'jr_quotation_product.description',visible:false},
                            { data: 'status', name: 'status',visible:false},
                            { data: 'deadline_date', name: 'deadline_date',visible:false},
                            { data: 'designer_name', name: 'designer_name',visible:false},
                            { data: 'details', name: 'details',orderable: false, searchable: false},
                            { data: 'revision', name: 'revision',orderable: false, searchable: false},
                        ]
                    });
                }else{
                    alert_message("Failed!",'Delete Has Been Failed.','danger');
                }

            });
        }else{
            $('textarea[name="reason-remarks"]').addClass('is-invalid');
        }
    });
    $(document).on('click','.fixed-image',function(){
        var id = $(this).data('id');
        $('input[name="productId"]').val(id);
        $('#image-content').html('');
        $('#image-content').html('' +
            '<div class="col-md-12 mt-4">'+
            '    <div class="d-flex justify-content-center">'+
            '        <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">'+
            '            <span class="sr-only">Loading...</span>'+
            '        </div>'+
            '    </div>'+
            '</div>'+
            '');
        $('input[name="productsimg"]').val('');
        $('#forLabel').text('Choose file');
        $("#image-content").load("{{ route('job-request-product-image') }}?product_id="+id, function(){

        });
        $('#fix-image-modal').modal('show');
    });
});
</script>
@endsection