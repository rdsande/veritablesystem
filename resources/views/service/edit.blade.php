@extends('layouts.app')
@section('title', __('service.update'))

        @section('content')
        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="page-content">
                <x-breadcrumb :langArray="[
                                            'service.services',
                                            'service.list',
                                            'service.update',
                                        ]"/>
                <div class="row">
                    <div class="col-12 col-lg-12">
                        <div class="card">
                            <div class="card-header px-4 py-3">
                                <h5 class="mb-0">{{ __('service.details') }}</h5>
                            </div>
                            <div class="card-body p-4">
                                <form class="row g-3 needs-validation" id="serviceForm" action="{{ route('service.update') }}" enctype="multipart/form-data">
                                    {{-- CSRF Protection --}}
                                    @csrf
                                    @method('PUT')

                                    <input type="hidden" name='id' value="{{ $service->id }}" />
                                    <input type="hidden" id="base_url" value="{{ url('/') }}">
                                    <div class="col-md-6">
                                        <x-label for="picture" name="{{ __('app.picture') }}" />
                                        <x-browse-image 
                                                        src="{{ url('/service/getimage/' . $service->image_path) }}"
                                                        name='image' 
                                                        imageid='uploaded-image-1' 
                                                        inputBoxClass='input-box-class-1' 
                                                        imageResetClass='image-reset-class-1' 
                                                        />
                                    </div>
                                    <br>
                                    <div class="col-md-6">
                                        <x-label for="name" name="{{ __('app.name') }}" />
                                        <x-input type="text" name="name" :required="true" value="{{ $service->name }}"/>
                                    </div>
                                    <div class="col-md-6">
                                        <x-label for="unit_price" name="{{ __('app.unit_price') }}" />
                                        <x-input type="text" name="unit_price" :required="true" value="{{ $service->unit_price }}"/>
                                    </div>
                                    <div class="col-md-6">
                                        <x-label for="tax_id" name="{{ __('tax.tax') }}" />
                                        <x-drop-down-taxes selected="{{ $service->tax_id }}" />
                                    </div>
                                    <div class="col-md-6">
                                        <x-label for="tax_type" name="{{ __('tax.tax_type') }}" />
                                        <x-dropdown-status selected="{{ $service->tax_type }}" dropdownName='tax_type' optionNaming='InclusiveExclusive'/>
                                    </div>
                                    <div class="col-md-6">
                                        <x-label for="description" name="{{ __('app.description') }}" />
                                        <x-textarea name="description" value="{{ $service->description }}"/>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <x-label for="status" name="{{ __('app.status') }}" />
                                        <x-dropdown-status selected="{{ $service->status }}" dropdownName='status'/>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="d-md-flex d-grid align-items-center gap-3">
                                            <x-button type="submit" class="primary px-4" text="{{ __('app.submit') }}" />
                                            <x-anchor-tag href="{{ route('dashboard') }}" text="{{ __('app.close') }}" class="btn btn-light px-4" />
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end row-->
            </div>
        </div>
        @endsection

@section('js')
<script src="{{ versionedAsset('custom/js/service/service.js') }}"></script>
@endsection
