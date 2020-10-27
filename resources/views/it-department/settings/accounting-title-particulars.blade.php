@extends ('layouts.it-department.app')
@section ('title')
    Accounting Ttiles | {{ $account_title->name }} | Particulars
@endsection
@section ('styles')
<link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
@endsection
@section('breadcrumbs')
<li class="breadcrumb-item">Settings</li>
<li class="breadcrumb-item active">Accounting Titles</li>
<li class="breadcrumb-item ">{{ $account_title->name }}</li>
<li class="breadcrumb-item active">Particulars</li>
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
            <div class="col-md-5 ">
                <div class="flex-fill">
                    <span class="h5 mt-0">{{ strtoupper($account_title->name) }} PARTICULARS</span>
                    <br>
                    <p class="mb-0">{{ $account_title->name }} particular list</p>
                </div>
            </div>
            <div class="col-md-7">
                <div class="row">
                     <div class="col-md-8 text-right">
                         <div class="input-group bg-white shadow-inset-2">
                             <input type="search" id="accounting-title-particulars" class="form-control border-right-0 bg-transparent pr-0" placeholder="Search...">
                             <div class="input-group-append">
                                <span class="input-group-text bg-transparent border-left-0">
                                    <i class="fal fa-search"></i>
                                </span>
                             </div>
                         </div>
                     </div>
                    <div class="col-md-4 m-0">
                        <div class="form-group" align="right">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#add-accounting-title-particulars-modal"><span class="fa fa-plus"></span> | Particulars</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div id="panel-1" class="panel">
            <div class="panel-container show">
                <div class="panel-content">
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="dt-accounting-title-particulars" class="table table-bordered table-hover w-100 dataTable dtr-inline" role="grid" aria-describedby="dt-basic-example_info" style="width: 1222px;">
                                <thead class="bg-warning-500 text-center">
                                     <tr role="row">
                                        <th width="6%">No</th>
                                        <th width="50%">Name</th>
                                        <th width="25%">Date Created</th>
                                        <th width="5%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($account_title->particulars as $index=>$particular)
                                        @php
                                            $enc_account_title_id = encryptor('encrypt',$particular->account_title_id);
                                            $enc_account_title_particular_id = encryptor('encrypt',$particular->id);
                                        @endphp
                                        <tr>
                                            <td>{{ $index }}</td>
                                            <td>
                                                {{ $particular->name }}
                                                <hr class="m-0">
                                                <text class="text-info">Created By: {{ $particular->createdBy->username }}</text>
                                            </td>
                                            <td>
                                                {{ readableDate($particular->created_at,'time') }}
                                                <hr class="m-0">
                                                <text class="text-info">Last Update By: {{ $particular->updatedBy->username }}</text>
                                            </td>
                                            <td>
                                                <div id="particular-{{ $enc_account_title_particular_id }}" style="display:none">
                                                    <div class="form-group">
                                                        <label>Particular Name :</label>
                                                        <input type="hidden" name="particular_key" value="{{ $enc_account_title_particular_id }}"/>
                                                        <input type="hidden" name="key" value="{{ $enc_account_title_id }}"/>
                                                        <input type="text" class="form-control" required name="particulars" value="{{ $particular->name }}">
                                                    </div>
                                                </div>
                                                <button onClick="updateParticular('{{ $enc_account_title_particular_id }}')" class="btn btn-icon btn-info btn-sm"><span class="fas fa-edit"></span></button>
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
{{--====================================================================================--}}
<div class="modal fade" id="add-accounting-title-particulars-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header pb-2">
                <h4 class="modal-title">
                    Add Account Particular
                    <small class="m-0 text-muted">
                        Special character is not allowed Except spaces.
                    </small>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body pt-2">
                <form method="post" id="submit-accounting-title-form" action="{{ route('settings-functions',['id' => 'add-accounting-title-particular']) }}">
                    @csrf()
                        <div class="form-group">
                            <label>Particular Name :</label>
                            <input type="hidden" name="key" value="{{ encryptor('encrypt',$account_title->id) }}"/>
                            <input type="text" class="form-control" required name="particulars">
                        </div>
                </form>
            </div>
            <div class="modal-footer pt-2">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" form="submit-accounting-title-form" class="btn btn-warning">Submit</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="update-accounting-title-particulars-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content">
            <div class="modal-header pb-2">
                <h4 class="modal-title">
                    Update Account Particular
                    <small class="m-0 text-muted">
                        Special character is not allowed Except spaces.
                    </small>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body pt-2">
                <form method="post" id="update-accounting-title-form" action="{{ route('settings-functions',['id' => 'update-accounting-title-particular']) }}">
                    @csrf()
                      <div id="update-accounting-title-content">

                      </div>
                </form>
            </div>
            <div class="modal-footer pt-2">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" form="update-accounting-title-form" class="btn btn-warning">Update</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
    <script src="{{ asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
    <script>
        $(function(){
            var accounting_title_tbl = $('#dt-accounting-titles').DataTable({
                responsive: true,
                paging: false,
                sDom: 'lrtip'
            });

            $( "#accounting-title-particulars-search" ).keyup(function() {
               $("#dt-accounting-title-particulars_filter  input[type='search']").val(this.value);
                 accounting_title_tbl.search(
                    $(this).val(),
                ).draw() ;
            });
        });
        function updateParticular(key){
            $('#update-accounting-title-content').html($('#particular-'+key).html());
            $('#update-accounting-title-particulars-modal').modal('show');
        }
    </script>
@endsection
