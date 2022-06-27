@extends('layouts.admin')
@include('partials/admin.billing.nav', ['activeTab' => 'general'])

@section('title')
    Billing - General
@endsection

@section('content-header')
    <h1>Configuration<small>Configuration for the billing system.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li class="active">Billing</li>
    </ol>
@endsection

@section('content')
    @yield('billing::nav')
    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('admin.billing.update') }}" method="POST">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Billing Config</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label class="control-label" for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="{{ 0 }}" @if(!$status) selected @endif>Disabled</option>
                                    <option value="{{ 1 }}" @if($status) selected @endif>Enabled</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label" for="currency">Currency</label>
                                <select name="currency" id="currency" class="form-control">
                                    <option value="USD" @if($currency === 'USD') selected @endif>US Dollar</option>
                                    <option value="CAD" @if($currency === 'CAD') selected @endif>Canadian Dollar</option>
                                    <option value="GBP" @if($currency === 'GBP') selected @endif>British Pound</option>
                                    <option value="EUR" @if($currency === 'EUR') selected @endif>European Euro</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                {!! csrf_field() !!}
                {!! method_field('PATCH') !!}
                <button type="submit" name="_method" value="PATCH" class="btn btn-sm btn-primary pull-right">Save</button>
            </form>
        </div>
    </div>
@endsection
