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
        <a href="{{ route('backend.dealers.denied.grid') }}" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Grid View') }}" class="btn btn-sm btn-primary btn-icon">
            <i class="ti ti-layout-grid"></i>
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
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table mb-0 pc-dt-simple" id="users">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">{{ __('Image') }}</th>
                                        <th scope="col">{{ __('User Name') }}</th>
                                        <th scope="col">{{ __('Company Name') }}</th>
                                        <th scope="col">{{ __('Reason') }}</th>
                                        <th scope="col">{{ __('Status') }}</th>
                                        @if (Gate::check('user edit') || Gate::check('user delete'))
                                            <th width="10%"> {{ __('Action') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dealers as $index => $dealer)
                                        <tr>
                                            <th scope="row">{{++$index}}</th>
                                            <td>
                                                <a>
                                                    <img src="{{ check_file($dealer->logo) ? get_file($dealer->logo) :get_file('uploads/dealer-logos/default.png') }}" class="img-fluid rounded-circle card-avatar" width="35" id="blah3">
                                                </a>
                                            </td>
                                            <td>{{$dealer->user->name}}</td>
                                            <td>{{$dealer->company_name}}</td>
                                            <td>{{$dealer->reason}}</td>
                                            <td>
                                                <!-- add an if condition if @dealer has status -->
                                                @if ($dealer->status == 'Rejected')
                                                    <span class="badge bg-danger p-2 px-3 rounded rounded">rejected</span>
                                                @elseif ($dealer->status == 'Approved')
                                                    <span class="badge bg-success p-2 px-3 rounded rounded">approved</span>
                                                @else
                                                    <span class="badge bg-warning p-2 px-3 rounded rounded">pending</span>
                                                @endif
                                            </td>
                                            <td class="text-center me-3">
                                                @if($dealer->user->is_disable == 1 || Auth::user()->type == 'super admin')
                                                    @if(Auth::user()->type == "super admin")
                                                        <!-- dont know what to do here so leave it as it is -->
                                                        <div class="action-btn bg-primary ms-2">
                                                            <a data-url="{{ route('company.info',$dealer->id) }}" class="mx-3 btn btn-sm d-inline-flex align-items-center"  data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" data-title="{{ __('Admin Hub')}}"> <span class="text-white"><i class="ti ti-replace"></i></a>
                                                        </div>
                                                        <div class="action-btn bg-secondary ms-2">
                                                            <a href="{{ route('login.with.company',$dealer->id) }}" class="mx-3 btn btn-sm d-inline-flex align-items-center"   data-bs-toggle="tooltip" data-bs-original-title="{{ __('Login As Company')}}"> <span class="text-white"><i class="ti ti-replace"></i></a>
                                                        </div>
                                                    @endif
                                                    @can('user edit')

                                                        <!-- view button -->
                                                        <div class="action-btn bg-info ms-2">
                                                            <a href="{{ route('backend.dealers.view', $dealer->id) }}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-title="{{ __('View '.($singular_name)) }}" data-bs-toggle="tooltip" data-bs-original-title="{{ __('View')}}"> <span class="text-white"> <i class="ti ti-eye"></i></span></a>
                                                        </div>
                                                        
                                                        <!-- edit button -->
                                                        <div class="action-btn bg-warning ms-2">
                                                            <a href="{{ route('backend.dealers.edit', $dealer->id) }}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-title="{{ __('Edit '.($singular_name)) }}" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Edit')}}"> <span class="text-white"> <i class="ti ti-edit"></i></span></a>
                                                        </div>

                                                    @endcan
                                                    @can('user delete')
                                                    <div class="action-btn bg-danger ms-2">
                                                        {{ Form::open(['route' => ['backend.dealers.destroy', $dealer->id], 'class' => 'm-0']) }}
                                                        @method('DELETE')
                                                        <a href="#"
                                                            class="mx-3 btn btn-sm  align-items-center bs-pass-para show_confirm"
                                                            data-bs-toggle="tooltip" title=""
                                                            data-bs-original-title="Delete" aria-label="Delete"
                                                            data-confirm-yes="delete-form-{{ $dealer->id }}"><i
                                                                class="ti ti-trash text-white text-white"></i></a>
                                                        {{ Form::close() }}
                                                    </div>
                                                    @endcan
                                                @else
                                                    <div class="text-center">
                                                        <i class="ti ti-lock"></i>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <!-- [ Main Content ] end -->
@endsection