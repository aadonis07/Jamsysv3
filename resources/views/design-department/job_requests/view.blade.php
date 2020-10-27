@extends ('layouts.design-department.app')
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
            <form id="update-image-form" method="POST" onsubmit="submitChangeImageBtn.disabled = true;" action="{{ route('design-job-request-functions',['id' => 'update-product-image']) }}" enctype="multipart/form-data">
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
<!-- start gelo added -->
<div id="floor-plan-image-modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">
            Add Floor Plan Image ?
            <small class="m-0 text-muted">
                Please select the right image for this floor plan.
            </small>
        </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"><i class="fal fa-times"></i></span>
        </button>
    </div>
      <div class="modal-body">
            <form id="add-floor-plan-image-form" method="POST" onsubmit="submitAddFPImageBtn.disabled = true;" action="{{ route('design-job-request-functions',['id' => 'add-floor-plan-image']) }}" enctype="multipart/form-data">
            @csrf()
            <div id="fp-image-content" align="center">
                <img class="img-fluid text-center" src="http://placehold.it/454x400" id="fp-img-preview" style="width: 400px;height:454px;border: 1px solid #0000000f;">
            </div>
            <div class="form-group">
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="fp-jrproduct-img" name="fp-jrproduct-img" required>
                    <label class="custom-file-label mt-2 bg-success text-white text-left" id="FPforLabel" for="customFile">Choose file</label>
                </div>
            </div>
            <input class="form-control" type="hidden" name="floorPlanProductId" value="" required />
            </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success" form="add-floor-plan-image-form" id="submitAddFPImageBtn">Submit</button>
      </div>
    </div>
  </div>
</div>
<!-- ================================================================================ -->
<div id="update-floor-plan-image-modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">
            Update Floor Plan Image ?
            <small class="m-0 text-muted">
                Please select the right image for this floor plan.
            </small>
        </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"><i class="fal fa-times"></i></span>
        </button>
    </div>
      <div class="modal-body">
            <form id="update-floor-plan-image-form" method="POST" onsubmit="submitUpdateFPImageBtn.disabled = true;" action="{{ route('design-job-request-functions',['id' => 'update-floor-plan-image']) }}" enctype="multipart/form-data">
            @csrf()
            <div id="update-fp-image-content" align="center">
            </div>
            <div class="form-group">
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="update-fp-jrproduct-img" name="update-fp-jrproduct-img" required>
                    <label class="custom-file-label mt-2 bg-success text-white text-left" id="updateFPforLabel" for="customFile">Choose file</label>
                </div>
            </div>
            <input class="form-control" type="hidden" name="updateFloorPlanProductId" value="" required />
            </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success" form="update-floor-plan-image-form" id="submitUpdateFPImageBtn">Update</button>
      </div>
    </div>
  </div>
</div>
<!-- ================================================================================ -->
<div id="add-designer-modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">
            Add Designer
            <small class="m-0 text-muted">
                Please fill in the required fields.
            </small>
        </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"><i class="fal fa-times"></i></span>
        </button>
    </div>
      <div class="modal-body">
        <form method="post" id="add-designer-form" enctype="multipart/form-data">
        @csrf()
            <div class="form-group">
                <label>Designer</label>
                <select class="custom-select" name="assigned-designer" required>
                    <option value=""></option>
                    @foreach($designers as $designer)
                        <option value="{{$designer->id}}">{{ $designer->employee->first_name }} {{ $designer->employee->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group row">
                <div class="col-lg-6">
                    <label>Estimated Date</label>
                    <input type="text" class="form-control" required name="estimated-finish-date" />
                </div>
                <div class="col-lg-6">
                    <label>Estimated Time</label>
                    <input type="time" class="form-control" required name="estimated-finish-time" />
                </div>
            </div>
            <div class="form-group">
                <label>Task</label>
                <textarea class="form-control" rows="5" name="assigned-task" required></textarea>
            </div>
            <input class="form-control" type="hidden" name="taskJrProductID" required />
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success" id="addDesignerBtn" >Submit</button>
      </div>
    </div>
  </div>
</div>
<!-- ================================================================================ -->
<div id="reason-reject-plan" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">
            Reason to Reject
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
                <textarea class="form-control" rows="5" name="reject-reason" required></textarea>
            </div>
            <input class="form-control" type="hidden" name="rejectJRProductId" value="" required />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success" id="rejectReasonSubmitBtn" >Submit</button>
      </div>
    </div>
  </div>
</div>
<!-- end gelo added -->
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
            url :"{{ route('design-job-request-functions',['id' => 'jr-view-serverside']) }}",
            type: "POST",  
            data: {id: '<?php echo $jr->id; ?>', product_type:"NEW-DESIGN"},
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
            url :"{{ route('design-job-request-functions',['id' => 'jr-view-serverside']) }}",
            type: "POST",  
            data: {id: '<?php echo $jr->id; ?>', product_type:"REUPHOLSTER"},
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
    //start gelo added
    $("#dt-floor-plan").DataTable({
        "processing": true,
        "serverSide": true,
        "pageLength": 50,
        "width": "100%",
        "lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
        "ajax":{
            url :"{{ route('design-job-request-functions',['id' => 'jr-floor-plan-serverside']) }}",
            type: "POST",
            data: {id: '<?php echo $jr->id; ?>'},
            "processing": true,
            "serverSide": true,
            error: function(data){  // error handling
                $('#err').html(JSON.stringify(data));
            }
        },
        columns: [
            { data: 'status', name: 'status',visible:false},
            { data: 'deadline_date', name: 'deadline_date',visible:false},
            { data: 'designer_name', name: 'designer_name',visible:false},
            { data: 'details', name: 'details',orderable: false, searchable: false},
            { data: 'revision', name: 'revision',orderable: false, searchable: false},
        ]
    });
    //end gelo added
});
$(document).ready(function(){
    $('select[name="jr-type"]').select2({
        placeholder: "Select Job Request Type",
        allowClear: true,
        width:"100%"
    });
    var date = new Date();
    date.setDate(date.getDate() - 1);
    $('select[name="fp-type"]').select2({
        placeholder: "Select Job Request Type",
        allowClear: true,
        width:"100%"
    });
    $('select[name="assigned-designer"]').select2({
        placeholder: "Select Designer",
        allowClear: true,
        width:"100%"
    });
    var date = new Date();
    date.setDate(date.getDate() - 1);
    $('input[name="estimated-finish-date"]')
        .datepicker({
        format: 'yyyy-mm-dd',
        startDate: '+0d',
        autoclose: true
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
                $.post("{{ route('design-job-request-functions', ['id' => 'cancel-revision']) }}",
                {id: id,deltype:deltype},
                function(data){
                    if(data['success']==1){
                        $('#'+id).remove();
                        alert_message("Success",data['message'],'success');
                        $(this).prop('disabled', false);
                        reload_dataTable(data['jrtype']);
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
            $.post("{{ route('design-job-request-functions', ['id' => 'cancel-revision']) }}",
            {id: id,reason_cancelled:reason_cancelled,deltype:'1'},
            function(data){
                console.log(data);
                $(this).prop('disabled', false);
                $('#reason-delete-plan').modal('toggle');
                if(data['success']==1){
                    alert_message("Success",data['message'],'success');
                    reload_dataTable(data['jrtype']);
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
        $("#image-content").load("{{ route('design-job-request-product-image') }}?product_id="+id, function(){

        });
        $('#fix-image-modal').modal('show');
    });
    //start gelo added
    $(document).on('click','.add-floor-plan-image',function(){
        var id = $(this).data('id');
        $('input[name="floorPlanProductId"]').val(id);
        $('#fp-img-preview').attr('src', 'http://placehold.it/454x400');
        $('input[name="fp-jrproduct-img"]').val('');
        $('#FPforLabel').text('Choose file');
        $('#floor-plan-image-modal').modal('show');
    });

    $(document).on('click','.update-floor-plan-image',function(){
        var id = $(this).data('id');
        $('input[name="updateFloorPlanProductId"]').val(id);
        $('#update-fp-image-content').html('');
        $('#update-fp-image-content').html('' +
            '<div class="col-md-12 mt-4">'+
            '    <div class="d-flex justify-content-center">'+
            '        <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">'+
            '            <span class="sr-only">Loading...</span>'+
            '        </div>'+
            '    </div>'+
            '</div>'+
            '');
        $("#update-fp-image-content").load("{{ route('design-job-request-floor-plan-image') }}?jr_product_id="+id, function(){});
        $('input[name="update-fp-jrproduct-img"]').val('');
        $('#updateFPforLabel').text('Choose file');
        $('#update-floor-plan-image-modal').modal('show');
    });

    $(document).on('click','.add-designer',function(){
        var id = $(this).data('id');
        $('input[name="taskJrProductID"]').val(id);
        $('select[name="assigned-designer"]').removeClass("is-invalid");
        $('select[name="assigned-designer"]').removeClass("is-valid");
        $('textarea[name="assigned-task"]').removeClass("is-invalid");
        $('textarea[name="assigned-task"]').removeClass("is-valid");
        $('input[name="estimated-finish-date"]').removeClass("is-invalid");
        $('input[name="estimated-finish-date"]').removeClass("is-valid");
        $('input[name="estimated-finish-time"]').removeClass("is-invalid");
        $('input[name="estimated-finish-time"]').removeClass("is-valid");
        $('#addDesignerBtn').prop('disabled', false);
       $('#add-designer-modal').modal('show');
    });

    $(document).on('click','#addDesignerBtn',function(){
        $(this).prop('disabled', true);
        var assigned_designer = $('select[name="assigned-designer"]').find(':selected').val();
        var assigned_task = $('textarea[name="assigned-task"]').val();
        var estimated_finish_date = $('input[name="estimated-finish-date"]').val();
        var estimated_finish_time = $('input[name="estimated-finish-time"]').val();
        var id = $('input[name="taskJrProductID"]').val();

        if($.trim(assigned_designer)){
            $('select[name="assigned-designer"]').removeClass("is-invalid");
            $('select[name="assigned-designer"]').addClass("is-valid");
            if($.trim(assigned_task)){
                $('textarea[name="assigned-task"]').removeClass("is-invalid");
                $('textarea[name="assigned-task"]').addClass("is-valid");
                if($.trim(estimated_finish_date)){
                    $('input[name="estimated-finish-date"]').removeClass("is-invalid");
                    $('input[name="estimated-finish-date"]').addClass("is-valid");
                    if($.trim(estimated_finish_time)){
                        $('input[name="estimated-finish-time"]').removeClass("is-invalid");
                        $('input[name="estimated-finish-time"]').addClass("is-valid");
                        $.post("{{ route('design-job-request-functions', ['id' => 'add-designer']) }}",
                        {id: id, designer: assigned_designer, task: assigned_task, estimated_date: estimated_finish_date, estimated_time: estimated_finish_time},
                        function(data){
                            console.log(data);
                            if(data['success'] == 1){
                                $(this).prop('disabled', false);
                                $('#add-designer-modal').modal('toggle');
                                $('select[name="assigned-designer"]').removeClass("is-valid");
                                $('textarea[name="assigned-task"]').removeClass("is-valid");
                                $('input[name="estimated-finish-date"]').removeClass("is-invalid");
                                $('input[name="estimated-finish-time"]').removeClass("is-invalid");
                                $('select[name="assigned-designer"]').val('').trigger('change');
                                $('textarea[name="assigned-task"]').val('');
                                $('input[name="estimated-finish-date"]').val('');
                                $('input[name="estimated-finish-time"]').val('');
                                alert_message("Success",data['message'],'success');
                                reload_dataTable(data['jrtype']);
                            }else{
                                alert_message("Failed",data['message'],'danger');
                                $(this).prop('disabled', false);
                            }
                        });
                    } else {
                        $('input[name="estimated-finish-time"]').addClass("is-invalid");
                        $(this).prop('disabled', false);
                    }
                } else {
                    $('input[name="estimated-finish-date"]').addClass("is-invalid");
                    $(this).prop('disabled', false);
                }
            }else{
                $('textarea[name="assigned-task"]').addClass("is-invalid");
                $(this).prop('disabled', false);
            }
        }else{
            $('select[name="assigned-designer"]').addClass("is-invalid");
            $(this).prop('disabled', false);
        }
    });

    $(document).on('click','.action-task',function(){
        $(this).prop('disabled', true);
        var id = $(this).data('id');
        var task_action = $(this).data('actiontype');
        Swal.fire({
            title: 'Confirm '+task_action,
            text: "Are you sure you want to "+task_action+" this task ?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes!, '+task_action+' This Task.'
        }).then((result) => {
            if (result.value) {
                $.post("{{ route('design-job-request-functions', ['id' => 'action-task']) }}",
                {id: id, action: task_action},
                function(data){
                    if(data['success']==1){
                        alert_message("Success",data['message'],'success');
                        $(this).prop('disabled', false);
                        reload_dataTable(data['jrtype']);
                    }else{
                        $(this).prop('disabled', false);
                        alert_message("Failed",data['message'],'danger');
                    }
                });
            }else{
                $(this).prop('disabled', false);
            }
        });
    });

    // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    function reload_dataTable(jrtype) {
        if(jrtype == 'FIT-OUT') {
            $("#dt-floor-plan").dataTable().fnDestroy();
            $("#dt-floor-plan").DataTable({
                "processing": true,
                "serverSide": true,
                "pageLength": 50,
                "width": "100%",
                "lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
                "ajax":{
                    url :"{{ route('design-job-request-functions',['id' => 'jr-floor-plan-serverside']) }}",
                    type: "POST",  
                    data: {id: '<?php echo $jr->id; ?>', product_type: jrtype},
                    "processing": true,
                    "serverSide": true,
                    error: function(data){  // error handling
                        $('#err').html(JSON.stringify(data));
                    }
                },
                columns: [
                    { data: 'status', name: 'status',visible:false},
                    { data: 'deadline_date', name: 'deadline_date',visible:false},
                    { data: 'designer_name', name: 'designer_name',visible:false},
                    { data: 'details', name: 'details',orderable: false, searchable: false},
                    { data: 'revision', name: 'revision',orderable: false, searchable: false},
                ]
            });
        } else {
            var tb_type = jrtype;
            $("#dt-"+tb_type).dataTable().fnDestroy();
            $('#dt-'+tb_type).DataTable({
                "processing": true,
                "serverSide": true,
                "pageLength": 50,
                "width": "100%",
                "lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
                "ajax":{
                    url :"{{ route('design-job-request-functions',['id' => 'jr-view-serverside']) }}",
                    type: "POST",  
                    data: {id: '<?php echo $jr->id; ?>', product_type:jrtype},
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
    }

    $('#fp-jrproduct-img').change(function(){
        readURL(this);
    });

    function readURL(input) {
        if(input.files && input.files[0] && input.files[0].name.match(/\.(jpg|jpeg|JPG|JPEG|png|PNG)$/)) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#fp-img-preview').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        } else{
            $('input[name="fp-jrproduct-img"]').val('');
            $("#fp-img-preview").attr("src","http://placehold.it/454x400");
            Swal.fire({
                type: "warning",
                title: "Please make sure uploaded image is JPG or PNG.",
                width: 500,
                padding: "3em",
            });
        }
    }

    $('#update-fp-jrproduct-img').change(function(){
        updateReadURL(this);
    });

    function updateReadURL(input) {
        if(input.files && input.files[0] && input.files[0].name.match(/\.(jpg|jpeg|JPG|JPEG|png|PNG)$/)) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#update-fp-img-preview').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        } else{
            $('input[name="update-fp-jrproduct-img"]').val('');
            $("#update-fp-img-preview").attr("src","http://placehold.it/454x400");
            Swal.fire({
                type: "warning",
                title: "Please make sure uploaded image is JPG or PNG.",
                width: 500,
                padding: "3em",
            });
        }
    }

    $(document).on('click', '.delete-floor-plan', function() {
        $(this).prop('disabled', true);
        var id = $(this).data('id');
        var task_action = $(this).data('actiontype');
        Swal.fire({
            title: 'Confirm '+task_action,
            text: "Are you sure you want to "+task_action+" this floor plan ?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes!, '+task_action+' This Floor Plan.'
        }).then((result) => {
            if (result.value) {
                $.post("{{ route('design-job-request-functions', ['id' => 'delete-floor-plan']) }}",
                {id: id, action: task_action},
                function(data){
                    if(data['success']==1){
                        alert_message("Success",data['message'],'success');
                        $(this).prop('disabled', false);
                        reload_dataTable(data['jrtype']);
                    }else{
                        $(this).prop('disabled', false);
                        alert_message("Failed",data['message'],'danger');
                    }
                });
            }else{
                $(this).prop('disabled', false);
            }
        });
    });

    $(document).on('click','.reject-revision-withwork',function(){
        var id = $(this).data('id');
        $('input[name="rejectJRProductId"]').val(id);
        $('#reason-reject-plan').modal('show');
    });
    
    $(document).on('click','#rejectReasonSubmitBtn',function(){
        var id = $('input[name="rejectJRProductId"]').val();
        var reason_reject = $('textarea[name="reject-reason"]').val();
        $(this).prop('disabled', true);
        if($.trim(reason_reject)){
            $('textarea[name="reject-reason"]').removeClass('is-invalid');
            $('textarea[name="reject-reason"]').addClass('is-valid');
            $.post("{{ route('design-job-request-functions', ['id' => 'reject-revision']) }}",
            {id: id, reason_reject:reason_reject},
            function(data){
                console.log(data);
                $(this).prop('disabled', false);
                $('#reason-reject-plan').modal('toggle');
                if(data['success']==1){
                    alert_message("Success",data['message'],'success');
                    reload_dataTable(data['jrtype']);
                }else{
                    alert_message("Failed!",'Reject Has Been Failed.','danger');
                }
            });
        }else{
            $('textarea[name="reject-reason"]').addClass('is-invalid');
        }
    });
    //end gelo added
});
</script>
@endsection