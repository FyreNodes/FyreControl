@extends('layouts.admin')
@include('partials/admin.billing.nav', ['activeTab' => 'types'])

@section('title')
    Billing - View Types
@endsection

@section('content-header')
    <h1>Types<small>Available server types for instance deployment.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li><a href="{{ route('admin.billing') }}">Billing</a></li>
        <li class="active">Types</li>
    </ol>
@endsection

@section('content')
    @yield('billing::nav')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Type List</h3>
                    <div class="box-tools">
                        <a href="{{ route('admin.billing.types.new') }}"><button type="button" class="btn btn-sm btn-primary" style="border-radius: 0 3px 3px 0;margin-left:-1px;">Create</button></a>
                    </div>
                </div>
                <div class="box-body table-responsive no-padding">
                    <table class="table">
                        <tbody>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Egg ID</th>
                            <th>Default Image</th>
                            <th class="text-right">Actions</th>
                        </tr>
                        @foreach($types as $type)
                            <tr>
                                <td>{{$type->id}}</td>
                                <td>{{$type->name}}</td>
                                <td>{{$type->egg}}</td>
                                <td>{{$type->default_image}}</td>
                                <td class="text-right">
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.billing.types.edit', $type->id) }}"><i class="fa fa-pencil"></i></a>
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
