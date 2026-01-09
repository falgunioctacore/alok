<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; }
        .item-box {
            text-align: center;
            margin-bottom: 40px;
            page-break-inside: avoid;
        }
        .label {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .qr-img {
            width: 180px;
            height: 180px;
            margin: 10px auto;
        }
    </style>
</head>
<body>

@foreach($qrData as $item)
    <div class="item-box">
        {{--<div class="label">Employee No: {{ $item['employee_no'] }}</div>--}}

        <img src="data:image/png;base64,{{ $item['qr_image'] }}" class="qr-img">

        <div class="label">V.Pass No: {{ $item['pass_no'] }}</div>
    </div>
@endforeach

</body>
</html>
