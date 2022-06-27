{{-- Pterodactyl - Panel --}}
{{-- Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com> --}}

{{-- This software is licensed under the terms of the MIT license. --}}
{{-- https://opensource.org/licenses/MIT --}}
@extends('layouts.admin')

@section('title')
    Announcements
@endsection

@section('content-header')
    <h1>Announcements
        <small>You can create announcements labels.</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li class="active">Announcements</li>
    </ol>
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Create Announcement Label</h3>
                    <div class="box-tools">
                        <a href="{{ route('admin.announcements') }}">
                            <button type="button" class="btn btn-sm btn-primary"
                                    style="border-radius: 0 3px 3px 0;margin-left:-1px;">Go Back
                            </button>
                        </a>
                    </div>
                </div>
                <form method="post" action="{{ route('admin.announcements.labels.create')  }}">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" name="title" id="title" class="form-control" value="" />
                        </div>

                        <div class="form-group">
                            <label for="public" class="control-label">Types</label>
                            <div class="row">
                                <div class="col-md-2">
                                    <input type="radio" name="type" value="default" id="type"> 
                                    <span class="label label-default">Default</span>
                                </div>
                                <div class="col-md-2">
                                    <input type="radio" name="type" value="success" id="type"> 
                                    <span class="label label-success">Success</span>
                                </div>
                                <div class="col-md-2">
                                    <input type="radio" name="type" value="primary" id="type"> 
                                    <span class="label label-primary">Primary</span>
                                </div>
                                <div class="col-md-2">
                                    <input type="radio" name="type" value="info" id="type"> 
                                    <span class="label label-info">Info</span>
                                </div>
                                <div class="col-md-2">
                                    <input type="radio" name="type" value="warning" id="type"> 
                                    <span class="label label-warning">Warning</span>
                                </div>
                                <div class="col-md-2">
                                    <input type="radio" name="type" value="danger" id="type"> 
                                    <span class="label label-danger">Danger</span>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="box-footer">
                        {!! csrf_field() !!}
                        <button class="btn btn-success pull-right" type="submit">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('footer-scripts')
    @parent
@endsection
