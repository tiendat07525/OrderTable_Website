<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Đặt bàn thành công</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f4f4; font-family: Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4; padding:20px 0;">
    <tr>
    <td align="center">

        <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.1);">

            <tr>
                <td style="background:#d4af37; color:#ffffff; padding:20px; text-align:center;">
                    <h2 style="margin:0;">🍽 Golden Spoons</h2>
                    <p style="margin:5px 0 0;">Xác nhận đặt bàn</p>
                </td>
            </tr>

            <tr>
                <td style="padding:25px;">
                    <p style="font-size:16px;">Xin chào <strong>{{ $booking->user->name ?? 'Khách hàng' }}</strong>,</p>

                    <p style="color:#555;">
                        Cảm ơn bạn đã đặt bàn tại <strong>Golden Spoons</strong>.
                        Đơn đặt bàn của bạn đã được <span style="color:green; font-weight:bold;">xác nhận thành công</span>.
                    </p>

                    <table width="100%" cellpadding="8" cellspacing="0" style="margin-top:15px; border-collapse:collapse;">
                        <tr style="background:#f9f9f9;">
                            <td><strong>Email</strong></td>
                            <td>{{ $booking->email }}</td>
                        </tr>
                        <tr>
                            <td><strong>Số điện thoại</strong></td>
                            <td>{{ $booking->phone }}</td>
                        </tr>
                        <tr style="background:#f9f9f9;">
                            <td><strong>Ngày</strong></td>
                            <td>{{ \Carbon\Carbon::parse($booking->date)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Giờ</strong></td>
                            <td>{{ $booking->time }}</td>
                        </tr>
                        <tr style="background:#f9f9f9;">
                            <td><strong>Số khách</strong></td>
                            <td>{{ $booking->guest_count }}</td>
                        </tr>
                        <tr>
                            <td><strong>Bàn</strong></td>
                            <td>{{ $booking->table->name ?? '---' }}</td>
                        </tr>
                        <tr style="background:#f9f9f9;">
                            <td><strong>Tổng tiền</strong></td>
                            <td style="color:#d4af37; font-weight:bold;">
                                {{ number_format($booking->total_price) }} VND
                            </td>
                        </tr>
                    </table>

                    <p style="margin-top:20px; color:#555;">
                        Chúng tôi rất mong được phục vụ bạn. Nếu cần hỗ trợ, vui lòng liên hệ với chúng tôi.
                    </p>
                </td>
            </tr>

            <tr>
                <td style="background:#f4f4f4; text-align:center; padding:15px; font-size:12px; color:#888;">
                    © 2026 Golden Spoons. All rights reserved.
                </td>
            </tr>

        </table>

    </td>
    </tr>
    </table>

</body>
</html>