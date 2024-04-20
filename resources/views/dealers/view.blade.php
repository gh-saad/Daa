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
    {{$plural_name }}
@endsection

@section('page-breadcrumb')
    {{__('View')}}
@endsection

@section('page-action')
    <!-- leave empty -->
@endsection

@section('content')
    <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- show information of the request -->
                        <h2>Dealer Request Information 
                            <!-- add an if condition if @dealer has status -->
                            @if ($dealer->status == 'Rejected')
                                <span class="badge bg-danger p-2 px-3 rounded rounded" style="margin:0px 10px; text-transform: uppercase;">rejected</span>
                            @elseif ($dealer->status == 'Approved')
                                <span class="badge bg-success p-2 px-3 rounded rounded" style="margin:0px 10px; text-transform: uppercase;">approved</span>
                            @else
                                <span class="badge bg-warning p-2 px-3 rounded rounded" style="margin:0px 10px; text-transform: uppercase;">pending</span>
                            @endif
                        </h2>
                        <hr>
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <h5><b>Request Submitted by: </b>{{ $dealer->user->name }} - {{ $dealer->user->email }}<h5>
                                <h5><b>Dealer Name: </b>{{ $dealer->company_name }}<h5>
                                <h5><b>Contract Status: </b>
                                    <!-- add an if condition for $dealer->user->contract-status -->
                                    @if ($dealer->user->{ 'contract-status' } == 'inactive')
                                        <span class="badge bg-danger p-2 px-3 rounded rounded" style="margin:0px 10px; text-transform: uppercase;">inactive</span>
                                    @elseif (!$dealer->user->{ 'contract-status' })
                                        <span class="" style="margin:0px 10px; text-transform: uppercase; color: red;">error no contract found!</span>
                                    @elseif ($dealer->user->{ 'contract-status' } == 'active')
                                        <span class="badge bg-success p-2 px-3 rounded rounded" style="margin:0px 10px; text-transform: uppercase;">active</span>
                                    @else
                                        <span class="badge bg-warning p-2 px-3 rounded rounded" style="margin:0px 10px; text-transform: uppercase;">pending</span>
                                    @endif
                                <h5>
                            </div>
                            <div class="col-md-2 col-sm-0"></div>
                            <div class="col-md-4 col-sm-12">
                                <!-- dealer current balance amount -->
                                <div class="form-group mb-3">
                                    <label class="form-label">Dealer Balance</label>
                                    <input type="number" class="form-control" readonly value="{{ $dealer->user->{'balance-amount'} }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <!-- show personal information of the user -->
                        <h2>Personal Information</h2>
                        <hr>
                        <div id="sub-one" class="row">
                            <div class="col-md-6 col-sm-12">
                                <!-- user name -->
                                <div class="form-group mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" readonly value="{{ $dealer->user->name }}">
                                </div>
                                <!-- email -->
                                <div class="form-group mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" readonly value="{{ $dealer->user->email }}">
                                </div>
                                <!-- contact no -->
                                <div class="form-group mb-3">
                                    <label class="form-label">Contact</label>
                                    <input type="text" class="form-control" readonly value="{{ $dealer->user->contact_no }}">
                                </div>
                                <!-- address -->
                                <div class="form-group mb-3">
                                    <label class="form-label">Address</label>
                                    <input type="text" class="form-control" readonly value="{{ $dealer->user->address }} - {{ $dealer->user->address1 }}">
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <!-- country -->
                                <div class="form-group mb-3">
                                    <label class="form-label">Country</label>
                                    <input type="text" class="form-control" readonly value="{{ $dealer->user->country }} - {{ $dealer->user->state }} - {{ $dealer->user->city }}">
                                </div>
                                <!-- zipcode -->
                                <div class="form-group mb-3">
                                    <label class="form-label">ZipCode</label>
                                    <input type="text" class="form-control" readonly value="{{ $dealer->user->zip_code }}">
                                </div>
                                <!-- gender -->
                                <div class="form-group mb-3">
                                    <label class="form-label">Gender</label>
                                    <input type="text" class="form-control" readonly value="{{ $dealer->user->gender }}">
                                </div>
                                <!-- profile picture -->
                                <div class="form-group mb-3">
                                    <label class="form-label">Profile Photo</label>
                                    <img id="photo-preview" src="{{ asset($dealer->user->avatar) }}" alt="user-photo" class="rounded-circle img-thumbnail m-2 w-25">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <!-- show dealer information -->
                        <h2>Dealer Information</h2>
                        <hr>
                        <div id="sub-one" class="row">
                            <div class="col-md-6 col-sm-12">
                                <!-- dealer name -->
                                <div class="form-group mb-3">
                                    <label class="form-label">Dealer Name</label>
                                    <input type="text" class="form-control" readonly value="{{ $dealer->company_name }}">
                                </div>
                                <!-- dealer website -->
                                <div class="form-group mb-3">
                                    <label class="form-label">Dealer Website</label>
                                    <input type="text" class="form-control" readonly value="{{ $dealer->website }}">
                                </div>
                                <!-- dealer whatsapp -->
                                <div class="form-group mb-3">
                                    <label class="form-label">Dealer Whatsapp</label>
                                    <input type="text" class="form-control" readonly value="{{ $dealer->company_whatsapp }}">
                                </div>
                                <!-- dealer p.o box -->
                                <div class="form-group mb-3">
                                    <label class="form-label">P.O Box</label>
                                    <input type="text" class="form-control" readonly value="{{ $dealer->po_box }}">
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <!-- general manager whatsapp -->
                                <div class="form-group mb-3">
                                    <label class="form-label">General Manager Whatsapp</label>
                                    <input type="text" class="form-control" readonly value="{{ $dealer->GM_whatsapp }}">
                                </div>
                                <!-- marketing director phone -->
                                <div class="form-group mb-3">
                                    <label class="form-label">Marketing Director Phone</label>
                                    <input type="text" class="form-control" readonly value="{{ $dealer->marketing_director_no }}">
                                </div>
                                <!-- relationship manager -->
                                <div class="form-group mb-3">
                                    <label class="form-label">Relationship Manager</label>
                                    <input type="text" class="form-control" readonly value="{{ \App\Models\User::find($dealer->relational_manager)->name }}">
                                </div>
                                <!-- working currency -->
                                <div class="form-group mb-3">
                                    <label class="form-label">Currency</label>
                                    <input type="text" class="form-control" readonly value="{{ $dealer->currency }}">
                                </div>
                            </div>
                            <!-- dealer picture -->
                            <div class="form-group mb-3">
                                <label class="form-label">Dealer Photo</label>
                                <img id="photo-preview" src="{{ asset($dealer->logo) }}" alt="dealer-photo" class="rounded-circle img-thumbnail m-2 w-25">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <!-- show bank information of the user -->
                        <h2>Bank Information</h2>
                        <hr>
                        <div id="sub-one" class="row">
                            <div class="col-md-6 col-sm-12">
                                <!-- bank name -->
                                <div class="form-group mb-3">
                                    <label class="form-label">Bank Name</label>
                                    <input type="text" class="form-control" readonly value="{{ $dealer->bank_name }}">
                                </div>
                                <!-- account name -->
                                <div class="form-group mb-3">
                                    <label class="form-label">Account Name</label>
                                    <input type="text" class="form-control" readonly value="{{ $dealer->ac_name }}">
                                </div>
                                <!-- bank address -->
                                <div class="form-group mb-3">
                                    <label class="form-label">Bank Address</label>
                                    <input type="text" class="form-control" readonly value="{{ $dealer->branch_address }}">
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <!-- swift code -->
                                <div class="form-group mb-3">
                                    <label class="form-label">Swift Code</label>
                                    <input type="text" class="form-control" readonly value="{{ $dealer->swift_code }}">
                                </div>
                                <!-- international bank account number -->
                                <div class="form-group mb-3">
                                    <label class="form-label">Account Number</label>
                                    <input type="text" class="form-control" readonly value="{{ $dealer->iban }}">
                                </div>
                                <!-- branch name -->
                                <div class="form-group mb-3">
                                    <label class="form-label">Branch Name</label>
                                    <input type="text" class="form-control" readonly value="{{ $dealer->branch_name }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <!-- show documents of the dealer -->
                        <h2>Documents</h2>
                        <hr>
                        <!-- dealer document -->
                        <div class="form-group mb-3">
                            <label class="form-label">Dealer Document</label>
                            @php
                                $extension = pathinfo($dealer->dealer_document, PATHINFO_EXTENSION);
                            @endphp
                            @if($extension === 'pdf')
                                <!-- if the document is pdf -->
                                <embed id="document-preview" src="{{ asset($dealer->dealer_document) }}" type="application/pdf" width="100%" height="300px" />
                            @elseif(in_array($extension, ['png', 'jpg', 'jpeg']))
                                <!-- if the document is png/jpg -->
                                <img id="document-preview" src="{{ asset($dealer->dealer_document) }}" alt="dealer-document-photo" class="img-thumbnail m-2 w-25">
                            @else
                                <!-- Handle other file types here -->
                                <p>Unsupported file type</p>
                            @endif
                        </div>
                        <!-- trade license -->
                        <div class="form-group mb-3">
                            <label class="form-label">Trade License</label>
                            @php
                                $extension = pathinfo($dealer->trade_license, PATHINFO_EXTENSION);
                            @endphp
                            @if($extension === 'pdf')
                                <!-- if the document is pdf -->
                                <embed id="document-preview" src="{{ asset($dealer->trade_license) }}" type="application/pdf" width="100%" height="300px" />
                            @elseif(in_array($extension, ['png', 'jpg', 'jpeg']))
                                <!-- if the document is png/jpg -->
                                <img id="document-preview" src="{{ asset($dealer->trade_license) }}" alt="dealer-document-photo" class="img-thumbnail m-2 w-25">
                            @else
                                <!-- Handle other file types here -->
                                <p>Unsupported file type</p>
                            @endif
                        </div>
                        <!-- tax document -->
                        <div class="form-group mb-3">
                            <label class="form-label">Tax Document</label>
                            @php
                                $extension = pathinfo($dealer->tax_document, PATHINFO_EXTENSION);
                            @endphp
                            @if($extension === 'pdf')
                                <!-- if the document is pdf -->
                                <embed id="document-preview" src="{{ asset($dealer->tax_document) }}" type="application/pdf" width="100%" height="300px" />
                            @elseif(in_array($extension, ['png', 'jpg', 'jpeg']))
                                <!-- if the document is png/jpg -->
                                <img id="document-preview" src="{{ asset($dealer->tax_document) }}" alt="dealer-document-photo" class="img-thumbnail m-2 w-25">
                            @else
                                <!-- Handle other file types here -->
                                <p>Unsupported file type</p>
                            @endif
                        </div>
                        <!-- copy of passport -->
                        <div class="form-group mb-3">
                            <label class="form-label">Copy of Passport</label>
                            @php
                                $extension = pathinfo($dealer->passport_copy, PATHINFO_EXTENSION);
                            @endphp
                            @if($extension === 'pdf')
                                <!-- if the document is pdf -->
                                <embed id="document-preview" src="{{ asset($dealer->passport_copy) }}" type="application/pdf" width="100%" height="300px" />
                            @elseif(in_array($extension, ['png', 'jpg', 'jpeg']))
                                <!-- if the document is png/jpg -->
                                <img id="document-preview" src="{{ asset($dealer->passport_copy) }}" alt="dealer-document-photo" class="img-thumbnail m-2 w-25">
                            @else
                                <!-- Handle other file types here -->
                                <p>Unsupported file type</p>
                            @endif
                        </div>
                        <!-- emirates document -->
                        <div class="form-group mb-3">
                            <label class="form-label">Emirates Document</label>
                            @php
                                $extension = pathinfo($dealer->emirates_document, PATHINFO_EXTENSION);
                            @endphp
                            @if($extension === 'pdf')
                                <!-- if the document is pdf -->
                                <embed id="document-preview" src="{{ asset($dealer->emirates_document) }}" type="application/pdf" width="100%" height="300px" />
                            @elseif(in_array($extension, ['png', 'jpg', 'jpeg']))
                                <!-- if the document is png/jpg -->
                                <img id="document-preview" src="{{ asset($dealer->emirates_document) }}" alt="dealer-document-photo" class="img-thumbnail m-2 w-25">
                            @else
                                <!-- Handle other file types here -->
                                <p>Unsupported file type</p>
                            @endif
                        </div>
                        <!-- copy of security cheque -->
                        <div class="form-group mb-3">
                            <label class="form-label">Copy of Security Cheque</label>
                            @php
                                $extension = pathinfo($dealer->security_cheque_copy, PATHINFO_EXTENSION);
                            @endphp
                            @if($extension === 'pdf')
                                <!-- if the document is pdf -->
                                <embed id="document-preview" src="{{ asset($dealer->security_cheque_copy) }}" type="application/pdf" width="100%" height="300px" />
                            @elseif(in_array($extension, ['png', 'jpg', 'jpeg']))
                                <!-- if the document is png/jpg -->
                                <img id="document-preview" src="{{ asset($dealer->security_cheque_copy) }}" alt="dealer-document-photo" class="img-thumbnail m-2 w-25">
                            @else
                                <!-- Handle other file types here -->
                                <p>Unsupported file type</p>
                            @endif
                        </div>
                        <!-- contract -->
                        <div class="form-group mb-3">
                            <label class="form-label">Contract</label>
                            @php
                                $extension = pathinfo($dealer->contract, PATHINFO_EXTENSION);
                            @endphp
                            @if($extension === 'pdf')
                                <!-- if the document is pdf -->
                                <embed id="document-preview" src="{{ asset($dealer->contract) }}" type="application/pdf" width="100%" height="300px" />
                            @elseif(in_array($extension, ['png', 'jpg', 'jpeg']))
                                <!-- if the document is png/jpg -->
                                <img id="document-preview" src="{{ asset($dealer->contract) }}" alt="dealer-document-photo" class="img-thumbnail m-2 w-25">
                            @else
                                <!-- Handle other file types here -->
                                <p>Unsupported file type</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <!-- [ Main Content ] end -->
@endsection