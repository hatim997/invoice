@extends('layouts.master')

@section('title', __('Edit Invoice'))

@section('css')
@endsection


@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.invoices.index') }}">{{ __('Invoices') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Edit') }}</li>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-6">
            <!-- Account -->
            <div class="card-body pt-4">
                <form method="POST" action="{{ route('dashboard.invoices.update', $invoice->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row p-5">
                        <h3>{{ __('Edit Invoice') }}</h3>
                        <h6 class="text-muted" style="font-style: italic;">Invoice Agency Info</h6>
                        <hr>
                        <div class="mb-4 col-md-6">
                            <label for="agency_name" class="form-label">{{ __('Agency Name') }}</label><span
                                class="text-danger">*</span>
                            <input class="form-control @error('agency_name') is-invalid @enderror" type="text"
                                id="agency_name" name="agency_name" required
                                placeholder="{{ __('Enter invoice agency name') }}" autofocus
                                value="{{ old('agency_name', $invoice->agency_name) }}" />
                            @error('agency_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-4 col-md-6">
                            <label for="agency_licence_number"
                                class="form-label">{{ __('Agency licence Number') }}</label><span
                                class="text-danger">*</span>
                            <input class="form-control @error('agency_licence_number') is-invalid @enderror" type="text"
                                id="agency_licence_number" name="agency_licence_number" required
                                placeholder="{{ __('Enter agency licence number') }}"
                                value="{{ old('agency_licence_number', $invoice->agency_licence_number) }}" />
                            @error('agency_licence_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-4 col-md-12">
                            <div class="d-flex justify-content-between align-items-baseline mb-3">
                                <label class="form-label">{{ __('Agency Categories') }}<span
                                        class="text-danger">*</span></label>
                                <button type="button" id="addMorecategory"
                                    class="btn btn-primary btn-sm">{{ __('Add More') }}</button>
                            </div>

                            <div id="category-wrapper">
                                @if (isset($invoice->invoiceAgencyCategories) && count($invoice->invoiceAgencyCategories) > 0)
                                    @foreach ($invoice->invoiceAgencyCategories as $category)
                                        <div class="row item-row mb-2">
                                            <div class="col-md-5">
                                                <input class="form-control" type="text" name="category_name_english[]"
                                                    placeholder="{{ __('Enter category name in english') }}" value="{{ $category->category_name_english }}">
                                            </div>
                                            <div class="col-md-5">
                                                <input class="form-control" type="text" name="category_name_arabic[]"
                                                    placeholder="{{ __('Enter category name in arabic') }}" value="{{ $category->category_name_arabic }}">
                                            </div>
                                            <div class="col-md-2 d-flex align-items-center">
                                                <button type="button"
                                                    class="btn btn-danger btn-sm remove-button">Remove</button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="row item-row mb-2">
                                        <div class="col-md-5">
                                            <input class="form-control" type="text" name="category_name_english[]"
                                                placeholder="{{ __('Enter category name in english') }}">
                                        </div>
                                        <div class="col-md-5">
                                            <input class="form-control" type="text" name="category_name_arabic[]"
                                                placeholder="{{ __('Enter category name in arabic') }}">
                                        </div>
                                        <div class="col-md-2 d-flex align-items-center">
                                            <button type="button"
                                                class="btn btn-danger btn-sm remove-button d-none">Remove</button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <h6 class="text-muted" style="font-style: italic;">Invoice Basic Info</h6>
                        <hr>

                        <div class="mb-4 col-md-4">
                            <label for="invoice_number" class="form-label">{{ __('Invoice Number') }}</label><span
                                class="text-danger">*</span>
                            <input class="form-control @error('invoice_number') is-invalid @enderror" type="text"
                                id="invoice_number" name="invoice_number" required
                                placeholder="{{ __('Enter invoice number') }}" autofocus
                                value="{{ old('invoice_number', $invoice->invoice_number) }}" />
                            @error('invoice_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-4 col-md-4">
                            <label for="invoice_date" class="form-label">{{ __('Invoice Date') }}</label><span
                                class="text-danger">*</span>
                            <input class="form-control @error('invoice_date') is-invalid @enderror" type="date"
                                id="invoice_date" name="invoice_date" required
                                placeholder="{{ __('Enter invoice number') }}" autofocus
                                value="{{ old('invoice_date', $invoice->invoice_date) }}" />
                            @error('invoice_date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-4 col-md-4">
                            <label for="vat_reg_no" class="form-label">{{ __('VAT Reg. No.') }}</label><span
                                class="text-danger">*</span>
                            <input class="form-control @error('vat_reg_no') is-invalid @enderror" type="text"
                                id="vat_reg_no" name="vat_reg_no" required
                                placeholder="{{ __('Enter VAT registration number') }}" autofocus
                                value="{{ old('vat_reg_no', $invoice->vat_reg_no) }}" />
                            @error('vat_reg_no')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <h6 class="text-muted" style="font-style: italic;">Invoice Details</h6>
                        <hr>

                        <div class="mb-4 col-md-12">
                            <label for="pax_name" class="form-label">{{ __('Pax Name') }}</label><span
                                class="text-danger">*</span>
                            <input class="form-control @error('pax_name') is-invalid @enderror" type="text"
                                id="pax_name" name="pax_name" required placeholder="{{ __('Enter pax name') }}"
                                value="{{ old('pax_name', $invoice->invoiceDetails->first()->pax_name) }}" />
                            @error('pax_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4 col-md-12">
                            <label for="service_description"
                                class="form-label">{{ __('Service Description') }}</label><span
                                class="text-danger">*</span>
                            <textarea class="form-control @error('service_description') is-invalid @enderror" type="text"
                                id="service_description" name="service_description" required
                                placeholder="{{ __('Enter service description') }}">{{ old('service_description', $invoice->invoiceDetails->first()->service_description) }}</textarea>
                            @error('service_description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4 col-md-6">
                            <label for="unit_price" class="form-label">{{ __('Unit Price') }}</label><span
                                class="text-danger">*</span>
                            <input class="form-control @error('unit_price') is-invalid @enderror" step="any"
                                type="number" id="unit_price" name="unit_price" required
                                placeholder="{{ __('Enter unit price') }}" autofocus value="{{ old('unit_price', $invoice->invoiceDetails->first()->unit_price) }}" />
                            @error('unit_price')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4 col-md-6">
                            <label for="taxable_amount" class="form-label">{{ __('Taxable Amount') }}</label><span
                                class="text-danger">*</span>
                            <input class="form-control @error('taxable_amount') is-invalid @enderror" step="any"
                                type="number" id="taxable_amount" name="taxable_amount" required
                                placeholder="{{ __('Enter taxable amount') }}" autofocus
                                value="{{ old('taxable_amount', $invoice->invoiceDetails->first()->taxable_amount) }}" />
                            @error('taxable_amount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4 col-md-6">
                            <label for="tax_rate" class="form-label">{{ __('Tax Rate (%)') }}</label><span
                                class="text-danger">*</span>
                            <input class="form-control @error('tax_rate') is-invalid @enderror" step="any"
                                type="number" id="tax_rate" name="tax_rate" required
                                placeholder="{{ __('Enter tax rate') }}" autofocus
                                value="{{ old('tax_rate', $invoice->invoiceDetails->first()->tax_rate) }}" />
                            @error('tax_rate')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4 col-md-6">
                            <label for="tax_amount" class="form-label">{{ __('Tax Amount') }}</label><span
                                class="text-danger">*</span>
                            <input class="form-control @error('tax_amount') is-invalid @enderror" step="any"
                                type="number" id="tax_amount" name="tax_amount" required
                                placeholder="{{ __('Enter tax amount') }}" autofocus
                                value="{{ old('tax_amount', $invoice->invoiceDetails->first()->tax_amount) }}" />
                            @error('tax_amount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4 col-md-6">
                            <label for="total" class="form-label">{{ __('Total') }}</label><span
                                class="text-danger">*</span>
                            <input class="form-control @error('total') is-invalid @enderror" step="any"
                                type="number" id="total" name="total" required
                                placeholder="{{ __('Enter total') }}" autofocus value="{{ old('total', $invoice->invoiceDetails->first()->total) }}" />
                            @error('total')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                    </div>
                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-3">{{ __('Edit Invoice') }}</button>
                    </div>
                </form>
            </div>
            <!-- /Account -->
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#addMorecategory').click(function() {
                const html = `
                <div class="row item-row mb-2">
                    <div class="col-md-5">
                        <input class="form-control" type="text" name="category_name_english[]" placeholder="Enter category name in english">
                    </div>
                    <div class="col-md-5">
                        <input class="form-control" type="text" name="category_name_arabic[]" placeholder="Enter category name in arabic">
                    </div>
                    <div class="col-md-2 d-flex align-items-center">
                        <button type="button" class="btn btn-danger btn-sm remove-button">Remove</button>
                    </div>
                </div>
            `;
                $('#category-wrapper').append(html);
                updateRemoveButtons();
            });

            $(document).on('click', '.remove-button', function() {
                $(this).closest('.item-row').remove();
                updateRemoveButtons();
            });

            function updateRemoveButtons() {
                const rows = $('.item-row');
                if (rows.length > 1) {
                    rows.find('.remove-button').removeClass('d-none');
                } else {
                    rows.find('.remove-button').addClass('d-none');
                }
            }

            updateRemoveButtons(); // Call on load

            // function calculateTotal() {
            //     let unitPrice = parseFloat($('#unit_price').val()) || 0;
            //     let taxableAmount = parseFloat($('#taxable_amount').val()) || 0;
            //     let taxRate = parseFloat($('#tax_rate').val()) || 0;

            //     // Calculate tax amount
            //     let taxAmount = taxableAmount * (taxRate / 100);
            //     $('#tax_amount').val(taxAmount.toFixed(2));

            //     // Calculate total: unit_price + tax
            //     let total = unitPrice + taxAmount;
            //     $('#total').val(total.toFixed(2));
            // }

            // $('#unit_price, #taxable_amount, #tax_rate').on('input', calculateTotal);

            // // Initialize total on load
            // calculateTotal();
        });
    </script>
@endsection
