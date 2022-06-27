@extends('layouts.admin')
@section('title')
    Billing - Edit Type
@endsection

@section('content-header')
    <h1>Types<small>Available server types for instance deployment.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li><a href="{{ route('admin.billing') }}">Billing</a></li>
        <li><a href="{{ route('admin.billing.types') }}">Types</a></li>
        <li class="active">Edit</li>
    </ol>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('admin.billing.types.update', $type->id) }}" method="POST">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Type Information</h3>
                    </div>
                    <div class="box-body row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $type->name }}"/>
                            </div>
                            <div class="form-group">
                                <label for="default_image">Default Image</label>
                                <input type="text" class="form-control" id="default_image" name="default_image" value="{{ $type->default_image }}"/>
                            </div>
                            <div class="form-group">
                                <label for="egg">Egg</label>
                                <input type="text" class="form-control" id="egg" name="egg" value="{{ $type->egg }}"/>
                            </div>
                        </div>
                    </div>
                </div>
                {!! csrf_field() !!}
                {{ method_field('PATCH') }}
                <button type="submit" class="btn btn-sm btn-primary pull-right">Save</button>
            </form>
            <form action="{{ route('admin.billing.types.delete', $type->id) }}" method="POST">
                {!! csrf_field() !!}
                {!! method_field('DELETE') !!}
                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
            </form>
        </div>
    </div>
@endsection
