@extends ('layouts.it-department.app')
@section ('title')
    {{ $category->name }} | Sub Categories
@endsection
@section ('styles')
<link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
@endsection
@section('breadcrumbs')
<li class="breadcrumb-item">Settings</li>
<li class="breadcrumb-item"><a href="{{ route('settings-categories') }}">Categories</a></li>
<li class="breadcrumb-item ">{{ strToTitle($category->name) }}</li>
<li class="breadcrumb-item active">Sub Categories</li>
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
                        {{ strtoupper($category->name) }} | SUB CATEGORIES
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
							<form class="" role="form" id="create-sib-categories" method="POST" action="{{ route('settings-functions',['id' => 'create-sub-categories']) }}">
								@csrf()
								<div class="row">
									<div class="col-lg-12">
    									<div class="input-group alert alert-primary mb-4  input-group-multi-transition"> <span class="input-group-text"><i class="ni ni-my-apps"></i></span>
                                            <input type="hidden" name="category_key" value="{{ encryptor('encrypt',$category->id) }}"/>
											<input required="" type="text" maxlength="50" class="form-control" name="sub_category" placeholder="Sub Category">
											<div class="input-group-append">
												<button type="submit" onClick="$('#'+this.id,'disable',true);" id="add-sub-categories-btn" class="btn btn-dark waves-themed waves-effect waves-themed">ADD SUB CATEGORY <i class="fas fa-arrow-right"></i>
												</button>
											</div>
										</div>
										<hr>
									</div>
								</div>
							</form>
						</div>
						<div class="col-sm-12">
							<div id="" class="dataTables_wrapper dt-bootstrap4">
								<div class="row">
									<div class="col-sm-12">
							    		<table id="dt-sub-categories" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-departments_info" style="width: 1222px;">
											<thead class="bg-warning-500 text-center">
												<tr role="row">
													<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-departments" rowspan="1" colspan="1" data-column-index="0"></th>
													<th width="40%" class="sorting" tabindex="0" aria-controls="dt-departments" rowspan="1" colspan="1" data-column-index="0">Sub Category Name</th>
													<th width="24%" class="sorting" tabindex="0" aria-controls="dt-departments" rowspan="1" colspan="1" data-column-index="2">Status</th>
													<th width="30%" class="sorting" tabindex="0" aria-controls="dt-departments" rowspan="1" colspan="1" data-column-index="4">Action</th>
												</tr>
											</thead>
											<tbody>
                                            @foreach($sub_categories as $index=>$sub_category)
                                                @php
                                                    $enc_sub_category = encryptor('encrypt',$sub_category->id);
                                                @endphp
                                                <tr role="row" class="odd">
                                                    <td style="vertical-align: middle" tabindex="0" class="text-center sorting_1">{{ ( $index + 1 ) }}</td>
                                                    <td style="vertical-align: middle" tabindex="0" class="sorting_1 text-center">{{  $sub_category->name }}</td>
                                                    <td style="vertical-align: middle" class="text-center">
                                                        <div class="custom-control custom-switch">
                                                            @php
                                                                $isActive = 'checked';
                                                                if($sub_category->status == 'INACTIVE'){
                                                                    $isActive = '';
                                                                }
                                                            @endphp
                                                            <input type="checkbox"  onChange="changeStat('{{ $enc_sub_category }}',this)" class="custom-control-input" id="{{ $enc_sub_category }}" {{ $isActive }}>
                                                            <label class="custom-control-label " for="{{ $enc_sub_category }}">&nbsp;</label>
                                                        </div>
                                                    </td>
                                                    <td style="vertical-align: middle" class="pb-0">
                                                        <div class="demo text-center mb-0">
                                                            <input type="hidden" id="{{ $enc_sub_category }}-name" value="{{ $sub_category->name  }}">
                                                            <a href="javascript:;" onClick='updateSubCategory("{{ $enc_sub_category }}")' class="pb-0 btn btn-info btn-icon waves-effect waves-themed" data-toggle="tooltip" data-placement="top" title="" data-original-title="UPDATE">
                                                                <i class="ni ni-note"></i>
                                                            </a>
                                                            <a href="{{ route('settings-sub-category-swatches',['scid'=>$enc_sub_category]) }};"  class="pb-0 btn btn-success btn-icon waves-effect waves-themed" data-toggle="tooltip" data-placement="top" title="Swatches" data-original-title="SWATCHES">
                                                                <i class="fas fa-swatchbook"></i>
                                                            </a>
                                                            <a href="javascript:;" onclick="logsModal('{{ $enc_sub_category }}')" class="pb-0 btn btn-default btn-icon waves-effect waves-themed" data-toggle="tooltip" data-placement="top" title="" data-original-title="HISTORY LOGS">
                                                                <i class="ni ni-calendar"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
											</tbody>
											<tfoot class="thead-themed">
												<tr class="text-center">
													<th rowspan="1" colspan="1"></th>
													<th rowspan="1" colspan="1">Position</th>
													<th rowspan="1" colspan="1">Status</th>
													<th rowspan="1" colspan="1">Action</th>
												</tr>
											</tfoot>
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
<div class="modal fade" id="sub-category-logs-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header mb-0">
                <h5 class="modal-title"><b> <i class="ni ni-calendar"></i> History Logs </b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div id="feedback"></div>
                    <div class="col-md-12 text-center"  id="logs-content">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="update-sub-category-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header pb-2">
                <h4 class="modal-title">
                    UPDATE SUB CATEGORY
                    <small class="m-0 text-muted">
                        Special character is not allowed Except spaces.
                    </small>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body pt-2">
                <form class="" role="form" id="create-categories" method="POST" action="{{ route('settings-functions',['id' => 'update-sub-categories']) }}">
                    @csrf()
                    <div class="row">
                        <div class="col-md-12">
                            <div class="input-group">
                                <input type="hidden" name="sub_category" id="update-sub-category-key"/>
                                <input type="text" maxlength="50" name="sub_category_name" id="update-sub-category-name" class="form-control form-control-md" placeholder="">
                                <button type="submit" onClick="$(this).attr('disable',true)" id="update-sub-category-btn" class="pb-0 btn btn-default btn-icon waves-effect waves-themed" data-toggle="tooltip" data-placement="top" title="" data-original-title="UPDATE">
                                    <i class="ni ni-note pt-2s pt-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{  asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
    <script>
        var selected_category = 0;
        var data = [];
        $(function(){
            var sub_category_tbl = $('#dt-sub-categories').DataTable({
                responsive: true,
                paging: false,
                sDom: 'lrtip'
            });
            $( "#sub-category-search" ).keyup(function() {
                $("#dt-sub-categories_filter  input[type='search']").val(this.value);
                sub_category_tbl.search(
                    $(this).val(),
                ).draw() ;
            });
        });
        function updateSubCategory(key){
             $('#update-sub-category-key').val(key);
             $('#update-sub-category-name').val($('#'+key+"-name").val());
             $('#update-sub-category-modal').modal('show');
        }
        function logsModal(key){
            var url = "{{ route('settings-sub-category-logs-details') }}";
            $('#logs-content').html('');
            $('#logs-content').html('' +
                '<div class="loading mt-6 mb-6">'+
                '<div class="spinner-grow text-secondary" style="width: 4rem; height: 4rem;" role="status">'+
                '<span class="sr-only">Loading...</span>'+
                '</div>'+
                '<br>'+
                '<br>'+
                '</div>'+
                '');
            $("#logs-content").load(url+"?scid="+key, function () {
                var data = { 'key': key }
                $("#dt-sub-category-logs").dataTable().fnDestroy();
                $('#dt-sub-category-logs').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax":{
                        url :"{{ route('settings-functions',['id' => 'logs-sub-category-details']) }}", // json datasource
                        type: "POST",  // method  , by default get
                        data : data,
                        error: function(result){  // error handling
                            $('#err').html(JSON.stringify(result));
                            $('#feedback').html(JSON.stringify(result));
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex',orderable: false, searchable: false },
                        { data: 'auditable_type', name: 'auditable_type', visible: false},
                        { data: 'user.username', name: 'user.username', visible: false},
                        { data: 'source_model', name: 'source_model',orderable: false, searchable: false},
                        { data: 'event', name: 'event'},
                        { data: 'old_values', name: 'old_values'},
                        { data: 'new_values', name: 'new_values'},
                    ]
                });
            });
            $('#sub-category-logs-modal').modal('show');
        }
        function changeStat(key,objectKey){
            var value = objectKey.checked ? 'ACTIVE' : 'INACTIVE';
            var formData = new FormData();
            formData.append('stat',value);
            formData.append('scid',key);
            $.ajax({
                type: "POST",
                url: "{{route('settings-functions',['id' => 'update-sub-category-status'])}}",
                data: formData,
                contentType: false,
                processData: false,
                success: function (result) {
                    if(result.success != '1'){
                        alert_message(result.message,'danger');
                    }
                },
                error: function(result){
                    alert_message(result.responseText,'danger');
                }
            });
        }
    </script>
@endsection
