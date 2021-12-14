<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Email for new password</title>
</head>
<body>
    <table border="0" width="430" cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin:0 auto 0 auto" >
                <tbody>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            <p style="margin:10px 0 10px 0;color:#565a5c;font-size:18px">Hi {{$details['user_name']}},</p>
                            <p style="margin:10px 0 10px 0;color:#565a5c;font-size:18px">We got a Request for password change On image-housting</p>
                            <p style="margin:10px 0 10px 0;color:#565a5c;font-size:18px">your new password is {{$details['new_password']}}.</p>
                        </td>
                    </tr>
                    <tr></tr>
                </tbody>
            </table>
</body>
</html>
