<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice - #{{ $data->ref }}</title>

    <style type="text/css">
        @page {
            margin: 0px;
        }

        body {
            margin: 0px;
            font-size: 11pt;
        }

        * {
            font-family: Arial, sans-serif;
        }

        a {
            color: #fff;
            text-decoration: none;
        }

        .invoice, .information, .header {
            padding: 10px;
        }
        /* .information {
            color: #FFF;
        } */
        .information {
            padding: 10px;
        }

        .information .logo {
            margin: 5px;
        }

        .information table {
            padding: 10px;
        }

        .invoice table {
            border-collapse: collapse;
            width: 100%;
        }

        .invoice table td {
            padding : 10px;
        }

        .invoice table th {
            padding: 18px 16px;
            border-top: 1px solid #31353B;
            border-bottom: 1px solid #31353B;
        }

        .invoice table tbody td {
            border-bottom: 1px solid #e5e7e9;
        }

        .invoice table tfoot td {
            border-top: 1px solid #e5e7e9;
        }

        .invoice table tfoot td.noval {
            border-top: none;
        }


    </style>

</head>

<body>
    <div class="page">
        <div class="header">
            <table width="100%">
                <tr>
                    <td align="left" style="width: 40%;">
                        <img src="{{ public_path("uploads/app/logo.svg") }}" width="130px">
                    </td>
                    <td align="right" style="width: 40%;">
                        <h2 style="margin-bottom:0px">INVOICE</h2>
                        <span>{{ $data->ref }}</span>
                    </pre>
                    </td>
                </tr>
            </table>
        </div>
        <div class="information">
            <table width="100%">
                <tr>
                    <td align="left" style="width: 40%">
                        <table width="100%" style="padding: 0">
                            <tr>
                                <td colspan="2">
                                    <h3 style="margin: 0">Customer</h3>
                                </td>
                            </tr>
                            <tr>
                                <td>Name</td>
                                <td>: {{ $data->customer->name }}</td>
                            </tr>
                            <tr>
                                <td>Order Date</td>
                                <td>: {{ \Carbon\Carbon::parse($data->date)->format('d F Y') }}</td>
                            </tr>
                            @if ($data->delivery)
                            <tr>
                                <td>Address</td>
                                <td>
                                    <b>{{ $data->delivery->reciever }}</b><br>
                                    {{ $data->delivery->phone }}<br>
                                    {{ $data->delivery->address }}<br>
                                    {{ $data->delivery->area_text }}, {{ $data->delivery->postal_code }}
                                </td>
                            </tr>
                            @endif
                        </table>
                    </td>
                    <td align="right" style="width: 40%">
    
                    </td>
                </tr>
            </table>
        </div>
        <br />
        <div class="invoice">
            <table width="100%">
                <thead>
                    <tr>
                        <th>PRODUCT INFO</th>
                        <th>QTY</th>
                        <th>UNIT PRICE</th>
                        <th>TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data->line as $d)
                    <tr>
                        <td>{{ $d->product->name }}</td>
                        <td>{{ $d->qty }}</td>
                        <td>Rp {{ number_format($d->net_price,0,',','.') }}</td>
                        <td align="left">Rp {{ number_format($d->sub_total,0,',','.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
    
                <tfoot>
                    <tr>
                        <td colspan="2" class="noval"></td>
                        <td align="left">TOTAL ( {{ $data->line->sum('qty') }} product)</td>
                        <td align="left" class="gray">Rp {{ number_format($data->total,0,',','.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="noval"></td>
                        <td align="left">DISCOUNT</td>
                        <td align="left" class="gray">Rp {{ number_format($data->shipping_cost,0,',','.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="noval"></td>
                        <td align="left">TAX</td>
                        <td align="left" class="gray">Rp {{ number_format($data->shipping_cost,0,',','.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="noval"></td>
                        <td align="left">SHIPPING</td>
                        <td align="left" class="gray">Rp {{ number_format($data->shipping_cost,0,',','.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="noval"></td>
                        <td align="left">GRAND TOTAL</td>
                        <td align="left" class="gray">Rp {{ number_format($data->grand_total,0,',','.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    
        <div class="footer" style="position: absolute; bottom: 0;">
            <table width="100%">
                <tr>
                    <td align="left" style="width: 50%;">
                        &copy; {{ date('Y') }} {{ config('app.name') }} - All rights reserved.
                    </td>
                    <td align="right" style="width: 50%;">
                        Company Slogan
                    </td>
                </tr>
    
            </table>
        </div>
    </div>
</body>

</html>