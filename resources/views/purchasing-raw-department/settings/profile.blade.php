@extends ('layouts.purchasing-raw-department.app')
@section ('title')
    User Profile
@endsection
@section ('styles')

@endsection
@section('breadcrumbs')
<li class="breadcrumb-item">Users</li>
<li class="breadcrumb-item active">User Profile</li>
@endsection
@section('content')
<div class="row mb-3 ">
    <div class="col-lg-12 d-flex flex-start w-100 mb-2">
        <div class="mr-2 hidden-md-down">
            <span class="icon-stack icon-stack-lg">
                <i class="base base-6 icon-stack-3x opacity-100 color-primary-500"></i>
                <i class="base base-10 icon-stack-2x opacity-100 color-primary-300 fa-flip-vertical"></i>
                <i class="ni ni-settings icon-stack-1x opacity-100 color-white" style="font-size: 14px; margin-bottom: 2px;"></i>
            </span>
        </div>
        <div class="row d-flex flex-fill">
            <div class="col-lg-7 flex-fill">
                <span class="h5 mt-0">User Profile</span>
                <br>
                <p class="mb-0">Before changing profile picture please use <b class="text-dark">JECAMS Formal Profile Picture</b> to avoid being different employee.</p>
            </div>
            <div class="col-lg-5 form-group">

            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>

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
                            <div class="col-lg-6" >
                                <div class="form-group" align="center">
                                    @php
                                        $destination = 'assets/img/employee/profile/';
                                        $filename = $user->employee->employee_num;
                                        $imagePath = imagePath($destination.''.$filename,'//via.placeholder.com/400X400');

                                        $firstname = strtoupper($user->employee->first_name);
                                        $middlename = strtoupper($user->employee->middle_name);
                                        $surname = strtoupper($user->employee->last_name);
                                        $prefix = '';
                                        if($user->employee->prefix!='N/A'){
                                        $prefix = strtoupper($user->employee->prefix);
                                        }
                                        $name = $firstname.' '.$middlename.' '.$surname.' '.$prefix;
                                        $destinationSignature = 'assets/img/employee/signature/';
                                        $filenameSignature = $user->employee->employee_num;
                                        $imagePathSignature = imagePath($destinationSignature.''.$filenameSignature,'//via.placeholder.com/400X200');
                                    @endphp
                                    <img src="{{$imagePath}}" style="margin-top:2cm;width:300px;height:300px;" class="rounded m-0" id="image_preview">
                                    <img src="{{$imagePathSignature}}" style="width:400px;height:200px;" class="rounded" >
                               </div>
                          </div>
                            <div class="col-lg-6" style="padding:10px;">
                                <form method="post" id="update-password-form" action="{{ route('purchasing-raw-settings-functions', ['id' => 'change-password']) }}">
                                @csrf()
                                    <div class="form-group">
                                        <label>Name : </label>
                                        <input type="text" disabled value="{{ $name }}" class="form-control"/>
                                    </div>
                                    <div class="form-group">
                                        <label>Job Title : </label>
                                        <input type="text" disabled value="{{ strtoupper($user->department->name) }} DEPARTMENT | {{ strtoupper($user->position->name) }}" class="form-control"/>
                                    </div>
                                    <input type="hidden" required name="id_user" class="form-control" value="{{ encryptor('encrypt',$user->id) }}"/>
                                    <div class="form-group">
                                        <label>Username : </label>
                                        <input type="text" disabled value="{{ $user->username }}" class="form-control"/>
                                    </div>
                                    <div class="form-group">
                                        <label>Current Password : </label>
                                        <input type="password" required name="current_password" id="current_password" class="form-control"/>
                                    </div>
                                    <div class="form-group">
                                        <label>New Password : </label>
                                        <input type="password" required name="password" class="form-control" id="new_password"/>
                                        <input type="hidden" required name="id_user" class="form-control" value="{{ encryptor('encrypt',$user->id) }}"/>
                                    </div>
                                    <div class="form-group">
                                        <label>Confirm Password : </label>
                                        <input type="password"  class="form-control" required  id="confirm_password" name="password_confirmation"/>
                                    </div>
                                    <div class="form-group" align="right">
                                        <button type="submit" class="btn btn-warning">Update Password</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')

@endsection
