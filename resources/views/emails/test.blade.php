<!DOCTYPE html>
<html>
<head>
    <title>Price</title>
</head>
<body>
<h1>Hi {{$data['broker_name']}},</h1>

<p>Could you give me a quote for the following, please</p>
<p>Thanks</p>
<table class="send-email">

    @foreach($data['purchasers'] as $purchaser)
        <tr>
            <td>{{$purchaser['customer_name']}}</td>
            <td>{{$purchaser['address']}}</td>
        </tr>
    @endforeach
</table>

</body>
</html>
<style>
    .send-email {
        border-collapse: collapse;
    }

    td {
        border: 1px solid #000000;
        padding:10px;
    }
</style>
