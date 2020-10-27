@extends ('layouts.proprietor-department.app')
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
                                    <img src="{{$imagePath}}" style="margin-top:2cm;width:300px;height:300px;" class="rounded" id="image_preview">
									<img src="{{$imagePathSignature}}" style="width:400px;height:200px;" class="rounded" >
							   </div>
                            </div>
                            <div class="col-lg-6" style="padding:10px;">
                                <form method="post" id="update-password-form" action="{{ route('proprietor-settings-functions', ['id' => 'change-password']) }}">
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
                                        <input type="password" required name="new_password" class="form-control" id="new_password"/>
                                        <input type="hidden" required name="id_user" class="form-control" value="{{ encryptor('encrypt',$user->id) }}"/>
                                    </div>
                                    <div class="form-group">
                                        <label>Confirm Password : </label>
                                        <input type="password"  class="form-control" required disabled id="confirm_password" name="confirm_password"/>
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

<script>
$(document).ready(function(index){
    $(document).on('change','#new_password',function(){
            var temp_val = $(this).val();
            var old_pass = $('#current_password').val();
            if($.trim(temp_val)){
                $("#new_password").removeClass("is-invalid");
                if(temp_val.length>=8){
                    $("#new_password").removeClass("is-invalid");
                    $("#new_password").addClass("is-valid");
                    $("#current_password").removeClass("is-invalid");
                    $("#current_password").addClass("is-valid");
                    if(temp_val!=old_pass){
                        $("#new_password").addClass("is-valid");
                        $("#current_password").addClass("is-valid");
                        $('#confirm_password').removeAttr('disabled');
                    }else{
                        $("#new_password").addClass("is-invalid");
                        $("#current_password").addClass("is-invalid");
                    }
                }else{
                    $("#new_password").addClass("is-invalid");
                }
            }else{
                $("#new_password").addClass("is-invalid");
                $('#confirm_password').attr('disabled','disabled');
            }
    });
    $(document).on('change','#confirm_password',function(){
        var confirm_pass = $(this).val();
        var new_pass = $('#new_password').val();

        if(new_pass!=confirm_pass){
            $('#confirm_password').val("");
            $("#confirm_password").addClass("is-invalid");
        }else{
            $("#confirm_password").removeClass("is-invalid");
            $("#confirm_password").addClass("is-valid");
        }
    });
});
</script>
@endsection