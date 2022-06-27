@extends('layouts.admin')
@section('title')
    Billing - Create Plan
@endsection

@section('content-header')
    <h1>Plans<small>Plans for sale at FyreNodes.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li><a href="{{ route('admin.billing') }}">Billing</a></li>
        <li><a href="{{ route('admin.billing.plans') }}">Plans</a></li>
        <li class="active">Create</li>
    </ol>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('admin.billing.plans.store') }}" method="POST">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Plan Information</h3>
                    </div>
                    <div class="box-body row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name">Plan Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Lite Plan">
                            </div>
                            <div class="form-group">
                                <label for="image">Plan Image</label>
                                <input type="text" class="form-control" id="image" name="image">
                            </div>
                            <div class="form-group">
                                <label for="price">Plan Cost</label>
                                <input type="text" class="form-control" id="price" name="price" placeholder="1.50">
                            </div>
                            <div class="form-group">
                                <label for="description">Plan Description</label>
                                <textarea class="form-control" id="description" name="description"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Plan Resources</h3>
                    </div>
                    <div class="box-body row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="cpu">Plan CPU</label>
                                <input type="text" class="form-control" id="cpu" name="cpu" placeholder="25">
                            </div>
                            <div class="form-group">
                                <label for="memory">Plan Memory</label>
                                <input type="text" class="form-control" id="memory" name="memory" placeholder="256">
                            </div>
                            <div class="form-group">
                                <label for="disk">Plan Disk</label>
                                <input type="text" class="form-control" id="disk" name="disk" placeholder="1024">
                            </div>
                            <div class="form-group">
                                <label for="swap">Plan Swap</label>
                                <input type="text" class="form-control" id="swap" name="swap" placeholder="0">
                            </div>
                            <div class="form-group">
                                <label for="io">Plan IO</label>
                                <input type="text" class="form-control" id="io" name="io" placeholder="500">
                            </div>
                            <div class="form-group">
                                <label for="databases">Plan Databases</label>
                                <input type="text" class="form-control" id="databases" name="databases" placeholder="1">
                            </div>
                            <div class="form-group">
                                <label for="backups">Plan Backups</label>
                                <input type="text" class="form-control" id="backups" name="backups" placeholder="1">
                            </div>
                            <div class="form-group">
                                <label for="allocations">Plan Allocations</label>
                                <input type="text" class="form-control" id="allocations" name="allocations" placeholder="1">
                            </div>
                        </div>
                    </div>
                </div>
                {!! csrf_field() !!}
                <button type="submit" name="_method" value="POST" class="btn btn-sm btn-primary pull-right">Save</button>
            </form>
        </div>
    </div>
    <script src="//cdn.ckeditor.com/ckeditor5/12.4.0/classic/ckeditor.js"></script>
    <script>
        function MinHeightPlugin(editor) {
            this.editor = editor;
        }

        MinHeightPlugin.prototype.init = function() {
            this.editor.ui.view.editable.extendTemplate({
                attributes: {
                    style: {
                        minHeight: '300px',
                        color: '#000'
                    }
                }
            });
        };

        ClassicEditor.builtinPlugins.push(MinHeightPlugin);
        ClassicEditor
            .create( document.querySelector( '#description' ) )
            .catch( error => {
                console.error( error );
            } );
    </script>
@endsection
