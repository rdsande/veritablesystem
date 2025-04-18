@extends('layouts.app')
@section('title', __('warehouse.stock_transfer'))

        @section('content')
        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="page-content">
                <x-breadcrumb :langArray="[
                                            'warehouse.warehouse',
                                            'warehouse.stock_transfer_list',
                                            'warehouse.new_transfer',
                                        ]"/>
                <div class="row">
                    <form class="g-3 needs-validation" id="stockTransferForm" action="{{ route('stock_transfer.update') }}" enctype="multipart/form-data">
                        {{-- CSRF Protection --}}
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="transfer_id" value="{{ $transfer->id }}">
                        <input type="hidden" name="row_count" value="0">
                        <input type="hidden" name="row_count_payments" value="0">
                        <input type="hidden" id="base_url" value="{{ url('/') }}">

                        <input type="hidden" id="operation" name="operation" value="{{ $transfer->operation }}">

                        <div class="row">
                            <div class="col-12 col-lg-12">
                                <div class="card">
                                    <div class="card-header px-4 py-3">
                                        <h5 class="mb-0">{{ __('sale.details') }}</h5>
                                    </div>
                                    <div class="card-body p-4 row g-3">

                                            <div class="col-md-4">
                                                <x-label for="transfer_date" name="{{ __('app.date') }}" />
                                                <div class="input-group mb-3">
                                                    <x-input type="text" additionalClasses="datepicker-edit" name="transfer_date" :required="true" value="{{ $transfer->formatted_transfer_date }}"/>
                                                    <span class="input-group-text" id="input-near-focus" role="button"><i class="fadeIn animated bx bx-calendar-alt"></i></span>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <x-label for="warehouse_id" name="{{ __('warehouse.from_warehouse') }}" />
                                                <x-dropdown-warehouse selected="" dropdownName='warehouse_id' :enableToSelect="true" />
                                            </div>
                                            <div class="col-md-4">
                                                <x-label for="to_warehouse_id" name="{{ __('warehouse.to_warehouse') }}" />
                                                <x-dropdown-warehouse selected="" dropdownName='to_warehouse_id' :enableToSelect="true" />
                                            </div>
                                            <div class="col-md-4">
                                                <x-label for="stock_transfer" name="{{ __('warehouse.stock_transfer') }}" />
                                                <div class="input-group mb-3">
                                                    <x-input type="text" name="prefix_code" :required="true" placeholder="Prefix Code" value="{{ $transfer->prefix_code }}"/>
                                                    <span class="input-group-text">#</span>
                                                    <x-input type="text" name="count_id" :required="true" placeholder="Serial Number" value="{{ $transfer->count_id }}"/>
                                                </div>
                                            </div>

                                    </div>
                                    <div class="card-header px-4 py-3">
                                        <h5 class="mb-0">{{ __('item.items') }}</h5>
                                    </div>
                                    <div class="card-body p-4 row g-3">
                                            <div class="col-md-9">
                                                <x-label for="search_item" name="{{ __('item.enter_item_name') }}" />
                                                <div class="input-group">
                                                    <span class="input-group-text" id="basic-addon1"><i class="fadeIn animated bx bx-barcode-reader text-primary"></i></span>
                                                    <input type="text" id="search_item" value="" class="form-control" required placeholder="Scan Barcode/Search Items">
                                                </div>
                                            </div>
                                            <div class="col-md-12 table-responsive">
                                                <table class="table mb-0 table-striped table-bordered" id="stockTransferItemsTable">
                                                    <thead>
                                                        <tr class="text-uppercase">
                                                            <th scope="col">{{ __('app.action') }}</th>
                                                            <th scope="col">{{ __('item.item') }}</th>
                                                            <th scope="col" class="{{ !app('company')['enable_serial_tracking'] ? 'd-none':'' }}">{{ __('item.serial') }}</th>
                                                            <th scope="col" class="{{ !app('company')['enable_batch_tracking'] ? 'd-none':'' }}">{{ __('item.batch_no') }}</th>
                                                            <th scope="col" class="{{ !app('company')['enable_mfg_date'] ? 'd-none':'' }}">{{ __('item.mfg_date') }}</th>
                                                            <th scope="col" class="{{ !app('company')['enable_exp_date'] ? 'd-none':'' }}">{{ __('item.exp_date') }}</th>
                                                            <th scope="col" class="{{ !app('company')['enable_model'] ? 'd-none':'' }}">{{ __('item.model_no') }}</th>
                                                            <th scope="col" class="{{ !app('company')['enable_color'] ? 'd-none':'' }}">{{ __('item.color') }}</th>
                                                            <th scope="col" class="{{ !app('company')['enable_size'] ? 'd-none':'' }}">{{ __('item.size') }}</th>
                                                            <th scope="col">{{ __('item.stock') }}</th>
                                                            <th scope="col">{{ __('unit.unit') }}</th>
                                                            <th scope="col">{{ __('warehouse.qty_to_transfer') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td colspan="8" class="text-center fw-light fst-italic default-row">
                                                                No items are added yet!!
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="2" class="fw-bold text-end tfoot-first-td">
                                                                {{ __('app.total') }}
                                                            </td>
                                                            <td class="fw-bold sum_of_quantity">
                                                                0
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                            <div class="col-md-8">
                                                <x-label for="note" name="{{ __('app.note') }}" />
                                                <x-textarea name='note' value='{{ $transfer->note }}'/>
                                            </div>
                                    </div>

                                    <div class="card-header px-4 py-3"></div>
                                    <div class="card-body p-4 row g-3">
                                            <div class="col-md-12">
                                                <div class="d-md-flex d-grid align-items-center gap-3">
                                                    <x-button type="button" class="primary px-4" buttonId="submit_form" text="{{ __('app.submit') }}" />
                                                    <x-anchor-tag href="{{ route('dashboard') }}" text="{{ __('app.close') }}" class="btn btn-light px-4" />
                                                </div>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
                <!--end row-->
            </div>
        </div>
        <!-- Import Modals -->
        @include("modals.service.create")
        @include("modals.item.serial-tracking")
        @include("modals.item.batch-tracking-sale")

        @endsection

@section('js')
<script type="text/javascript">
        const itemsTableRecords = @json($itemTransactionsJson);
        const taxList = JSON.parse('{!! $taxList !!}');
</script>
<script src="{{ versionedAsset('custom/js/stock-transfer/stock-transfer.js') }}"></script>
<script src="{{ versionedAsset('custom/js/items/serial-tracking.js') }}"></script>
<script src="{{ versionedAsset('custom/js/items/serial-tracking-settings.js') }}"></script>
<script src="{{ versionedAsset('custom/js/items/batch-tracking-sale.js') }}"></script>
<script src="{{ versionedAsset('custom/js/common/common.js') }}"></script>
@endsection
