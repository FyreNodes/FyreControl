{{-- Pterodactyl - Panel --}}
{{-- Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com> --}}

{{-- This software is licensed under the terms of the MIT license. --}}
{{-- https://opensource.org/licenses/MIT --}}
@extends('layouts.admin')

@section('title')
    Administration
@endsection

@section('content-header')
    <h1>System Overview<small>A quick glance at your system.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li class="active">Overview</li>
    </ol>
@endsection

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box @if($version->isLatest()) box-success @else box-danger @endif">
            <div class="box-header with-border">
                <h3 class="box-title">Application Version @if(!$version->isLatest())is out of date!@endif</h3>
            </div>
            <div class="box-body"><strong>Fyre:</strong> You are running FyreControl <code>v{{ config('app.version') }}</code>. The latest version is <code>v{{ $version->getVersion('fyrecontrol') }}</code><br/><strong>Pterodactyl:</strong> You are running Pterodactyl Panel <code>v{{ config('pterodactyl.version') }}</code>. The latest version is <code>v{{ $version->getVersion('pterodactyl') }}</code></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">Application Information</h3>
            </div>
            <div class="box-body">Name: <code>{{ config('app.name') }}</code><br>Debug: <code>{{ config('app.debug') }}</code><br>App Env: <code>{{ config('app.env') }}</code><br>Timezone: <code>{{ config('app.timezone') }}</code><br>Locale: <code>{{ config('app.locale') }}</code></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-6 col-sm-3 text-center">
        <a href="{{ $version->getDiscord() }}"><button class="btn btn-warning" style="width:100%;"><i class="fa fa-fw fa-support"></i> Get Help <small>(via Discord)</small></button></a>
    </div>
    <div class="col-xs-6 col-sm-3 text-center">
        <a href="https://pterodactyl.io"><button class="btn btn-primary" style="width:100%;"><i class="fa fa-fw fa-link"></i> Documentation</button></a>
    </div>
    <div class="clearfix visible-xs-block">&nbsp;</div>
    <div class="col-xs-6 col-sm-3 text-center">
        <a href="https://github.com/pterodactyl/panel"><button class="btn btn-primary" style="width:100%;"><i class="fa fa-fw fa-support"></i> Github</button></a>
    </div>
    <div class="col-xs-6 col-sm-3 text-center">
        <a href="{{ $version->getDonations() }}"><button class="btn btn-success" style="width:100%;"><i class="fa fa-fw fa-money"></i> Support the Project</button></a>
    </div>
</div>
@endsection
