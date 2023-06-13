<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Mail</title>
</head>
<body>
    <div class="container" id="Emailvarify" style="padding:0px 200px ;">
        <div class="d-flex justify-content-center">
            <div class="col-sm-6">
                <img src="{{$message->embed(public_path('images/logo.png')) }}" alt="" width="150px">
                <h5 class="mt-4" style="color: #000;font-weight: 500;font-size: 23px;">Hi, {{ $data1['name'] }},</h5>
                <p>Thank you for contacting us. We appreciate your interest in our service.</p>
                <p>We have received your message and are reviewing it now.We will do our best to assist you.</p>
                <p>If you have any further questions or concerns, please don't hesitate to reach out to us. We are always happy to help.</p>

               <p>Best regards</p>
            </div>
        </div>
    </div>
    
</body>
</html>