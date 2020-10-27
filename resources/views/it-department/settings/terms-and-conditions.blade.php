@extends ('layouts.it-department.app')
@section ('title')
    Terms and Conditions
@endsection
@section ('styles')
<link href="//cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
@endsection
@section('breadcrumbs')
<li class="breadcrumb-item">Settings</li>
<li class="breadcrumb-item active">Terms and Conditions</li>
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
                <span class="h5 mt-0">TERMS AND CONDITIONS</span>
                <br>
                <p class="mb-0">Add note here if applicable.</p>
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
                    Terms and Conditions
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
							<ul class="nav nav-tabs" role="tablist">
								<li class="nav-item  "><a class="nav-link active fs-lg text-primary" data-toggle="tab" href="#quotation-term-tab" role="tab">QUOTAION</a></li>
								<li class="nav-item "><a class="nav-link fs-lg text-success" data-toggle="tab" href="#po-term-tab" role="tab">PURCHASE ORDER</a></li>
							</ul>
						</div>
                        <div class="col-md-12">
						<br>
						<div class="tab-content">
							<div class="tab-pane show active" id="quotation-term-tab" role="tabpanel">
								<form id="quotation-term-condtion-form" method="POST" action="{{ route('settings-functions',['id' => 'update-terms-and-conditions']) }}">
								    @csrf()
								    <textarea style="display:none" id="quote_term" name="quote_term" required>
										{{ $quotation_terms['data'] }}
									</textarea>
									<input type="hidden" name="term-type" value="quotation" readonly/>
								</form>
								<div align="right">
					                <button type="submit" class="btn btn-warning pull-right" form="quotation-term-condtion-form">Update</button>
					            </div>
							</div>
							<div class="tab-pane show" id="po-term-tab" role="tabpanel">
								<form id="po-term-condtion-form" method="POST" action="{{ route('settings-functions',['id' => 'update-terms-and-conditions']) }}">
								    @csrf()
								    <textarea style="display:none" id="po_term" name="po_term" required>
										{{ $po_terms['data'] }}
									</textarea>
									<input type="hidden" name="term-type" value="po" readonly/>
								</form>
								<div align="right">
					                <button type="submit" class="btn btn-warning pull-right" form="po-term-condtion-form">Update</button>
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
<!-- ============================================================================ -->
<form id="action-form" method="POST" action="{{ route('quotation-functions',['id' => 'action-quotation']) }}">
    @csrf()
	<input class="form-control" name="quotationId" readonly type="hidden" />
	<input class="form-control" name="actionMode" readonly type="hidden" />
</form>

@endsection
@section('scripts')
<script src="//cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script>
$(function(){
	$('#quote_term, #po_term').summernote({
		toolbar: [
			['style', ['style']],
			['font', ['bold', 'underline', 'clear']],
			['para', ['ul', 'ol', 'paragraph']],
			//['table', ['table']],
			//['view', ['fullscreen', 'codeview', 'help']]
		],
		height:400
	});
	
});
</script>
@endsection