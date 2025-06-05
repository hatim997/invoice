<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceAgencyCategory;
use App\Models\InvoiceDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view invoice');
        try {
            $invoices = Invoice::get();
            return view('dashboard.invoice.index', compact('invoices'));
        } catch (\Throwable $th) {
            Log::error('Invoice Index Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create invoice');
        try {
            $lastInvoice = Invoice::latest('id')->first();
            // Calculate new invoice number
            $newId = $lastInvoice ? $lastInvoice->id + 1 : 1;
            $invoiceNumber = 'INV' . str_pad($newId, 6, '0', STR_PAD_LEFT);
            return view('dashboard.invoice.create', compact('invoiceNumber'));
        } catch (\Throwable $th) {
            Log::error('Invoice Create Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create invoice');
        $validator = Validator::make($request->all(), [
            'agency_name' => 'required|string|max:255',
            'agency_licence_number' => 'required|string|max:255',
            'category_name_english' => 'required|array',
            'category_name_english.*' => 'string|max:255',
            'category_name_arabic' => 'required|array',
            'category_name_arabic.*' => 'string|max:255',
            'invoice_number' => 'required|string|max:255',
            'invoice_date' => 'required|date',
            'vat_reg_no' => 'required|string|max:255',
            'pax_name' => 'required|string|max:255',
            'service_description' => 'required|string|max:255',
            'unit_price' => 'required|numeric',
            'taxable_amount' => 'required|numeric',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'tax_amount' => 'required|numeric',
            'total' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            Log::error('Invoice Store Validation Failed', ['error' => $validator->errors()->all()]);
            return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error', 'Validation Error!');
        }

        try {
            DB::beginTransaction();

            $invoice = new Invoice();
            $invoice->agency_name = $request->agency_name;
            $invoice->agency_licence_number = $request->agency_licence_number;
            $invoice->invoice_number = $request->invoice_number;
            $invoice->invoice_date = $request->invoice_date;
            if(isset($request->invoice_date)){
                $hijri_date = $this->convertToHijriDate($request->invoice_date);
                $invoice->invoice_hijri_date = $hijri_date;
            }
            $invoice->vat_reg_no = $request->vat_reg_no;
            $invoice->save();

            if(isset($request->category_name_english) && count($request->category_name_english) > 0){
                foreach($request->category_name_english as $key => $category){
                    $invoiceAgencyCategory = new InvoiceAgencyCategory();
                    $invoiceAgencyCategory->invoice_id = $invoice->id;
                    $invoiceAgencyCategory->category_name_english = $category;
                    $invoiceAgencyCategory->category_name_arabic = $request->category_name_arabic[$key];
                    $invoiceAgencyCategory->save();
                }
            }else{
                DB::rollBack();
                return redirect()->back()->with('error', "Something went wrong! Please try again later");
            }

            $invoiceDetail = new InvoiceDetail();
            $invoiceDetail->invoice_id = $invoice->id;
            $invoiceDetail->pax_name = $request->pax_name;
            $invoiceDetail->service_description = $request->service_description;
            $invoiceDetail->unit_price = $request->unit_price;
            $invoiceDetail->taxable_amount = $request->taxable_amount;
            $invoiceDetail->tax_rate = $request->tax_rate;
            $invoiceDetail->tax_amount = $request->tax_amount;
            $invoiceDetail->total = $request->total;
            $invoiceDetail->save();

            DB::commit();
            return redirect()->route('dashboard.invoices.index')->with('success', 'Invoice Created Successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Invoice Store Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->authorize('view invoice');
        try {
            $invoice = Invoice::withSum('invoiceDetails', 'total')   // adds a SUM() sub-query
                  ->with(['invoiceDetails', 'invoiceAgencyCategories'])
                  ->findOrFail($id);

            // Access the column Laravel adds:
            $grandTotal = $invoice->invoice_details_sum_total;
            $words_amount =$this->convertNumberToWord($grandTotal);
            $arabic_words_amount = $this->convertNumberToArabicWord($grandTotal); // Arabic
            return view('dashboard.invoice.show', compact('invoice', 'words_amount', 'arabic_words_amount'));
        } catch (\Throwable $th) {
            Log::error('Invoice Show Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->authorize('update invoice');
        try {
            $invoice = Invoice::with(['invoiceDetails', 'invoiceAgencyCategories'])
                  ->findOrFail($id);
            return view('dashboard.invoice.edit', compact('invoice'));
        } catch (\Throwable $th) {
            Log::error('Invoice Edit Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->authorize('update invoice');
        $validator = Validator::make($request->all(), [
            'agency_name' => 'required|string|max:255',
            'agency_licence_number' => 'required|string|max:255',
            'category_name_english' => 'required|array',
            'category_name_english.*' => 'string|max:255',
            'category_name_arabic' => 'required|array',
            'category_name_arabic.*' => 'string|max:255',
            'invoice_number' => 'required|string|max:255',
            'invoice_date' => 'required|date',
            'vat_reg_no' => 'required|string|max:255',
            'pax_name' => 'required|string|max:255',
            'service_description' => 'required|string|max:255',
            'unit_price' => 'required|numeric',
            'taxable_amount' => 'required|numeric',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'tax_amount' => 'required|numeric',
            'total' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            Log::error('Invoice Store Validation Failed', ['error' => $validator->errors()->all()]);
            return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error', 'Validation Error!');
        }

        try {
            DB::beginTransaction();

            $invoice = Invoice::findOrFail($id);
            $invoice->agency_name = $request->agency_name;
            $invoice->agency_licence_number = $request->agency_licence_number;
            $invoice->invoice_number = $request->invoice_number;
            $invoice->invoice_date = $request->invoice_date;
            if(isset($request->invoice_date)){
                $hijri_date = $this->convertToHijriDate($request->invoice_date);
                $invoice->invoice_hijri_date = $hijri_date;
            }
            $invoice->vat_reg_no = $request->vat_reg_no;
            $invoice->save();

            if(isset($request->category_name_english) && count($request->category_name_english) > 0){
                InvoiceAgencyCategory::where('invoice_id', $invoice->id)->delete();
                foreach($request->category_name_english as $key => $category){
                    $invoiceAgencyCategory = new InvoiceAgencyCategory();
                    $invoiceAgencyCategory->invoice_id = $invoice->id;
                    $invoiceAgencyCategory->category_name_english = $category;
                    $invoiceAgencyCategory->category_name_arabic = $request->category_name_arabic[$key];
                    $invoiceAgencyCategory->save();
                }
            }else{
                DB::rollBack();
                return redirect()->back()->with('error', "Something went wrong! Please try again later");
            }

            $invoiceDetail = InvoiceDetail::where('invoice_id', $invoice->id)->first();
            $invoiceDetail->invoice_id = $invoice->id;
            $invoiceDetail->pax_name = $request->pax_name;
            $invoiceDetail->service_description = $request->service_description;
            $invoiceDetail->unit_price = $request->unit_price;
            $invoiceDetail->taxable_amount = $request->taxable_amount;
            $invoiceDetail->tax_rate = $request->tax_rate;
            $invoiceDetail->tax_amount = $request->tax_amount;
            $invoiceDetail->total = $request->total;
            $invoiceDetail->save();

            DB::commit();
            return redirect()->route('dashboard.invoices.index')->with('success', 'Invoice Updated Successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Invoice Updated Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('delete invoice');
        try {
            $invoice = Invoice::findOrFail($id);
            $invoice->delete();
            return redirect()->back()->with('success', 'Invoice Deleted Successfully');
        } catch (\Throwable $th) {
            Log::error('Invoice Delete Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
            throw $th;
        }
    }

    function convertNumberToWord(float $number)
    {
        $decimal = round($number - ($no = floor($number)), 2) * 100;
        $hundred = null;
        $digits_length = strlen($no);
        $i = 0;
        $str = array();
        $words = array(0 => '', 1 => 'one', 2 => 'two',
            3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
            7 => 'seven', 8 => 'eight', 9 => 'nine',
            10 => 'ten', 11 => 'eleven', 12 => 'twelve',
            13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
            16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
            19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
            40 => 'forty', 50 => 'fifty', 60 => 'sixty',
            70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
        $digits = array('', 'hundred','thousand','lakh', 'crore');
        while( $i < $digits_length ) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += $divider == 10 ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
            } else $str[] = null;
        }
        $Rupees = implode('', array_reverse($str));
        $paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
        return ($Rupees ? $Rupees . 'Rupees ' : '') . $paise;
    }

    public function convertNumberToArabicWord(float $number): string
    {
        $ones = [
            "", "واحد", "اثنان", "ثلاثة", "أربعة", "خمسة",
            "ستة", "سبعة", "ثمانية", "تسعة", "عشرة",
            "أحد عشر", "اثنا عشر", "ثلاثة عشر", "أربعة عشر", "خمسة عشر",
            "ستة عشر", "سبعة عشر", "ثمانية عشر", "تسعة عشر"
        ];

        $tens = [
            "", "", "عشرون", "ثلاثون", "أربعون", "خمسون",
            "ستون", "سبعون", "ثمانون", "تسعون"
        ];

        $hundreds = [
            "", "مائة", "مائتان", "ثلاثمائة", "أربعمائة", "خمسمائة",
            "ستمائة", "سبعمائة", "ثمانمائة", "تسعمائة"
        ];

        $scales = [
            "", "ألف", "مليون", "مليار"
        ];

        if ($number == 0) {
            return "صفر ريال سعودي فقط";
        }

        $integerPart = floor($number);
        $fractionPart = round(($number - $integerPart) * 100);

        $parts = [];
        $scaleIndex = 0;

        while ($integerPart > 0) {
            $chunk = $integerPart % 1000;

            if ($chunk > 0) {
                $words = [];

                $hundredDigit = floor($chunk / 100);
                $tenUnit = $chunk % 100;

                if ($hundredDigit) {
                    $words[] = $hundreds[$hundredDigit];
                }

                if ($tenUnit > 0) {
                    if ($tenUnit < 20) {
                        $words[] = $ones[$tenUnit];
                    } else {
                        $words[] = $ones[$tenUnit % 10] . " و " . $tens[floor($tenUnit / 10)];
                    }
                }

                $phrase = implode(" و ", array_filter($words));
                if ($scaleIndex > 0) {
                    $phrase .= " " . $scales[$scaleIndex];
                }

                $parts[] = $phrase;
            }

            $integerPart = floor($integerPart / 1000);
            $scaleIndex++;
        }

        $wordsPart = implode(" و ", array_reverse($parts));
        $wordsPart .= " ريال سعودي";

        if ($fractionPart > 0) {
            $wordsPart .= " و " . $this->convertNumberToArabicWord($fractionPart) . " هللة";
        }

        $wordsPart .= " فقط";

        return $wordsPart;
    }

    public function convertToHijriDate($gregorianDate)
    {
        $date = new \DateTime($gregorianDate);

        $formatter = new \IntlDateFormatter(
            'ar_SA@calendar=islamic',
            \IntlDateFormatter::LONG,
            \IntlDateFormatter::NONE,
            'Asia/Riyadh', // or your preferred timezone
            \IntlDateFormatter::TRADITIONAL,
            "d MMMM y"
        );

        return $formatter->format($date); // Example: 7 شعبان 1445
    }
}
