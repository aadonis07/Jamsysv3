@extends ('layouts.it-department.app')
@section ('title')
    Categories
@endsection
@section ('styles')
<link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
@endsection
@section('breadcrumbs')
<li class="breadcrumb-item">Settings</li>
<li class="breadcrumb-item active">Categories</li>
@endsection

@section('content')
<div class="row mb-3">
	<div class="col-lg-12 d-flex flex-start w-100">
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
                    <input type="search" id="category-search" class="form-control border-right-0 bg-transparent pr-0" placeholder="Search...">
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
                    CATEGORY TABLE
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
							<form class="" role="form" id="create-categories" method="POST" action="{{ route('settings-functions',['id' => 'create-categories']) }}">
								@csrf()
								<div class="row">
									<div class="col-lg-12">
    									<div class="input-group alert alert-primary mb-4  input-group-multi-transition"> <span class="input-group-text"><i class="ni ni-my-apps"></i></span>
											<input required="" type="text" maxlength="50" class="form-control" name="category" placeholder="Category">
											<div class="input-group-append">
												<button type="submit" onClick="$('#'+this.id,'disable',true);" form="create-categories" id="add-categories-btn" class="btn btn-dark waves-themed waves-effect waves-themed">ADD CATEGORY <i class="fas fa-arrow-right"></i>
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
							    		<table id="dt-categories" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-departments_info" style="width: 1222px;">
											<thead class="bg-warning-500 text-center">
												<tr role="row">
													<th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-departments" rowspan="1" colspan="1" data-column-index="0"></th>
													<th width="40%" class="sorting" tabindex="0" aria-controls="dt-departments" rowspan="1" colspan="1" data-column-index="0">Category Name</th>
													<th width="24%" class="sorting" tabindex="0" aria-controls="dt-departments" rowspan="1" colspan="1" data-column-index="2">Status</th>
													<th width="30%" class="sorting" tabindex="0" aria-controls="dt-departments" rowspan="1" colspan="1" data-column-index="4">Action</th>
												</tr>
											</thead>
											<tbody>
                                                @foreach($categories as $index=>$category)
                                                    @php
                                                        $enc_category = encryptor('encrypt',$category->id);
                                                    @endphp
                                                    <tr role="row" class="odd">
                                                        <td style="vertical-align: middle" tabindex="0" class="text-center sorting_1">{{ ( $index + 1 ) }}</td>
                                                        <td style="vertical-align: middle" tabindex="0" class="sorting_1 text-center">{{  $category->name }}</td>
                                                        <td style="vertical-align: middle" class="text-center">
                                                            <div class="custom-control custom-switch">
                                                                @php
                                                                    $isActive = 'checked';
                                                                    if($category->status == 'INACTIVE'){
                                                                        $isActive = '';
                                                                    }
                                                                @endphp
                                                                <input type="checkbox"  onChange="changeStat('{{ $enc_category }}',this)" class="custom-control-input" id="{{ $enc_category }}" {{ $isActive }}>
                                                                <label class="custom-control-label " for="{{ $enc_category }}">&nbsp;</label>
                                                            </div>
                                                        </td>
                                                        <td style="vertical-align: middle" class="pb-0">
                                                            <div class="demo text-center mb-0">
                                                                <input type="hidden" id="{{ $enc_category }}-name" value="{{ $category->name  }}">
                                                                <a href="javascript:;" onClick='updateCategory("{{ $enc_category }}")' class="pb-0 btn btn-info btn-icon waves-effect waves-themed" data-toggle="tooltip" data-placement="top" title="" data-original-title="UPDATE">
                                                                    <i class="ni ni-note"></i>
                                                                </a>
                                                                <a href="javascript:;" onClick="showAttributes('{{ $enc_category }}')" class="pb-0 btn btn-success btn-icon waves-effect waves-themed" data-toggle="tooltip" data-placement="top" title="ATTRIBUTES" data-original-title="ATTRIBUTES">
                                                                    <i class="ni ni-list"></i>
                                                                </a>
                                                                <a href="{{ route('settings-sub-categories',['cid' => $enc_category ]) }}" class="pb-0 btn btn-dark btn-icon waves-effect waves-themed" data-toggle="tooltip" data-placement="top" title="" data-original-title="VIEW SUB-CATEGORY">
                                                                  <i class="ni ni-minify-nav fs-md"></i>
                                                                </a>
                                                                <a href="javascript:;" onclick="logsModal('{{ $enc_category }}')" class="pb-0 btn btn-default btn-icon waves-effect waves-themed" data-toggle="tooltip" data-placement="top" title="" data-original-title="HISTORY LOGS">
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
<div class="modal fade" id="attribute-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header pb-2">
                <h4 class="modal-title">
                     ATTRIBUTES
                    <small class="m-0 text-muted">
                        Special character is not allowed Except spaces.
                    </small>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body pt-2">
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" name="category_key" value="" id="category_key">
                        <div class="input-group mb-0  input-group-multi-transition"> <span class="input-group-text"><i class="ni ni-list"></i></span>
                            <input required="" type="text" maxlength="20" class="form-control" name="attribute_name" id="attribute-name" placeholder="Attribute">
                            <div class="input-group-append">
                                <button type="button" onClick="addAttribute()"  class="btn btn-secondary waves-themed waves-effect waves-themed">
                                    ADD ATTRIBUTE
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                        <p id="attribute-result" class="m-0 "></p>
                        <hr>
                    </div>
                </div>
                <div class="panel-content">
                    <table id="attribute-tbl" width="100%" class="table table-bordered m-0">
                        <thead>
                            <tr class="text-center">
                                <th width="10%">#</th>
                                <th width="50%">Attribute</th>
                                <th width="20%">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-center">
                                <th scope="row" style="vertical-align: middle">1</th>
                                <td class="input-group" style="vertical-align: middle">
                                    <input type="text" id="example-input-small" name="example-email-disabled" class="form-control form-control-md" placeholder="Office Seating" disabled>
                                    <a href="" class="pb-0 btn btn-default btn-icon waves-effect waves-themed" data-toggle="tooltip" data-placement="top" title="" data-original-title="UPDATE">
                                        <i class="ni ni-note pt-2s pt-2"></i>
                                    </a>
                                </td>
                                <td style="vertical-align: middle">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="customSwitch2" checked="">
                                        <label class="custom-control-label" for="customSwitch2"></label>
                                    </div>
                                </td>
                            </tr>
                            <tr class="text-center">
                                <th scope="row">2</th>
                                <td>Mark</td>
                                <td>Otto</td>
                            </tr>
                            <tr class="text-center">
                                <th scope="row">3</th>
                                <td>Jacob</td>
                                <td>Thornton</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="category-logs-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
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
<div class="modal fade" id="update-category-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header pb-2">
                <h4 class="modal-title">
                    UPDATE CATEGORY
                    <small class="m-0 text-muted">
                        Special character is not allowed Except spaces.
                    </small>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body pt-2">
                <form class="" role="form" id="create-categories" method="POST" action="{{ route('settings-functions',['id' => 'update-categories']) }}">
                    @csrf()
                    <div class="row">
                        <div class="col-md-12">
                            <div class="input-group">
                                <input type="hidden" name="category" id="update-category-key"/>
                                <input type="text" maxlength="50" name="category_name" id="update-category-name" class="form-control form-control-md" placeholder="">
                                <button type="submit" onClick="$(this).attr('disable',true)" id="update-category-btn" class="pb-0 btn btn-default btn-icon waves-effect waves-themed" data-toggle="tooltip" data-placement="top" title="" data-original-title="UPDATE">
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
            var category_tbl = $('#dt-categories').DataTable({
                responsive: true,
                paging: false,
                sDom: 'lrtip'
            });
            $( "#category-search" ).keyup(function(){
                $("#dt-categories_filter  input[type='search']").val(this.value);
                category_tbl.search(
                    $(this).val(),
                ).draw() ;
            });
        });
        function addAttribute(){
            formData = new FormData();
            var attribute_name = $('#attribute-name').val();
            formData.append('category', selected_category);
            formData.append('name', attribute_name);
            $('#add-attribute-btn').prop('disabled', true);
            $('#attribute-modal').modal('hide');
            $('#attribute-result').removeClass('text-danger');
            $('#attribute-result').removeClass('text-success');
            $('#attribute-result').text('');
            Swal.fire({
                title: 'Are you sure ?',
                text: 'This attribute must be usable.',
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes"
            }).then(function(result){
                if (result.value){
                    $.ajax({
                        type: "POST",
                        url: "{{route('settings-functions',['id' => 'add-attribute'])}}",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (result) {
                            data = { 'category': selected_category }
                            $('#attribute-result').fadeIn('slow');
                            if(result.success == 0){
                                $('#attribute-result').addClass('text-danger');
                                $('#attribute-result').text(result.message);
                                $('#attribute-modal').modal('show');
                            }else{
                                $('#attribute-result').addClass('text-sucess');
                                $('#attribute-result').text(result.message);
                                $("#attribute-tbl").dataTable().fnDestroy();
                                $('#attribute-tbl').DataTable({
                                    "processing": true,
                                    "serverSide": true,
                                    "ajax":{
                                        url :"{{route('settings-functions',['id' => 'attributes-list'])}}", // json datasource
                                        type: "POST",  // method  , by default get
                                        data: data,
                                        error: function(result){  // error handling
                                            $('#err').html(JSON.stringify(data));
                                        }
                                    },
                                    columns: [
                                        { data: 'DT_RowIndex',orderable: false, searchable: false },
                                        { data: 'name', name: 'name'},
                                        { data: 'status', name: 'status'},
                                    ]
                                });
                                $('#attribute-modal').modal('show');
                            }
                            $('#attribute-result').delay(3000).fadeOut(400);
                            $('#add-attribute-btn').prop('disabled', false);
                        },
                        error: function(result){
                            $('#attribute-result').text(result.responseText);
                            $('#attribute-result').delay(3000).fadeOut(400);
                            $('#attribute-modal').modal('show');
                            $('#add-attribute-btn').prop('disabled', false);
                        }
                    });
                }else{
                    $('#attribute-modal').modal('show');
                }
            });
        }
        function updateCategory(key){
             $('#update-category-key').val(key);
             $('#update-category-name').val($('#'+key+"-name").val());
             $('#update-category-modal').modal('show');
        }
        function showAttributes(key){
            selected_category = key;
            data = { 'category': selected_category }
            $('#category_key').val(selected_category);
            $('#attribute-result').text('');
            $('#attribute-result').removeClass('text-success');
            $('#attribute-result').removeClass('text-danger');
            $("#attribute-tbl").dataTable().fnDestroy();
            $('#attribute-tbl').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax":{
                    url :"{{route('settings-functions',['id' => 'attributes-list'])}}", // json datasource
                    type: "POST",  // method  , by default get
                    data: data,
                    error: function(result){  // error handling
                        $('#err').html(JSON.stringify(data));
                    }
                },
                columns: [
                    { data: 'DT_RowIndex',orderable: false, searchable: false },
                    { data: 'name', name: 'name'},
                    { data: 'status', name: 'status'},
                ]
            });
            $('#attribute-modal').modal('show');
        }
        function logsModal(key){
            var url = "{{ route('settings-category-logs-details') }}";
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
            $("#logs-content").load(url+"?cid="+key, function () {
                var data = { 'key': key }
                $("#dt-category-logs").dataTable().fnDestroy();
                $('#dt-category-logs').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax":{
                        url :"{{ route('settings-functions',['id' => 'logs-category-details']) }}", // json datasource
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
            $('#category-logs-modal').modal('show');
        }
        function changeStat(key,objectKey){
            var value = objectKey.checked ? 'ACTIVE' : 'INACTIVE';
            var formData = new FormData();
            formData.append('stat',value);
            formData.append('cid',key);
            $.ajax({
                type: "POST",
                url: "{{route('settings-functions',['id' => 'update-category-status'])}}",
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
