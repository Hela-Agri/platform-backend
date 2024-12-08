<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice-{{$farmActivity->invoice_number}}</title>
    <style>
        @page {
            margin: 5mm 10mm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .logo {
            float: center;
            width: 100px;
        }

        .address-cell {
            border-left: 1px solid #9EC14D;
            height: 20%;
        }

        .address-info {
            padding-top: 10px;
            padding-left: 10px;
            padding-right: 50px;
            width: 100%;
            font-size: 14px;
            float: left;
            text-align: left;
            line-height: 0.9;
            font-size: 12px;
        }

        .invoice-info {
            padding-left: 10px;
            width: 100%;
            font-size: 14px;
            float: left;
            text-align: left;
            line-height: 0.9;
            font-size: 12px;
            padding-top: 15%;


        }

        .divider {
            border-bottom: 1px solid #e0f3d6;
            margin: 20px 5px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            border-top: 1px solid #e0f3d6;
            float: center;
            text-align: center;
            text-transform: uppercase;
            font-size: 12px;
            color: #9EC14D;
        }

        .invoice_title {
            font: 1.3em sans-serif;
            color: #9EC14D;

        }

        .invoice_table {
            margin-top: 20px;
            margin-left: 10px;
        }

        .extra_info_table {
            margin-top: 20px;
            margin-left: 10px;
        }

        .invoice_table th {
            background-color: #bcd385;
            color: rgb(46, 39, 39);
            padding: 10px;
            font: 0.7em sans-serif;
        }

        .invoice_items tr {
            color: rgb(46, 39, 39);
            padding: 20px;
            font: 0.8em sans-serif;
            line-height: 1.7;
        }

        tfoot {
            padding-top: 20px;

        }

        tfoot tr {

            color: rgb(46, 39, 39);
            padding: 20px;
            font-weight: bold;
            font-size: 14px
        }

        .loan_details {
            color: rgb(46, 39, 39);
            font: 0.8em sans-serif;
            line-height: 1;

        }

        .payment_details {
            color: rgb(46, 39, 39);

            font: 0.8em sans-serif;
            line-height: 1;
        }

        .page-break {
            page-break-after: always;
        }

        .table-header {
            margin: 0 0 0 10px;
        }
    </style>
</head>

<body>

    <table width="100%">
        <tbody>
            <tr>
                <td width="80%">
                    <div class="invoice-info">
                        <p class="invoice_title">INVOICE</p>

                        <p>Invoice To: {{$farmActivity->farmer->first_name ?? ''}}
                            {{$farmActivity->farmer->middle_name ?? ''}} {{$farmActivity->farmer->last_name ?? ''}}
                        </p>
                        <p>ID No: {{$farmActivity->farmer->registration_number ?? ''}}</p>
                        <p>Phone No: {{$farmActivity->farmer->phone_number ?? ''}}</p>
                        <p>Invoice No: {{$farmActivity->invoice_number ?? ''}}</p>
                        <p>Invoice Date: {{\Carbon\carbon::parse($loan->approval_date)->format('d M, Y')}}</p>

                    </div>
                </td>
                <td width="20%">
                    <table>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="address-cell">
                                    </div>
                                </td>
                                <td>

                                    <div class="address-info">
                                        <div>
                                            <img src="data:image/png;base64,{{ $logo }}" width="100px" alt="Logo" />
                                        </div>
                                        <div>
                                            <div class="divider"></div>
                                            <p>{{$setting->address}}</p>
                                            <p>Email: {{$setting->email}}</p>
                                            <p>Phone: {{$setting->phone}}</p>
                                            <p>KRA PIN: {{$setting->kra_pin}}</p>

                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="table-header">
        <h4>Inputs</h4>
    </div>
    <table width="100%" class="invoice_table">
        <thead>
            <tr>
                <th style="text-align: left;" width="15%">Date</th>
                <th style="text-align: left;" width="22%">Item</th>
                <th style="text-align: left;" width="23%">Category</th>
                <th style="text-align: right;" width="10%">Qnty</th>
                <th style="text-align: right;" width="10%">Cost(KES)</th>
                <th style="text-align: right;" width="10%">Total Loan</th>
            </tr>
        </thead>
        <tbody class="invoice_items">
            @php
                $total_amount = 0;
                $interest_total = 0;
                $item_total = 0;
                $date = '';
            @endphp
            @foreach ($farmActivity->activityItems as $idx => $item)
                        @php
                            $item_total = $item->rateCard->amount * $item->quantity;

                            $total_amount += $item_total;

                            if ($farmActivity->activityItems->count() === $idx + 1) {

                                if ($farmActivity->package->rate_type == 'percentage') {
                                    $interest_total = $total_amount * ($farmActivity->package->interest_rate / 100);
                                } else {
                                    $interest_total = $farmActivity->package->interest_rate;

                                }
                            }


                        @endphp
                        <tr>
                            <td style="text-align: left;">
                                @if($date != $item->date)
                                    {{\Carbon\Carbon::parse($item->date)->format('d-m-Y')}}
                                @endif
                            </td>
                            <td style="text-align: left;">{{$item->rateCard->name}}</td>
                            <td style="text-align: left;">{{$item->rateCard->product->category->name ?? ''}}</td>
                            <td style="text-align: right;">{{number_format($item->quantity, 2)}}
                                {{$item->rateCard->product->unit->name ?? ''}}
                            </td>
                            <td style="text-align: right;">{{number_format($item->rateCard->amount, 2)}}</td>
                            <td style="text-align: right;">{{number_format($item_total, 2)}}</td>
                        </tr>
                        @php
                            $date = $item->date;
                        @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td style="text-align: left;" colspan="5">Sub Total</td>
                <td style="text-align: right;">{{number_format($total_amount, 2)}}</td>
            </tr>
            <tr>
                <td style="text-align: left;" colspan="5">Interest
                    @if($farmActivity->package->rate_type == 'percentage')
                        ({{$farmActivity->package->interest_rate}}%)
                    @else
                        ({{$farmActivity->package->interest_rate}})
                    @endif

                </td>
                <td style="text-align: right;">{{number_format($interest_total, 2)}}</td>
            </tr>
            <tr>
                <td style="text-align: left;" colspan="5">Processing Fee</td>
                <td style="text-align: right;">{{number_format($farmActivity->package->processing_fee ?? 0, 2)}}
                </td>
            </tr>
            <tr>
                <td style="text-align: left;" colspan="5">Total Loaned Inputs</td>
                <td style="text-align: right;">
                    {{number_format($farmActivity->package->processing_fee + $total_amount + $interest_total, 2)}}
                </td>
            </tr>
            @php
                $paid_amount = 0;
            @endphp
            @if($loan->loanPayments)
                        @php
                            $paid_amount = $loan->loanPayments->sum('amount')
                        @endphp
                        <tr>
                            <td style="text-align: left;" colspan="5">Paid</td>
                            <td style="text-align: right;">
                                {{number_format($paid_amount, 2)}}
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: left;" colspan="5">Balance</td>
                            <td style="text-align: right;">
                                {{number_format($farmActivity->package->processing_fee + $total_amount + $interest_total - $paid_amount, 2)}}
                            </td>
                        </tr>
            @endif
        </tfoot>

    </table>
    <div class="table-header">
        <h4>Payments</h4>
    </div>
    @if($loan->loanPayments)

        <table width="100%" class="invoice_table">
            <thead>
                <tr>
                    <th style="text-align: left;" width="34%">Date</th>
                    <th style="text-align: right;" width="33%">Reference</th>
                    <th style="text-align: right;" width="33%">Amount(KES)</th>
                </tr>
            </thead>
            <tbody class="invoice_items">

                @foreach ($loan->loanPayments as $idx => $loanPayment)
                    <tr>
                        <td style="text-align: left;">
                            {{\Carbon\Carbon::parse($loanPayment->payment->transaction_date)->format('d-m-Y')}}
                        </td>
                        <td style="text-align: right;">{{$loanPayment->payment->transaction_reference}}</td>
                        <td style="text-align: right;">{{number_format($loanPayment->amount, 2)}}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td style="text-align: left;" colspan="2">Total Paid </td>
                    <td style="text-align: right;">{{number_format($paid_amount, 2)}} </td>
                </tr>
            </tfoot>
        </table>
    @endif

    <div class="table-header">
        <h4>Yields</h4>
    </div>

    @if($farmActivity->harvests)
        <table width="100%" class="invoice_table">
            <thead>
                <tr>
                    <th style="text-align: left;" width="10%">Date</th>
                    <th style="text-align: right;" width="33%">Weight</th>
                </tr>
            </thead>
            <tbody class="invoice_items">
                @php
                    $total_weight = 0;
                    $date = '';
                @endphp
                @foreach ($farmActivity->harvests as $idx => $harvest)
                        @php
                            $total_weight += $harvest->weight;
                        @endphp
                        <tr>
                            <td style="text-align: left;">
                                @if($date != $harvest->harvest_date)
                                    {{\Carbon\Carbon::parse($harvest->harvest_date)->format('d-m-Y')}}
                                @endif

                            </td>
                            <td style="text-align: right;">{{$harvest->weight}} {{$harvest->unit->name ?? ''}}</td>

                        </tr>
                        @php
                            $date = $harvest->harvest_date;
                        @endphp
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td style="text-align: left;" colspan="1">Total weight </td>
                    <td style="text-align: right;">{{number_format($total_weight, 2)}} (KGS)</td>
                </tr>
            </tfoot>
        </table>
    @endif

    <table width="100%" class="extra_info_table">
        <tbody>
            <tr>
                <td>
                    <div class="loan_details">
                        <p>{!!$setting->invoice_note!!}</p>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="payment_details">
                        <p>{!!$setting->payment_note!!}</p>
                    </div>
                </td>
            </tr>

        </tbody>
    </table>



    </div>
    <div class="footer">
        <p>{{$setting->slogan}}</p>
    </div>
</body>

</html>