@extends ('layouts.sales-department.app')
@section ('title')
    Job Request View
@endsection
@section ('styles')
<link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
<link href="{{ asset('assets/css/formplugins/select2/select2.bundle.css') }}" rel="stylesheet" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
<link href="//cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
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
            <input class="form-control" type="hidden" name="jrProductID" required/>
        
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
        <form method="post" id="add-floor-plan-form" enctype="multipart/form-data">
        @csrf()
            <div class="form-group">
                <label>Type</label>
                <select class="custom-select" name="fp-type" required>
                    <option value=""></option>
                    @foreach($jr_types as $jr_type)
                        <option value="{{$jr_type->id}}">{{$jr_type->name}}</option>
                    @endforeach
                </select>
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
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success" id="addFloorPlanBtn" >Submit</button>
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
            <form id="update-image-form" method="POST" onsubmit="submitChangeImageBtn.disabled = true;" action="{{ route('sales-job-request-functions',['id' => 'update-product-image']) }}" enctype="multipart/form-data">
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
            <form id="add-floor-plan-image-form" method="POST" onsubmit="submitAddFPImageBtn.disabled = true;" action="{{ route('sales-job-request-functions',['id' => 'add-floor-plan-image']) }}" enctype="multipart/form-data">
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
            <form id="update-floor-plan-image-form" method="POST" onsubmit="submitUpdateFPImageBtn.disabled = true;" action="{{ route('sales-job-request-functions',['id' => 'update-floor-plan-image']) }}" enctype="multipart/form-data">
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
<div id="update-product-description-modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">
            Update Product Description
            <small class="m-0 text-muted">
                Please input correct description and update amount.
            </small>
        </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"><i class="fal fa-times"></i></span>
        </button>
    </div>
      <div class="modal-body">
            <form id="update-product-description-form" method="POST" onsubmit="updateProductDescriptionBtn.disabled = true;" action="{{ route('sales-job-request-functions',['id' => 'update-product-description']) }}" enctype="multipart/form-data">
                @csrf()
                <div id="product-description-content">
                </div>
            </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success" form="update-product-description-form" id="updateProductDescriptionBtn">Update</button>
      </div>
    </div>
  </div>
</div>
<!-- ================================================================================ -->
<div id="update-revision-remarks-modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">
            Update Revision Remarks
            <small class="m-0 text-muted">
                Please input remarks and accurate deadline.
            </small>
        </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"><i class="fal fa-times"></i></span>
        </button>
    </div>
    <div class="modal-body">
        <div id="update-revision-remarks-content"></div>
    </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success" id="updateRevisionRemarksBtn">Update</button>
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
<script src="//cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script>
$(function(){
    $('#dt-NEW-DESIGN').DataTable({
        "processing": true,
        "serverSide": true,
        "pageLength": 50,
        "width": "100%",
        "lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
        "ajax":{
            url :"{{ route('sales-job-request-functions',['id' => 'jr-view-serverside']) }}",
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
            url :"{{ route('sales-job-request-functions',['id' => 'jr-view-serverside']) }}",
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
            url :"{{ route('sales-job-request-functions',['id' => 'jr-floor-plan-serverside']) }}",
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
    $('select[name="assigned-designer"]').select2({
        placeholder: "Select Designer",
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
    $('input[name="estimated-finish-date"]')
        .datepicker({
        format: 'yyyy-mm-dd',
        startDate: '+0d',
        autoclose: true
    });
    $(document).on('click','.add-revision',function(){
        var id = $(this).data('id');
        $('input[name="jrProductID"]').val(id);
        $('#addRevisionBtn').prop('disabled', false);
       $('#add-revision').modal('show');
    });
    $('input[name="deadline-date"]').keyup(function() {
        $(this).attr('val', '');
    });
    $(document).on('click','#addRevisionBtn',function(){
        $(this).prop('disabled', true);
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
                    $.post("{{ route('sales-job-request-functions', ['id' => 'add-revision']) }}",
                    {id: id,jr_type:jr_type,deadline:deadline,remarks:remarks},
                    function(data){
                        console.log(data);
                        $(this).prop('disabled', false);
                        if(data['success'] == 1){
                            $('#add-revision').modal('toggle');
                            $('select[name="jr-type"]').removeClass("is-valid");
                            $('input[name="deadline-date"]').removeClass("is-valid");
                            $('textarea[name="revision-remarks"]').removeClass("is-valid");
                            $('input[name="deadline-date"]').val("");
                            $('textarea[name="revision-remarks"]').val("");
                            $('select[name="jr-type"]').val('').trigger('change');
                            alert_message("Success",data['message'],'success');
                            reload_dataTable(data);
                        }else{
                            alert_message("Failed",data['message'],'danger');
                        }
                    });
                }else{
                    $(this).prop('disabled', false);
                    $('textarea[name="revision-remarks"]').addClass("is-invalid");
                }
            }else{
                $(this).prop('disabled', false);
                $('input[name="deadline-date"]').addClass("is-invalid");
            }
        }else{
            $(this).prop('disabled', false);
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
                $.post("{{ route('sales-job-request-functions', ['id' => 'cancel-revision']) }}",
                {id: id,deltype:deltype},
                function(data){
                    if(data['success']==1){
                        $('#'+id).remove();
                        alert_message("Success",data['message'],'success');
                        $(this).prop('disabled', false);
                        reload_dataTable(data);
                    }else{
                        $(this).prop('disabled', false);
                    }
                });
            }else{
                $(this).prop('disabled', false);
            }
        });
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
        $("#image-content").load("{{ route('sales-job-request-product-image') }}?product_id="+id, function(){

        });
        $('#fix-image-modal').modal('show');
    });

    //start gelo added
    $(document).on('click','#addFloorPlanBtn',function(){
        var jr_type = $('select[name="fp-type"]').find(':selected').val();
        var deadline = $('input[name="fp-deadline-date"]').val();
        var remarks = $('textarea[name="fp-revision-remarks"]').val();
        var id = $('input[name="jrId"]').val();
        var type = $('input[name="jrType"]').val();

        if($.trim(jr_type)){
            $('select[name="fp-type"]').removeClass("is-invalid");
            $('select[name="fp-type"]').addClass("is-valid");
            if($.trim(deadline)){
                $('input[name="fp-deadline-date"]').removeClass("is-invalid");
                $('input[name="fp-deadline-date"]').addClass("is-valid");
                if($.trim(remarks)){
                    $('textarea[name="fp-revision-remarks"]').removeClass("is-invalid");
                    $('textarea[name="fp-revision-remarks"]').addClass("is-valid");
                    $.post("{{ route('sales-job-request-functions', ['id' => 'add-floor-plan']) }}",
                    {id: id, jr_type: jr_type, deadline: deadline, remarks: remarks, type: type},
                    function(data){
                        console.log(data);
                        if(data['success'] == 1){
                            $('#add-floor-plan').modal('toggle');
                            $('select[name="fp-type"]').removeClass("is-valid");
                            $('input[name="fp-deadline-date"]').removeClass("is-valid");
                            $('textarea[name="fp-revision-remarks"]').removeClass("is-valid");
                            $('input[name="fp-deadline-date"]').val("");
                            $('textarea[name="fp-revision-remarks"]').val("");
                            $('select[name="fp-type"]').val('').trigger('change');
                            alert_message("Success",data['message'],'success');
                            reload_dataTable(data);
                        }else{
                            alert_message("Failed",data['message'],'danger');
                        }
                    });
                }else{
                    $('textarea[name="fp-revision-remarks"]').addClass("is-invalid");
                }
            }else{
                $('input[name="fp-deadline-date"]').addClass("is-invalid");
            }
        }else{
            $('select[name="fp-type"]').addClass("is-invalid");
        }
    });

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
        $("#update-fp-image-content").load("{{ route('sales-job-request-floor-plan-image') }}?jr_product_id="+id, function(){});
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
                        $.post("{{ route('sales-job-request-functions', ['id' => 'add-designer']) }}",
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
                                reload_dataTable(data);
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
                $.post("{{ route('sales-job-request-functions', ['id' => 'action-task']) }}",
                {id: id, action: task_action},
                function(data){
                    if(data['success']==1){
                        alert_message("Success",data['message'],'success');
                        $(this).prop('disabled', false);
                        reload_dataTable(data);
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
                $.post("{{ route('sales-job-request-functions', ['id' => 'delete-floor-plan']) }}",
                {id: id, action: task_action},
                function(data){
                    if(data['success']==1){
                        alert_message("Success",data['message'],'success');
                        $(this).prop('disabled', false);
                        reload_dataTable(data);
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

    $(document).on('click', '.update-product-description', function() {
        var id = $(this).data('id');
        $('#product-description-content').html('');
        $('#product-description-content').html('' +
            '<div class="col-md-12 mt-4">'+
            '    <div class="d-flex justify-content-center">'+
            '        <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">'+
            '            <span class="sr-only">Loading...</span>'+
            '        </div>'+
            '    </div>'+
            '</div>'+
            '');
        $('#forLabel').text('Choose file');
        $("#product-description-content").load("{{ route('sales-job-request-product-desc-content') }}?jr_product_id="+id, function(){   
            $("textarea[name='product-description']").summernote();
            $(document).on('keyup', 'input.numeric_filter', function() {
                var val = $(this).val();
                if(isNaN(val)) {
                    val = val.replace(/[^0-9\.]/g,'');
                    if(val.split('.').length>2) { val =val.replace(/\.+$/,""); }
                }
                if(val=="") { value=""; }
                else { value = val; }
                $(this).val(value);
            });
        });
        $('#update-product-description-modal').modal('show');
    });


    $(document).on('change', '#product_price, #product_discount', function() {
        if($(this).val() == "") {
            $(this).val(0);
        }
        var id = $(this).data('id');
        var quotation_id = $(this).data('qid');
        var product_qty = $("input[name='product_qty']").val();
        var product_price = $("input[name='product_price']").val();
        var product_discount = $("input[name='product_discount']").val();
        var discount_quotation = $("input[name='discount_quotation']").val();
        var product_total_price = $("input[name='hidden_product_total_price']").val();
        var product_total_amount = $("input[name='hidden_product_total_amount']").val();
        var new_product_total_price = 0;
        var new_product_total_amount = 0;
        var new_product_total_price = parseFloat(product_qty) * parseFloat(product_price);
        var new_product_total_amount = parseFloat(new_product_total_price) - parseFloat(product_discount);
        $('input[name="product_total"]').val(new_product_total_amount);
        $("input[name='hidden_product_total_price']").val(new_product_total_price);
        $("input[name='hidden_product_total_amount']").val(new_product_total_amount);

        $.post("{{ route('sales-job-request-functions', ['id' => 'fetch-quotation-details']) }}",
            {id: id, quotation_id: quotation_id, new_total_price: new_product_total_price, product_discount: product_discount, discount_quotation: discount_quotation},
        function(data){
            console.log(data);
            $('input[name="sub_total"]').val(data.sub_total);
            $('input[name="installation_charge"]').val(data.installation_charge);
            $('input[name="delivery_charge"]').val(data.delivery_charge);
            $('input[name="discount_product_quotation"]').val(data.total_product_discount);
            $('input[name="discount_quotation"]').val(data.discount);
            $('input[name="total_discount"]').val(data.total_discount);
            $('input[name="grand_total"]').val(data.grand_total);
            $('input[name="grand_total_temp"]').val(data.temp_grand_total);
        });
    });

    $(document).on('click', '.update-revision-remarks', function() {
        var id = $(this).data('id');
        $('#update-revision-remarks-content').html('');
        $('#update-revision-remarks-content').html('' +
            '<div class="col-md-12 mt-4">'+
            '    <div class="d-flex justify-content-center">'+
            '        <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">'+
            '            <span class="sr-only">Loading...</span>'+
            '        </div>'+
            '    </div>'+
            '</div>'+
            '');
        $("#update-revision-remarks-content").load("{{ route('sales-job-request-revision-content') }}?jr_product_id="+id, function(){
            $('input[name="update-deadline-date"]')
                .datepicker({
                format: 'yyyy-mm-dd',
                startDate: '+0d',
            });
        });
        $('#update-revision-remarks-modal').modal('show');
    });

    $(document).on('click','#updateRevisionRemarksBtn',function(){
        $(this).prop('disabled', true);
        var deadline = $('input[name="update-deadline-date"]').val();
        var remarks = $('textarea[name="update-revision-remarks"]').val();
        var id = $('input[name="jr-product-id"]').val();

        if($.trim(deadline)){
            $('input[name="update-deadline-date"]').removeClass("is-invalid");
            $('input[name="update-deadline-date"]').addClass("is-valid");
            if($.trim(remarks)){
                $('textarea[name="update-revision-remarks"]').removeClass("is-invalid");
                $('textarea[name="update-revision-remarks"]').addClass("is-valid");
                $.post("{{ route('sales-job-request-functions', ['id' => 'update-revision-remarks']) }}",
                {id: id, deadline:deadline, remarks:remarks},
                function(data){
                    console.log(data);
                    $(this).prop('disabled', false);
                    if(data['success'] == 1){
                        $('#update-revision-remarks-modal').modal('toggle');
                        $('input[name="update-deadline-date"]').removeClass("is-valid");
                        $('input[name="update-deadline-date"]').val("");
                        $('textarea[name="update-revision-remarks"]').removeClass("is-valid");
                        $('textarea[name="update-revision-remarks"]').val("");
                        alert_message("Success",data['message'],'success');
                        reload_dataTable(data);
                    }else{
                        alert_message("Failed",data['message'],'danger');

                    }
                });
            }else{
                $('textarea[name="update-revision-remarks"]').addClass("is-invalid");
                $(this).prop('disabled', false);
            }
        }else{
            $('input[name="update-deadline-date"]').addClass("is-invalid");
            $(this).prop('disabled', false);
        }
    });

    $(document).on('click', '.done-update-revision', function() {
        $(this).prop('disabled', true);
        var id = $(this).data('id');
        Swal.fire({
            title: 'Confirm Done',
            text: "Are you sure you are done updating the revision ?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if(result.value) {
                $.post("{{ route('sales-job-request-functions', ['id' => 'done-update-revision']) }}",
                {id: id},
                function(data){
                    console.log(data);
                    $(this).prop('disabled', false);
                    if(data['success']==1){
                        alert_message("Success",data['message'],'success');
                        reload_dataTable(data);
                    }else{
                        alert_message("Failed",data['message'],'danger');
                    }
                });
            }else{
                $(this).prop('disabled', false);
            }
        });
    });



    // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    function reload_dataTable(data) {
        if(data['jrtype'] == 'FIT-OUT') {
            if(data['countFP'] != 0) {
                $("#dt-floor-plan").dataTable().fnDestroy();
                $("#dt-floor-plan").DataTable({
                    "processing": true,
                    "serverSide": true,
                    "pageLength": 50,
                    "width": "100%",
                    "lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
                    "ajax":{
                        url :"{{ route('sales-job-request-functions',['id' => 'jr-floor-plan-serverside']) }}",
                        type: "POST",  
                        data: {id: '<?php echo $jr->id; ?>', product_type: data['jrtype']},
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
                location.reload();
            }
        } else {
            var tb_type = data['jrtype'];
            $("#dt-"+tb_type).dataTable().fnDestroy();
            $('#dt-'+tb_type).DataTable({
                "processing": true,
                "serverSide": true,
                "pageLength": 50,
                "width": "100%",
                "lengthMenu": [[50, 100, 150, 250], [50, 100, 150, 250]],
                "ajax":{
                    url :"{{ route('sales-job-request-functions',['id' => 'jr-view-serverside']) }}",
                    type: "POST",  
                    data: {id: '<?php echo $jr->id; ?>', product_type:data['jrtype']},
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
    //end gelo added
});
</script>
@endsection