@if ($errors->any())
    <div class="row">
        <div class="col-md-12">
            @foreach ($errors->all() as $error)
                <div class="alert alert-danger alert-dismissible " role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">×</span>
                    </button>
                    <strong>{{ $error }}</strong>
                </div>
            @endforeach
        </div>
    </div>
@endif

@if (\Session::has('success'))
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-success alert-dismissible " role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">×</span>
                </button>
                <strong>{!! \Session::get('success') !!}</strong>
            </div>
        </div>
    </div>
@endif

@if (\Session::has('info'))
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info alert-dismissible " role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">×</span>
                </button>
                <strong>{!! \Session::get('info') !!}</strong>
            </div>
        </div>
    </div>
@endif

@if (\Session::has('warning'))
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-warning alert-dismissible " role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">×</span>
                </button>
                <strong>{!! \Session::get('warning') !!}</strong>
            </div>
        </div>
    </div>
@endif
