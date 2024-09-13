<table border="0" cellpadding="0" cellspacing="0" style="background-color: #ffd7fc; background: linear-gradient(180deg, #ffd7fc 0%, rgb(255, 255, 255) 80%); font-family: Calibri, sans-serif; margin: 0 auto; padding: 10px 30px 0px 30px;">
  <tbody>
    <tr style="border-collapse:collapse;border-spacing:0;padding:0;margin:0">
      <td style="text-align:center;padding:10px;color:#202124" valign="top" style="border-bottom: 1px solid #202124;">
      <a href="{{ URL::to('/') }}" title="{{ config('constant.siteTitle') }}">
              <img alt="{{ config('constant.siteTitle') }}" src="{{asset('images/logo-updated.png')}}" height="60">
          </a>
      </td>
    </tr>
    
    <tr style="border-collapse:collapse;border-spacing:0;padding:10px;margin:0">
      <td style="max-width:598px;padding:0;margin:0;width:598px;min-width:598px" width="598" align="center" valign="top">
        <table cellspacing="0" cellpadding="0" border="0" width="100%" valign="top" role="presentation" style="margin:auto;width:100%">
          <tbody>
            <tr>
              <td align="center" style="text-align:center;padding:0px 40px 19px 40px;color:#202124;font-size:42px;line-height:54px;direction:ltr;font-weight:normal;word-break:normal;color:#25272b;font-size:32px;line-height:39px;padding:2px 85px 6px 85px" dir="ltr">OTP Verification
              </td>
            </tr>
          </tbody>
        </table>

        <table cellspacing="0" cellpadding="0" border="0" width="480" style="width:480px" role="presentation">
           <tbody>
              <tr>
                <td style="padding:20px"  dir="ltr">
                  <p style="direction:ltr;font-weight:normal;color:#5f6368;word-break:normal;font-size:20px;line-height:32px;color:#5f6368;font-size:16px;line-height:26px;font-weight:normal;color:#25272b">Dear <b>{{ $mailData['name'] ?? 'User' }}</b>,</p>
                  <p style="direction:ltr;word-break:normal;font-size:16px;line-height:26px;font-weight:normal;color:#25272b">We received a request to reset your password. If this was you, simply find the OTP below to create a new password:
                  </p>

                  <p style="direction:ltr;word-break:normal;font-size:16px;line-height:26px;font-weight:normal;color:#25272b">Your One Time Password to change your password is <b>{{ $mailData['code'] }}</b></p>

                  <p style="direction:ltr;word-break:normal;font-size:16px;line-height:26px;font-weight:normal;color:#25272b">If you didn’t request a password reset, please ignore this email. Your account will remain secure.
                  </p>

                  <p style="direction:ltr;word-break:normal;font-size:16px;line-height:26px;font-weight:bold;color:#25272b">Warm regards,</p>
                  <p style="direction:ltr;word-break:normal;font-size:16px;line-height:26px;font-weight:normal;color:#25272b">ShowSearch</p>


              </td>
              </tr>
          </tbody>
        </table>
 
       

       
        <table cellspacing="0" cellpadding="0" border="0" width="100%" style="text-align:center;background:#5f0f58" >
          <tbody>
            <tr style="border-width:0">
              <td align="center" style="padding:10px;">
                <span style="color:#fff; font-size:14px;font-weight:normal">© 2024 ShowSearch </span>
              </td>
            </tr>
          </tbody>
        </table>
      </td>
    </tr>
  </tbody>
</table>