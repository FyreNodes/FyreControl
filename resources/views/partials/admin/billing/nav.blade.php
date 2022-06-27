@section('billing::nav')
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom nav-tabs-floating">
                <ul class="nav nav-tabs">
                    <li @if($activeTab === 'general') class="active" @endif><a href="{{ route('admin.billing') }}">General</a></li>
                    <li @if($activeTab === 'plans') class="active" @endif><a href="{{ route('admin.billing.plans') }}">Plans</a></li>
                    <li @if($activeTab === 'types') class="active" @endif><a href="{{ route('admin.billing.types') }}">Types</a></li>
                </ul>
            </div>
        </div>
    </div>
@endsection
