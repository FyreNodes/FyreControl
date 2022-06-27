@extends('layouts.admin')
@include('partials/admin.billing.nav', ['activeTab' => 'plans'])

@section('title')
    Billing - List Plans
@endsection

@section('content-header')
    <h1>Plans<small>Plans for sale at FyreNodes.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li><a href="{{ route('admin.billing') }}">Billing</a></li>
        <li class="active">Plans</li>
    </ol>
@endsection

@section('content')
    @yield('billing::nav')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Plan List</h3>
                    <div class="box-tools">
                        <a href="{{ route('admin.billing.plans.new') }}"><button type="button" class="btn btn-sm btn-primary" style="border-radius: 0 3px 3px 0;margin-left:-1px;">Create</button></a>
                    </div>
                </div>
                <div class="box-body table-responsive no-padding">
                    <table class="table">
                        <tbody>
                        <tr>
                            <th>ID</th>
                            <th>Stripe ID</th>
                            <th>Name</th>
                            <th>Cost</th>
                            <th class="text-right">Actions</th>
                        </tr>
                        @foreach($plans as $plan)
                            <tr>
                                <td>{{$plan->id}}</td>
                                <td>{{$plan->stripe_id}}</td>
                                <td>{{$plan->name}}</td>
                                <td>{{$plan->price}}</td>
                                <td class="text-right">
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.billing.plans.edit', $plan->id) }}"><i class="fa fa-eye"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
