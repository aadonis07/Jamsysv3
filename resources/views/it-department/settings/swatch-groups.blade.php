@extends ('layouts.it-department.app')
@section ('title')
    {{ $sub_category->name }} | Swatch Groups
@endsection
@section ('styles')
<link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.16.0/extensions/reorder-rows/bootstrap-table-reorder-rows.min.js">
@endsection
@section('breadcrumbs')
<li class="breadcrumb-item">Settings</li>
<li class="breadcrumb-item"><a href="{{ route('settings-categories') }}">Categories</a></li>
<li class="breadcrumb-item ">
    <a href="{{ route('settings-sub-categories',['cid'=>encryptor('encrypt',$sub_category->category_id)]) }}">{{ strToTitle($sub_category->category->name) }}</a>
</li>
<li class="breadcrumb-item ">{{ strToTitle($sub_category->name) }}</li>
<li class="breadcrumb-item active">Swatch Groups</li>
@endsection

@section('content')
<div class="row mb-3">
    <div class="col-lg-12 d-flex flex-start w-100 mb-2">
        <div class="mr-2 hidden-md-down"> <span class="icon-stack icon-stack-lg">
            <i class="base base-6 icon-stack-3x opacity-100 color-primary-500"></i>
            <i class="base base-10 icon-stack-2x opacity-100 color-primary-300 fa-flip-vertical"></i>
            <i class="ni ni-settings icon-stack-1x opacity-100 color-white" style="font-size: 14px; margin-bottom: 2px;"></i>
        </span>
        </div>
        <div class="row d-flex flex-fill">
            <div class="col-lg-7 flex-fill"> <span class="h5 mt-0">CATEGORIES SETTINGS</span>
                <br>
                <p class="mb-0">In creating categories, Ensure the added categories will be <b>use</b></p>
            </div>
            <div class="col-lg-5 form-group">
                <div class="input-group bg-white shadow-inset-2">
                    <input type="search" id="sub-category-search" class="form-control border-right-0 bg-transparent pr-0" placeholder="Search...">
                    <div class="input-group-append">
                    <span class="input-group-text bg-transparent border-left-0">
                        <i class="fal fa-search"></i>
                    </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#swatches-modal">CREATE</button>
                    &nbsp; {{ strtoupper($sub_category->name) }} | SWATCH GROUPS

                </h2>
                <div class="panel-toolbar">
                    <button class="btn btn-panel waves-effect waves-themed" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                    <button class="btn btn-panel waves-effect waves-themed" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                    <button class="btn btn-panel waves-effect waves-themed" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
                </div>
            </div>
            <div class="panel-container show">
                <div class="panel-content">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-sm-12">
                                <div id="" class="dataTables_wrapper dt-bootstrap4">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <table id="dt-sub-categories" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-departments_info" style="width: 1222px;">
                                                <thead class="bg-warning-500 text-center">
                                                    <tr role="row">
                                                        <th width="5%" class="sorting_asc" tabindex="0" aria-controls="dt-departments" rowspan="1" colspan="1" data-column-index="0"></th>
                                                        <th width="90%" class="sorting" tabindex="0" aria-controls="dt-departments" rowspan="1" colspan="1" data-column-index="0">Name</th>
                                                        <th width="5%" class="sorting" tabindex="0" aria-controls="dt-departments" rowspan="1" colspan="1" data-column-index="4">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @php
                                                    $count = 0;
                                                    $updatePath = route('settings-sub-category-swatch-details');
                                                @endphp
                                                @foreach($group_swatches as $group_swatch)
                                                    @php
                                                        $group_swatch_id = encryptor('encrypt',$group_swatch->id);
                                                        $count++;

                                                    @endphp
                                                    <tr>
                                                        <td> {{ $count }} </td>
                                                        <td> {{ $group_swatch->name }} </td>
                                                        <td>
                                                            <button class="btn btn-primary btn-xs" onClick="updateGswatch('{{ $updatePath }}','{{ $group_swatch_id }}')">Edit</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="update-swatch-group-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Update swatch group modal</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="update-swatch-group-form" enctype="multipart/form-data" role="form" onsubmit="updateGrpBtn.disabled = true; return true;" method="POST"  action="{{route('settings-functions',['id' => 'update-swatch-group'])}}">
                    @csrf()
                    <div class="row" id="update-content-details">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" id="updateGrpBtn" form="update-swatch-group-form" class="btn btn-primary" >Update </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="swatches-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Swatch Group</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <form id="create-swatch-group-form" role="form" enctype="multipart/form-data" onsubmit="$('#save-group-btn').prop('disabled',true); return true;" method="POST"  action="{{route('settings-functions',['id' => 'create-swatch-group'])}}">
                    @csrf()
                    <div class="row">
                        <input type="hidden" name="subcategory" value="{{ encryptor('encrypt',$sub_category->id) }}" readonly/>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-plaintext">Group Name</label>
                                <input class="form-control" placeholder="Enter Group name" required  name="group_name" type="text" maxlength="50" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-plaintext">Swatch Category</label>
                                <select class="form-control" name="category" required id="swatch-category" onChange="genSwatch(this.value)">
                                    <option value="">Choose Category</option>
                                    @foreach(swatchesCategory() as $index=>$swatchCat)
                                        <option value="{{ $index }}">{{ strtoupper($swatchCat) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" id="swatch-details">
                            <hr>
                            <text class="text-danger" >Please Select Swatches</text>
                            <div class="row" id="empty-swatch">

                            </div>
                        </div>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" form="create-swatch-group-form" id="save-group-btn" disabled class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{  asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/TableDnD/1.0.3/jquery.tablednd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.16.0/extensions/reorder-rows/bootstrap-table-reorder-rows.min.js"></script>
    <script>
        function isEnable(key,object){
            if($('#'+object).is(":checked")){
                $('#'+key+"-order").prop('disabled',false);
            }else{
                $('#'+key+"-order").prop('disabled',true);
            }
        }
        function updateGswatch(e,key){
            $('#update-content-details').html('');
            $('#update-content-details').html('' +
                '<div class="col-md-12 mt-4">'+
                '    <div class="d-flex justify-content-center">'+
                '        <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">'+
                '            <span class="sr-only">Loading...</span>'+
                '        </div>'+
                '    </div>'+
                '</div>'+
                '');
            $("#update-content-details").load(e+"?scwgid="+key);
            $('#update-swatch-group-modal').modal('show');
        }
        function genSwatch(value,select = 'swatch-category',divcontent = 'empty-swatch'){
            $('#'+select).prop('disabled',true);
            $('#save-group-btn').prop('disabled',true);
            $('#'+divcontent).html('');
            $('#'+divcontent).html('' +
                    '<div class="col-md-12 mt-4">'+
                    '    <div class="d-flex justify-content-center">'+
                    '        <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">'+
                    '            <span class="sr-only">Loading...</span>'+
                    '        </div>'+
                    '    </div>'+
                    '</div>'+
            '');
            formData = new FormData();
            formData.append('category',value);
            $.ajax({
                type: "POST",
                url: "{{route('settings-functions',['id' => 'create-swatch-group'])}}",
                data: formData,
                contentType: false,
                processData: false,
                success: function (result) {
                    if(result.success != '1'){
                        alert_message('Failed',result.message,'error');
                        $('#'+select).prop('disabled',false);
                        $('#'+divcontent).html('');
                        $('#'+select).prop('disabled',false);
                        $('#swatches-modal').modal('hide');
                    }
                    else{
                        $('#'+divcontent).html(result.data);
                        $('#'+select).prop('disabled',false);
                        $('#save-group-btn').prop('disabled',false);
                    }
                },
                error: function(result){
                    alert_message('Failed',result.responseText,'error');
                    $('#'+select).prop('disabled',false);
                    $('#'+divcontent).html('');
                    $('#swatches-modal').modal('hide');
                }
            });
        }
    </script>
@endsection
