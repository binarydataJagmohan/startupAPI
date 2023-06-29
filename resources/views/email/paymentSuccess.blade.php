<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Payment Success</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
        <meta content="Coderthemes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.ico">

        <!-- App favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.ico">

        <!-- Bootstrap Css -->
        <link href="assets/css/bootstrap.min.css" id="bootstrap-stylesheet" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="assets/css/app.min.css" id="app-stylesheet" rel="stylesheet" type="text/css" />

    </head>

    <body style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #f6f6f6; margin: 0;"
      bgcolor="#f6f6f6">

<table class="body-wrap"
       style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0;"
       bgcolor="#f6f6f6">
    <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
        <td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;"
            valign="top"></td>
        <td class="container" width="600"
            style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;"
            valign="top">
            <div class="content"
                 style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
                <table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope
                       itemtype="http://schema.org/ConfirmAction"
                       style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; margin: 0; border: none;"
                       >
                    
                    <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                        <td class="content-wrap"
                            style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; display: inline-block; font-size: 14px; vertical-align: top; margin: 0; padding: 30px; background-color: #fff;"
                            valign="top">
                            <meta itemprop="name" content="Confirm Email"
                                  style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"/>
                            <table width="100%" cellpadding="0" cellspacing="0"
                                   style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                <tr>
                                    <td style="text-align: center">
                                         <a href="#" style="display: block;margin-bottom: 10px;"> <img src="{{ $message->embed(public_path('img/logo.png')) }}" height="50" alt="logo"/></a> <br/>
                                    </td>
                                </tr>
            
                                <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                    <td class="content-block"
                                        style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;"
                                        valign="top">
                                        Hi Dear,<br>
                                               Congratulations Dear, Your Payment has been done Successfully.
                                    </td>
                                </tr>
                                <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                    <td class="content-block"
                                        style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;"
                                        valign="top">
                                      <table cellpadding="0" cellspacing="0"
                                             style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                        <tr style="background-color:#9fe870;"><td colspan="2" width="100%" style="padding: 8px; border: 1px solid #ddd;">Fund Invoice</td>
                                            {{-- <td width="100%" style="padding: 8px; border: 1px solid #ddd;"></td> --}}
                                        </tr>
                                        <tr>
                                            <td width="100%" style="padding: 8px; border: 1px solid #ddd;">
                                            {{$mail['username']}},
                                            <br>{{$mail['user']->city}},
                                            <br>{{$mail['user']->country}}
                                           
                                            </td>
                                            <td width="100%" style="padding: 8px; border: 1px solid #ddd;">
                                                <br>Phone no: +{{$mail['user']->phone}}
                                               {{-- Order Status:<p style="color:green;"> SUCCESS</p> --}}
                                            </td>
                                        </tr>
                                        <tr>
                                          <td style="padding: 8px; border: 1px solid #ddd;">
                                            Startup Information:<br>
                                            {{$mail['business']->business_name}},<br>
                                            {{$mail['business']->sector}}
                                        </td><br>
                                          <td style="padding: 8px; border: 1px solid #ddd;"> Startup Stage : {{$mail['business']->stage}}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px; border: 1px solid #ddd;">
                                             Repayment Date :
                                          </td><br>
                                            <td style="padding: 8px; border: 1px solid #ddd;">{{ $mail['date']}}
                                            </td>
                                          </tr>
                                        <tr>
                                            <td style="padding: 8px; border: 1px solid #ddd;">
                                             Total Units :
                                          </td><br>
                                            <td style="padding: 8px; border: 1px solid #ddd;"> {{$mail['booking']->no_of_units}}</td>
                                          </tr>
                                          <tr>
                                            <td style="padding: 8px; border: 1px solid #ddd;">
                                             Subscription Value :
                                          </td><br>
                                            <td style="padding: 8px; border: 1px solid #ddd;"> {{$mail['booking']->subscription_value}}</td>
                                          </tr>
                                        <tr>
                                          <td  style="padding: 8px; border: 1px solid #ddd; text-align: right;">Grand Total:</td>
                                          <td style="padding: 8px; border: 1px solid #ddd;">${{$mail['booking']->repayment_value}}</td>
                                        </tr>
                                      </table>
                                    </td>
                                  </tr>
                                  
                                <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                    <td class="content-block" itemprop="handler" itemscope
                                        itemtype="http://schema.org/HttpActionHandler"
                                        style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;"
                                        valign="top">
                                        
                                           <p style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px; text-align: left;">Thanks,<br>
                                              Startup - IT Startups &amp; Digital Services</p>
                                        </td>

                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <div style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 20px;">
                    <table width="100%"
                           style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                        <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                            <td class="aligncenter content-block"
                                style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 0 0 20px;"
                                align="center" valign="top"><a href="#"
                                                               style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; color: #999; text-decoration: underline; margin: 0;">Startup</a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </td>
        <td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;"
            valign="top"></td>
    </tr>
</table>
</body>
</html>