
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Show Search</title>
    <style>

    </style>
</head>

<body>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@200;300;400;500;600&display=swap"
        rel="stylesheet" />

    <body>
        <div class="email-template" style=" padding: 10px; ">
            <table align="center" cellpadding="0" cellspacing="0" width="600"
            style="background-color: #ffd7fc; background: linear-gradient(180deg, #ffd7fc 0%, rgb(255, 255, 255) 80%); font-family: Calibri, sans-serif; margin: 0 auto; padding: 10px 30px 0px 30px;">
                <tr>
                    <td
                        style="font-family:tahoma, geneva, sans-serif;color:#29054a;font-size:12px; padding:10px;text-align: center;">
                        <a href="{{ URL::to('/') }}" title="{{ config('constant.siteTitle') }}">
                            <img alt="{{ config('constant.siteTitle') }}" src="{{asset('images/logo-updated.png')}}" height="60">
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style=" padding: 10px;" >
                        <h1
                            style="color: #601059;font-size: 20px;text-align: center;font-weight:500; margin: 10px 0;line-height:10px;padding:8px">
                            PLANT {{ $status == 1 ? 'APPROVED' : 'UNAPPROVED' }}</h1>
                    </td>
                </tr>
                <tr>
                    <td valign="top" style="padding: 10px 30px;">
                        <p
                            style="font-size:16px;font-weight: 600;line-height: 24px;text-align:left;color: #25272b;margin: 10px 0;">
                            Hello User,</p>
                    </td>
                </tr>
                <tr>
                    <td valign="top" style="padding: 10px 30px;">
                    @if ($status == 1)
                    <p style="font-size: 16px; font-weight: normal; line-height: 24px; text-align: justify; color: #25272b;">
                            This is to inform you that the plant <strong>{{ $plant->plant_name }}</strong> has been 
                            Approved by the administrator. Now your plant will be available for all the Community Owners/ Retailers to contact. Please take note of this status update.
                        </p>
                    @else 
                    <p style="font-size: 16px; font-weight: normal; line-height: 24px; text-align: justify; color: #25272b;">
                            This is to inform you that the plant <strong>{{ $plant->plant_name }}</strong> has been 
                            unapproved by the administrator. Please take note of this status update.
                        </p>
                    @endif
                    </td>
                </tr>

                <tr>
                    <td valign="top" style="padding: 10px 30px;">
                        <p
                            style="font-size: 16px;font-weight: normal;line-height: 24px;text-align:justify;color: #25272b;">
                            <strong>Plant Name:</strong> {{ $plant->plant_name }}<br>
                            <strong>Plant Location:</strong> {{ $plant->full_address }}<br>
                        </p>
                    </td>
                </tr>

                <tr>
                    <td valign="top" style="padding: 10px 30px;">
                    @if ($status == 1)
                    <p style="font-size: 16px; font-weight: normal; line-height: 24px; text-align: justify; color: #25272b;">
                            Kindly use the below URL to login into the manufacturer panel.
                        </p>
                        <p
                            style="font-size: 16px;font-weight: normal;line-height: 24px;text-align:justify;color: #25272b;">URL -<a href="http://showsearch.net/manufacturer/login" style="text-decoration: underline;">https://showsearch.net/manufacturer/login</p>
                    @else 
                    <p style="font-size: 16px; font-weight: normal; line-height: 24px; text-align: justify; color: #25272b;">
                            You may contact the administrator to clarify the reason for rejection of your plant.
                        </p>
                    @endif
                    </td>
                </tr>

                <tr>
                    <td valign="top" style="padding: 10px 30px;">
                        <p
                            style="font-size: 16px;font-weight: normal;line-height: 24px;text-align:justify;color: #25272b;">
                            Warm regards,</p>
                        <p
                            style="font-size: 16px;font-weight: normal;line-height: 24px;text-align:justify;color: #25272b;">
                           ShowSearch</p>
                    </td>
                </tr>
            </table>
            <table align="center" cellspacing="0" cellpadding="0" border="0" width="600" style="text-align:center;background:#5f0f58" >
              <tbody>
                <tr style="border-width:0">
                  <td align="center" style="padding:10px;">
                    <span style="color:#fff; font-size:16px;font-weight:normal">© 2024 ShowSearch </span>
                  </td>
                </tr>
              </tbody>
            </table>

         
        </div>
    </body>

</html>
