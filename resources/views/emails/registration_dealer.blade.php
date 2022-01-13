<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta charset="utf-8"> <!-- utf-8 works for most cases -->
        <meta name="viewport" content="width=device-width"> <!-- Forcing initial-scale shouldn't be necessary -->
         <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Use the latest (edge) version of IE rendering engine -->
         <title></title> <!-- The title tag shows in email notifications, like Android 4.4. -->
    </head>               
    <body>

        <table width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;margin: 0 auto;font-family:Verdana; letter-spacing: -1px;background: #F1F1F1;">
            <tbody>
                <tr>
                    <td style="text-align: center;">
                         <img src="{{URL::asset('images/email/wmb-img-logo.png')}}" style="margin: 50px 0px;" />
                    </td>
                </tr>
                <tr style="">
                    <td class="action-content" style="color:#00043C;background: #F1F1F1;padding: 20px 60px;padding-bottom: 60px;">
                       <h1 style="font-family:Verdana;font-weight: normal;font-size: 22px;margin-top: 26px;line-height: 100%;color:#00043C;text-align: left;line-height: 160%;"><br>Welcome to WatchMyBid<br /></h1>
                      
                         <p style="font-weight: normal;line-height: 150%;margin-bottom: 20px;margin-top: 50px;">
                            <span style="">Hello {{ isset($name) ? $name : $first_name}},</span><br /><br />
                            This is a system generated mail for to inform you that your {{$account}} account has been created with WatchMyBid.</p>
                            <p style="font-weight: normal;line-height: 150%;margin-bottom: 20px;">Please find the new credentials below for login to WatchMyBid.</p>
                            <p style="font-weight: normal;color: #00043C;">Email: {{$email}}</p>
                            <p style="font-weight: normal;line-height: 150%;margin-bottom: 40px;color: #00043C;">Password: {{$password}}</p>
                            <p style="color: #00043C;margin-bottom: 40px;">Please click below</p>
                            <a href="{{url('dealer/login')}}" style="text-decoration: none;color: #fff;"><span style="color: #fff;background: #00043C;padding: 10px 30px; ">LOGIN</span></a>
                    </td>
                </tr>
                <tr>
                    <td style="">
                        <a style="color:#fff;text-decoration: none;" href="{{url('/')}}">
                            <p style="text-align: center;color: #00043c;margin: 30px 0px;font-size: 14px;"> www.watchmybid.com</p></a>
                    </td>
                </tr>
            </tbody>
        </table>
    </body>
</html>