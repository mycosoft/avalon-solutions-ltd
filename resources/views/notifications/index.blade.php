@extends('adminlte::page')

@section('title', 'Notifications')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Notifications</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Notifications</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">All Notifications</h3>
                        @if($notifications->where('read_at', null)->count() > 0)
                            <div class="card-tools">
                                <button onclick="event.preventDefault(); document.getElementById('mark-all-form').submit();" class="btn btn-info btn-sm">
                                    Mark All as Read
                                </button>
                                <form id="mark-all-form" action="{{ route('notifications.mark-all-read') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        @forelse($notifications as $notification)
                            <div class="notification-item mb-3 p-3 rounded {{ $notification->read_at ? 'bg-light' : 'bg-white border-left-primary' }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-1">
                                            @switch($notification->type)
                                                @case('alert')
                                                    <i class="fas fa-exclamation-triangle text-danger mr-2"></i>
                                                    @break
                                                @case('warning')
                                                    <i class="fas fa-exclamation-circle text-warning mr-2"></i>
                                                    @break
                                                @case('success')
                                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                                    @break
                                                @default
                                                    <i class="fas fa-info-circle text-info mr-2"></i>
                                            @endswitch
                                            <strong>{{ $notification->title }}</strong>
                                            @if(!$notification->read_at)
                                                <span class="badge badge-primary ml-2">New</span>
                                            @endif
                                        </div>
                                        <p class="mb-1">{{ $notification->message }}</p>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    @if(!$notification->read_at)
                                        <a href="{{ route('notifications.read', $notification->id) }}" class="btn btn-sm btn-outline-info">
                                            Mark as Read
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-muted">No notifications.</p>
                        @endforelse
                        <div class="mt-4">
                            {{ $notifications->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
