@extends ('layouts.it-department.app')
@section ('title')
    Swatches
@endsection
@section ('styles')
<link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
<style>
    .zoom-in:hover {
        -ms-transform: scale(4.5); /* IE 9 */
        -webkit-transform: scale(4.5); /* Safari 3-8 */
        transform: scale(4.5);
    }
    .switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
    }

    .switch input {
    opacity: 0;
    width: 0;
    height: 0;
    }

    .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    -webkit-transition: .4s;
    transition: .4s;
    }

    .slider:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    -webkit-transition: .4s;
    transition: .4s;
    }

    input:checked + .slider {
    background-color: #2196F3;
    }

    input:focus + .slider {
    box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
    -webkit-transform: translateX(26px);
    -ms-transform: translateX(26px);
    transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
    border-radius: 34px;
    }

    .slider.round:before {
    border-radius: 50%;
    }
</style>
@endsection
@section('breadcrumbs')
<li class="breadcrumb-item">Settings</li>
<li class="breadcrumb-item active">Swatches</li>
@endsection

@section('content')
<div class="row mb-3 ">
    <div class="col-lg-12 d-flex flex-start w-100">
        <div class="mr-2 hidden-md-down">
            <span class="icon-stack icon-stack-lg">
                <i class="base base-6 icon-stack-3x opacity-100 color-primary-500"></i>
                <i class="base base-10 icon-stack-2x opacity-100 color-primary-300 fa-flip-vertical"></i>
                <i class="ni ni-settings icon-stack-1x opacity-100 color-white" style="font-size: 14px; margin-bottom: 2px;"></i>
            </span>
        </div>
        <div class="d-flex flex-fill">
            <div class="flex-fill">
                <span class="h5 mt-0">SWATCHES SETTINGS</span>
                <br>
                <p class="mb-0">In creating swatches, please do not add the word <b class="text-dark">SWATCHES</b> in swatches name field.</p>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>
                    Swatch List
                </h2>
                <div class="panel-toolbar">
                    <button class="btn btn-panel waves-effect waves-themed" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                    <button class="btn btn-panel waves-effect waves-themed" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                    <button class="btn btn-panel waves-effect waves-themed" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
                </div>
            </div>
            <div class="panel-container show">
                <div class="panel-content">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="input-group bg-white shadow-inset-2">
                                    <input type="search" id="swatches-search" class="form-control border-right-0 bg-transparent pr-0" placeholder="Search...">
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-transparent border-left-0">
                                            <i class="fal fa-search"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6" align="right">
                                <div class="form-group" align="right">
                                    <button class="btn btn-success" data-toggle="modal" data-target="#add-swatches-modal"><span class="fa fa-plus"></span> Add Swatches</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="dt-swatches" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
                                <thead class="bg-warning-500 text-center">
                                    <tr role="row">
                                        <th width="6%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="0" >No</th>
                                        <th width="10%" class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="1">Image</th>
                                        <th width="20%" class="sorting_asc" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="2" aria-sort="ascending" >Name</th>
                                        <th width="30%" class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="3">Category</th>
                                        <th width="24%" class="sorting" tabindex="0" aria-controls="dt-basic-example" rowspan="1" colspan="1" data-column-index="4">Action</th>
                                    </tr>
                                    </thead>
                                <tbody>
                                    @foreach($swatches as $index=>$swatch)
                                    @php
                                        $enc_swatch_id = encryptor('encrypt',$swatch->id);
                                        $destination = 'assets/img/swatches/';
                                        $filename = encryptor('encrypt',$swatch->id);
                                        $imagePath = imagePath($destination.''.$filename,'//via.placeholder.com/300x300');
                                        $status_swatch = 'INACTIVE';
                                        if($swatch->status == 'INACTIVE'){
                                            $status_swatch = 'ACTIVE';
                                        }
                                    @endphp
                                    <tr role="row " class="odd">
                                        <td  style="vertical-align: middle" tabindex="0" class="text-center">{{ ($index + 1) }}</td>
                                        <td style="vertical-align: middle" tabindex="0" class="sorting_1 text-center">
                                            <img src="{{$imagePath}}" style="width:50px;height:50px;" class="zoom-in">
                                        </td>
                                        <td style="vertical-align: middle" tabindex="0" class="sorting_1 text-center">{{  $swatch->name }}</td>
                                        <td style="vertical-align: middle" class="text-center">{{  ucwords($swatch->category) }}</td>
                                        <td style="vertical-align: middle" class="pb-0">
                                            <div class="demo text-center mb-0">
                                                <input type="hidden" value="{{ $swatch->name }}" id="{{ $enc_swatch_id }}-swatch_name"/>
                                                <input type="hidden" value="{{ $swatch->category }}" id="{{ $enc_swatch_id }}-category"/>
                                                <button  class="pb-0 btn btn-info btn-icon waves-effect waves-themed text-white" onClick="updateSwatch('{{ $imagePath }}','{{$enc_swatch_id}}')" data-toggle="tooltip" data-placement="top" title="" data-original-title="UPDATE">
                                                    <i class="ni ni-note"></i>
                                                </button>
                                                <label class="switch" data-toggle="tooltip" data-placement="top" title="" data-original-title="Change to {{$status_swatch}} ?" >

                                                <input type="checkbox" class="trigger" onChange="updateSwatchStatus('{{ $enc_swatch_id }}')" @if($swatch->status=='ACTIVE') checked @endif>
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="thead-themed">
                                    <tr class="text-center">
                                        <th rowspan="1" colspan="1">No</th>
                                        <th rowspan="1" colspan="1"></th>
                                        <th rowspan="1" colspan="1">Swatch</th>
                                        <th rowspan="1" colspan="1">Category</th>
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
{{--====================================================================================--}}
<div class="modal fade" id="add-swatches-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header pb-2">
                <h4 class="modal-title">
                    Add Swatches
                    <small class="m-0 text-muted">
                        Special character is not allowed Except spaces.
                    </small>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body pt-2">
                <form method="post" id="submit-swatch-form" action="{{ route('settings-functions',['id' => 'add-swatches']) }}" enctype="multipart/form-data">
                    @csrf()
                        <div class="form-group" align="center">
                            <img src="//via.placeholder.com/300x300" id="swatch-preview" class="img-fluid" style="width:300px;height:300px;">
                        </div>
                        <div class="form-group" align="center">
                            <input type="file" class="form-control" required name="swatch-img" id="swatch-img">
                        </div>
                        <div class="form-group">
                            <label>Swatch Name :</label>
                            <input type="text" class="form-control" required name="swatch-name" id="swatch-name">
                        </div>
                        <div class="form-group">
                            <label>Select Category :</label>
                            <select class="form-control" id="select-category" required name="select-category">
                                <option value=""></option>
                                @foreach($categories as $index=>$category)
                                    <option value="{{$index}}">{{$category}}</option>
                                @endforeach
                            </select>
                        </div>
                </form>
            </div>
            <div class="modal-footer pt-2">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" form="submit-swatch-form" class="btn btn-info">Submit</button>
            </div>
        </div>
    </div>
</div>
{{--====================================================================================--}}
<div class="modal fade" id="update-swatches-modal" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header pb-2">
                <h4 class="modal-title">
                    Update Swatches
                    <small class="m-0 text-muted">
                        Special character is not allowed Except spaces.
                    </small>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body pt-2">
                <form method="post" id="update-swatch-form" action="{{ route('settings-functions',['id' => 'update-swatches']) }}" enctype="multipart/form-data">
                    @csrf()
                    @php
                        $defaultLink = '//via.placeholder.com/300x300';
                    @endphp
                      <div class="row" id="update-swatch-content">
                          <div class="col-md-8 offset-2">
                              <div class="form-group" align="center">
                                  <input type="hidden" value="" name="swatch_key" id="swatch_key"/>
                                  <img src="" id="swatch-preview-update" class="img-fluid" >
                                  <input type="file" name="img" onChange="readURL(this.id,'swatch-preview-update','{{ $defaultLink }}')" id="swatch-img-update">
                              </div>
                          </div>
                          <div class="col-md-12">
                              <div class="form-group">
                                  <label>Swatch Name :</label>
                                  <input type="text" class="form-control" required name="swatch-name-update" id="swatch-name-update" value="">
                              </div>
                              <div class="form-group">
                                  <label>Select Category :</label>
                                  <select class="form-control" id="category-update" required name="select-category-update">
                                      <option value=""></option>
                                      @foreach($categories as $index=>$category)
                                          <option value="{{$index}}">{{$category}}</option>
                                      @endforeach
                                  </select>
                              </div>
                          </div>
                      </div>
                </form>
            </div>
            <div class="modal-footer pt-2">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" form="update-swatch-form" class="btn btn-warning">Update</button>
            </div>
        </div>
    </div>
</div>
{{--====================================================================================--}}
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
    <script src="{{  asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
    <script>
        $(function(){
            var swatch_tbl = $('#dt-swatches').DataTable({
                responsive: true,
                paging: false,
                sDom: 'lrtip'
            });

            $( "#swatches-search" ).keyup(function() {
               $("#dt-swatches_filter  input[type='search']").val(this.value);
                 swatch_tbl.search(
                    $(this).val(),
                ).draw() ;
            });
        });

        function updateSwatch(img,key){
            var name = $('#'+key+'-swatch_name').val();
            var category = $('#'+key+'-category').val();
            $("#category-update").val(category).change();
            $('#swatch_key').val(key);
            $('#swatch-name-update').val(name);
            $('#swatch-preview-update').attr('src',img);
            $('#update-swatches-modal').modal('show');
        }

        function updateSwatchStatus(id){
            formData = new FormData();
            formData.append('id',id);
            $.ajax({
                type: "POST",
                url: "{{ route('settings-functions',['id' => 'swatches-status']) }}",
                data: formData,
                CrossDomain:true,
                contentType: !1,
                processData: !1,
                success: function(e) {

                },
                error: function(result){
                    alert('error');
                }
            });
        }

        $(document).ready(function(index){
            $("#select-category").select2({
                placeholder: "Select Category",
                allowClear: true,
                width:'100%',
                dropdownParent: $('#add-swatches-modal')
            });
            $('#swatch-img').change(function(){
                readURL(this);
            });


        });
    </script>
@endsection
