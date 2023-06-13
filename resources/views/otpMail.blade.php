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
                <img src="{{ $message->embed(public_path('images/logo.png')) }}" alt="" width="150px">
                <h5 class="mt-4" style="color: #000;font-weight: 500;font-size: 23px;">Hi {{$data['name']}},</h5>
                 <p>Thank you for registering on our website. As part of our account verification process, we have sent you a one-time password (OTP) to your registered phone number.</p>
                 <p>Your OTP is:  {{$data['otp']}}</p>
                 <p>Thank you for choosing our website. We look forward to helping you connect with other startup companies or investors.</p>

               <p>Best regards</p>
            </div>
        </div>
    </div>
</body>
</html>