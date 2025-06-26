<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Tax Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            /* Use pt for print consistency */
            margin: 0;
            padding: 10mm;
            /* Standard A4 margin */
            direction: ltr;
            box-sizing: border-box;
        }

        /* A4 page setup for print */
        @media print {
            @page {
                size: A4;
                margin: 15mm;
                /* Consistent margins for A4 */
            }

            body {
                margin: 0;
                padding: 0;
                width: 210mm;
                /* A4 width */
                height: 297mm;
                /* A4 height */
                font-size: 11pt;
                /* Slightly smaller for print */
            }

            table {
                page-break-inside: avoid;
                /* Prevent table breaking across pages */
            }

            .details-box,
            .invoice-data-table,
            .summary-box {
                width: 100%;
                max-width: 210mm;
                /* Fit within A4 with margins */
            }

            .invoice-data-table {
                height: 100vh;
                max-height: 150mm;
            }

            .invoice-data-table td {
                vertical-align: top;
            }

            .invoice-data-table th {
                background-color: #f0f0f0;
            }
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10mm;
        }

        th,
        td {
            border: none;
            border-left: 1px solid #000;
            padding: 5mm;
            text-align: center;
            vertical-align: middle;
        }

        th {
            border: 1px solid #000;
            background-color: #f0f0f0;
        }

        table {
            border-bottom: 1px solid #000;
            border-right: 1px solid #000;
        }

        .no-border {
            border: none;
        }

        .center {
            text-align: center;
        }

        .left {
            text-align: left;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .invoice-title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            margin: 5mm 0;
        }

        .arabic-title {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 5mm;
        }

        .section {
            margin-bottom: 5mm;
        }

        .details-box td {
            border: none;
            padding: 3mm 5mm;
            text-align: left;
        }

        .summary-box td {
            border: none;
            padding: 5mm;
        }

        .summary-box tr {
            border: 1px solid #000;
        }

        .footer {
            margin-top: 10mm;
            font-weight: bold;
            font-size: 10pt;
        }

        /* Ensure text wrapping in table cells */
        .invoice-data-table td,
        .invoice-data-table th {
            word-wrap: break-word;
            max-width: 50mm;
            /* Prevent columns from becoming too wide */
        }

        /* Flexbox cleanup for details box */
        .details-box .flex-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2mm;
        }

        .invoice-data-table th,
        .invoice-data-table td {
            width: 11.98%;
            /* Default width for all columns except description */
            word-wrap: break-word;
            max-width: 50mm;
            /* Prevent columns from becoming too wide */
        }

        .invoice-data-table th.description-column,
        .invoice-data-table td.description-column {
            width: 16.17%;
            /* 35% wider than other columns */
        }
    </style>
</head>

<body>
    <div class="header" style="display: flex; justify-content: space-between; align-items: center;">
        <div class="left">
            Riyadh Kingdom of Saudi Arabia - 11564
        </div>
        <div class="right" style="display: flex; align-items: center; gap: 10px; font-size: 18px;">
            <span>Rihlat alSama</span>
            <img style="height: 60px;" src="{{ asset('uploads/skytrip-logo.png') }}" alt="{{ env('APP_NAME') }}">
            <span>وكالة رحلات السماء للسفر و السياحة</span>
        </div>
    </div>

    <div class="invoice-title">TAX INVOICE</div>
    <div class="center arabic-title">فاتورة ضريبية مبسطة</div>

    <table class="details-box" style="border: 1px solid #000;">
        <tr>
            <td class="bold">{{ $invoice->agency_name }}</td>
            <td></td>
        </tr>
        <tr>
            <td>
                <div class="flex-row">
                    <span>Tourism license number / رقم ترخيص السياحة</span>
                    <span>: {{ $invoice->agency_licence_number }}</span>
                </div>
                <div class="flex-row">
                    <span>Category / الفئة</span>
                    <span></span>
                </div>
                @if (isset($invoice->invoiceAgencyCategories) && count($invoice->invoiceAgencyCategories) > 0)
                    @foreach ($invoice->invoiceAgencyCategories as $category)
                        <div class="flex-row">
                            <span>{{ $category->category_name_english }}</span>
                            <span>: {{ $category->category_name_arabic }}</span>
                        </div>
                    @endforeach
                @endif
            </td>
            <td>
                <div class="flex-row">
                    <span>Invoice No / رقم الفاتورة</span>
                    <span>: {{ $invoice->invoice_number }}</span>
                </div>
                <div class="flex-row">
                    <span>Invoice Date / تاريخ الفاتورة</span>
                    <span>: {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d F, Y') }}</span>
                </div>
                <div class="flex-row">
                    <span>Hijri Date / التاريخ الهجري</span>
                    <span>: {{ $invoice->invoice_hijri_date }}</span>
                </div>
                <div class="flex-row">
                    <span>VAT Reg. No / رقم ضريبي</span>
                    <span>: {{ $invoice->vat_reg_no }}</span>
                </div>
            </td>
        </tr>
    </table>

    <table class="invoice-data-table">
        <thead>
            <tr>
                <th>Sl No</th>
                <th>Pax Name<br>اسم الركاب</th>
                <th class="description-column">Service Description<br>وصف الخدمة</th>
                <th>Unit Price<br>سعر الوحدة</th>
                <th>Taxable Amount<br>المبلغ الخاضع للضريبة</th>
                <th>TAX Rate<br>معدل الضريبة</th>
                <th>TAX Amount<br>قيمة الضريبة</th>
                <th>Total<br>المجموع</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($invoice->invoiceDetails) && count($invoice->invoiceDetails) > 0)
                @foreach ($invoice->invoiceDetails as $index => $detail)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $detail->pax_name }}</td>
                        <td class="description-column">
                            {{ $detail->service_description }}
                        </td>
                        <td>{{ number_format($detail->unit_price, 2) }}</td>
                        <td>{{ number_format($detail->taxable_amount, 2) }}</td>
                        <td>{{ $detail->tax_rate }}%</td>
                        <td>{{ number_format($detail->tax_amount, 2) }}</td>
                        <td>{{ number_format($detail->total, 2) }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <table class="summary-box">
        <tr>
            <td class="bold" style="width: 60%;">
                SAUDI RIYAL {{ strtoupper($words_amount) }} Only<br>
                {{ $arabic_words_amount }}
            </td>
            <td style="width: 40%; text-align: right;">
                <div class="flex-row">
                    <span>Sub Total / الضريبة قبل المبلغ</span>
                    <span>: {{ number_format($invoice->invoice_details_sum_total, 2) }}</span>
                </div>
                <div class="flex-row">
                    <span>VAT / المضافة القيمة ضريبة</span>
                    <span>: {{ number_format($invoice->vat, 2) }}</span>
                </div>
                <div class="flex-row">
                    <span>Total / الإجمالي شامل الضريبة</span>
                    @php
                        $grandTotal = $invoice->invoice_details_sum_total + $invoice->vat;
                    @endphp
                    <span>: {{ number_format($grandTotal, 2) }}</span>
                </div>
                <div class="flex-row">
                    <img style="height: 60px;" src="{{ asset('uploads/frame.png') }}" alt="{{ env('APP_NAME') }}">
                </div>
            </td>
        </tr>
    </table>

    <div class="footer center">
        This is a Computer Generated Document, Does not Require Signature.
    </div>


    @if (request('print') == 'true')
        <script>
            window.onload = function() {
                window.print();
            };
        </script>
    @endif
</body>

</html>
