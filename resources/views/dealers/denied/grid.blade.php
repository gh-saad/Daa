@extends('layouts.main')
@php
    if(Auth::user()->type=='super admin')
    {
        $plural_name = __('Customers');
        $singular_name = __('Customer');
    }
    else{

        $plural_name =__('Dealers');
        $singular_name =__('Dealer');
    }
@endphp
@section('page-title')
    {{ $plural_name }}
@endsection
@section('page-breadcrumb')
    {{ $plural_name }}
@endsection
@section('page-action')
    <div>
        @can('user import')
            <a href="#" class="btn btn-sm btn-primary" data-ajax-popup="true" data-title="{{ __('Import') }}"
                data-url="{{ route('users.file.import') }}" data-toggle="tooltip" title="{{ __('Import') }}"><i
                    class="ti ti-file-import"></i>
            </a>
        @endcan
        @can('user manage')
            <a href="{{ route('backend.dealers.denied.list') }}" data-bs-toggle="tooltip" data-bs-original-title="{{ __('List View') }}" class="btn btn-sm btn-primary btn-icon ">
                <i class="ti ti-list"></i>
            </a>
        @endcan
        @can('user create')
            <a href="{{ route('backend.dealers.create') }}" data-bs-toggle="tooltip"  data-bs-original-title="{{ __('Create') }}" class="btn btn-sm btn-primary btn-icon">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection
@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <div id="loading-bar-spinner" class="spinner"><div class="spinner-icon"></div></div>
        @foreach ($dealers as $dealer)
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <!-- add an if condition if @dealer has status -->
                        @if ($dealer->status == 'Rejected')
                            <div class="d-flex align-items-center">
                                <span class="badge bg-danger p-2 px-3 rounded">rejected</span>
                            </div>
                        @elseif ($dealer->status == 'Approved')
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary p-2 px-3 rounded">approved</span>
                            </div>
                        @else
                            <div class="d-flex align-items-center">
                                <span class="badge bg-warning p-2 px-3 rounded">pending</span>
                            </div>
                        @endif
                        <div class="card-header-right">
                            @can('user manage')
                                <div class="btn-group card-option">
                                    @if($dealer->user->is_disable == 1 || Auth::user()->type == "super admin")
                                        <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="true">
                                            <i class="feather icon-more-vertical"></i>
                                        </button>
                                    @else
                                        <div class="btn">
                                            <i class="ti ti-lock"></i>
                                        </div>
                                    @endif
                                    <div class="dropdown-menu dropdown-menu-end" data-popper-placement="bottom-end">
                                        @can('user edit')
                                            <a href="{{ route('backend.dealers.view', $dealer->id) }}" class="dropdown-item"
                                                data-title="{{ __('View '.($singular_name)) }}"
                                                data-toggle="tooltip" data-original-title="{{ __('View') }}">
                                                <i class="ti ti-eye"></i>
                                                <span>{{ __('View') }}</span>
                                            </a>
                                            <a href="{{ route('backend.dealers.edit', $dealer->id) }}" class="dropdown-item"
                                                data-title="{{ __('Edit '.($singular_name)) }}"
                                                data-toggle="tooltip" data-original-title="{{ __('Edit') }}">
                                                <i class="ti ti-pencil"></i>
                                                <span>{{ __('Edit') }}</span>
                                            </a>
                                        @endcan
                                        @can('user delete')
                                            {{ Form::open(['route' => ['backend.dealers.destroy', $dealer->id], 'class' => 'm-0']) }}
                                            @method('DELETE')
                                            <a href="#!" class="dropdown-item bs-pass-para show_confirm" aria-label="Delete"
                                                data-confirm="{{ __('Are You Sure?') }}"
                                                data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                data-confirm-yes="delete-form-{{ $dealer->id }}">
                                                <i class="ti ti-trash"></i>
                                                <span>{{ __('Delete') }}</span>
                                            </a>
                                            {{ Form::close() }}
                                        @endcan
                                    </div>
                                </div>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body  text-center">
                        <img src="{{ check_file($dealer->logo) ? get_file($dealer->logo) :get_file('uploads/dealer-logos/default.png') }}"
                            alt="dealer-image" class="img-fluid rounded-circle" width="120px">
                        <h4 class="mt-2">{{ $dealer->user->name }}</h4>
                        <h4 class="mt-2">{{ $dealer->company_name }}</h4>
                        <small>{{ $dealer->reason }}</small>
                    </div>
                </div>
            </div>
        @endforeach
        @auth('web')
            @can('user create')
                <div class="col-md-3 All">
                    <a href="{{ route('backend.dealers.create') }}" class="btn-addnew-project " style="padding: 90px 10px;">
                        <div class="bg-primary proj-add-icon">
                            <i class="ti ti-plus my-2"></i>
                        </div>
                        <h6 class="mt-4 mb-2">{{ __('New '.($singular_name)) }}</h6>
                        <p class="text-muted text-center">{{ __('Click here to Create New '.($singular_name)) }}</p>
                    </a>
                </div>
            @endcan
        @endauth
    </div>
    <!-- [ Main Content ] end -->
@endsection
