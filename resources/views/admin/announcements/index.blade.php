@extends('layouts.admin')

@section('title')
    Announcements
@endsection

@section('content-header')
    <h1>Announcements<small>You can create, edit, delete announcements.</small></h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}">Admin</a></li>
        <li class="active">Announcements</li>
    </ol>
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-8">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Announcement List</h3>
                    <div class="box-tools">
                        <a href="{{ route('admin.announcements.new') }}"><button type="button" class="btn btn-sm btn-primary" style="border-radius: 0 3px 3px 0;margin-left:-1px;">Create New</button></a>
                    </div>
                </div>
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <tbody>
                        <tr>
                            <th>#</th>
                            <th>Label</th>
                            <th>Title</th>
                            <th>Body</th>
                            <th>Created</th>
                            <th>Updated</th>
                            <th class="text-right">Actions</th>
                        </tr>
                        @foreach ($announcements as $announcement)
                            <tr>
                                <td>{{$announcement->id}}</td>
                                <td>
                                    @foreach ($labels as $label)
                                        @if ($label->id == $announcement->label_id)
                                            <span class="label label-{{ $label->type }}">{{ $label->title }}</span>
                                        @endif
                                    @endforeach
                                </td>
                                <td>{{$announcement->title}}</td>
                                <td>{{str_limit(strip_tags($announcement->body), 50)}}</td>
                                <td>{{$announcement->created_at}}</td>
                                <td>{{$announcement->updated_at}}</td>
                                <td class="text-right">
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.announcements.edit', $announcement->id) }}"><i class="fa fa-pencil"></i></a>
                                    <a class="btn btn-xs btn-danger" data-action="delete" data-id="{{$announcement->id}}"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @if($announcements->hasPages())
                    <div class="box-footer with-border">
                        <div class="col-md-12 text-center">{!! $announcements->appends(['filter' => Request::input('filter')])->render() !!}</div>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-xs-4">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Labels List</h3>
                    <div class="box-tools">
                        <a href="{{ route('admin.announcements.labels.new') }}"><button type="button" class="btn btn-sm btn-primary" style="border-radius: 0 3px 3px 0;margin-left:-1px;">Create New</button></a>
                    </div>
                </div>
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <tbody>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Label</th>
                            <th class="text-right">Actions</th>
                        </tr>
                        @foreach ($labels as $label)
                            <tr>
                                <td>{{$label->id}}</td>
                                <td>{{$label->title}}</td>
                                <td><span class="label label-{{$label->type}}">{{$label->title}}</span></td>
                                <td class="text-right">
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.announcements.labels.edit', $label->id) }}"><i class="fa fa-pencil"></i></a>
                                    <a class="btn btn-xs btn-danger" data-action="delete" data-id="{{$label->id}}"><i class="fa fa-trash"></i></a>
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

@section('footer-scripts')
    @parent
    <script>
        $('[data-action="delete"]').click(function (event) {
            event.preventDefault();
            let self = $(this);
            swal({
                title: '',
                type: 'warning',
                text: 'Are you sure you want to delete this label?',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                confirmButtonColor: '#d9534f',
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
                cancelButtonText: 'Cancel',
            }, function () {
                $.ajax({
                    method: 'DELETE',
                    url: '/admin/announcements/labels/delete',
                    headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')},
                    data: {
                        id: self.data('id')
                    }
                }).done(function (data) {
                    if (data.success === true) {
                        swal({
                            type: 'success',
                            title: 'Success!',
                            text: 'You have successfully deleted this label!'
                        });

                        self.parent().parent().slideUp(1000);
                    } else {
                        swal({
                            type: 'error',
                            title: 'Ooops!',
                            text: (typeof data.error !== 'undefined') ? data.error : 'Failed to delete label! Please try again later...'
                        });
                    }
                }).fail(function (jqXHR) {
                    swal({
                        type: 'error',
                        title: 'Ooops!',
                        text: (typeof jqXHR.responseJSON.error !== 'undefined') ? jqXHR.responseJSON.error : 'A system error has occurred! Please try again later...'
                    });
                });
            });
        });
    </script>
@endsection
