@extends ('layouts.logistics-department.app')
@section('title')
    Delivery Schedule
@endsection
@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/datagrid/datatables/datatables.bundle.css') }}"/>
<link href="{{ asset('assets/css/formplugins/select2/select2.bundle.css') }}" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css" rel="stylesheet">
<style>
    .select2-dropdown {
        z-index: 999999;
    }
    .bootstrap-tagsinput {
        width: 100% !important;
    }
</style>
@endsection
@section('breadcrumbs')
<li class="breadcrumb-item active">Delivery Schedule</li>
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
                    <span class="h5 mt-0">Delivery Schedule</span>
                    <br>
                    <p class="mb-0">Add note here if applicable.</p>
                </div>
            </div>
            <div class="col-md-7">
                <div class="row">
                    <div class="col-md-12 m-0">
                        <div class="form-group" align="right">
                            <button class="btn btn-primary"><span class="fa fa-plus"></span> Add Delivery Schedule</button>
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
            <div class="panel-hdr">
                <h2>
                    Schedule Request List
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
                        <div class="col-sm-12">
                            <table id="clients-tbl" class="table table-bordered table-hover w-100 dataTable dtr-inline">
                                <thead class="bg-warning-500">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th>Delivery Date</th>
                                        <th>DR Number</th>
                                        <th>Client</th>
                                        <th>Requested By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>
                                            October 21, 2020 <br class="m-0"><small>[ 4 days ago ] 03:43 pm</small>
                                            <hr class="m-0">
                                            <b>Date Request :</b>
                                            September 19, 2020 
                                            <small>[ 1 month ago ]08:34 am</small>
                                        </td>
                                        <td>
                                            JEC-DR7301520093008
                                            <hr class="m-0">
                                            <b class="text-info">7301520093008</b>
                                        </td>
                                        <td>
                                        ROYALE COLD STORAGE NORTH INC. [<b class="text-danger">DELIVER</b>]
                                        <hr class="m-0">
                                        <b>Shipping Address:</b> <br class="m-0"> Miguel Villarica Road Sta. Rosa Marilao Bulacan, Miguel Villarica Road Sta. Rosa , Marilao Bulacan
                                        </td>
                                        <td>Rosie Lyn Capinig</td>
                                        <td></td>
                                    </tr>
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

{{--====================================================================================--}}
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/formplugins/select2/select2.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/datagrid/datatables/datatables.bundle.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
        $(function(){
          
        });

       
        $(document).ready(function(index){
         
        });
    </script>
@endsection