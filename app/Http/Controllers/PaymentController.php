<?php

namespace App\Http\Controllers;
use App\Models\Booking;
use App\Models\Transaction;
use App\Jobs\SendPaymentSuccessEmailJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{

    public function generateVietQr($booking)
    {
        $bankCode = config('payment.bank_code');
        $accountNo = config('payment.account_no');
        $accountName = config('payment.account_name');

        $amount = $booking->total_price;
        $content = "BOOKING-" . str_pad($booking->id, 6, '0', STR_PAD_LEFT);

        // VietQR API
        return "https://img.vietqr.io/image/{$bankCode}-{$accountNo}-compact.png"
            . "?amount={$amount}"
            . "&addInfo={$content}"
            . "&accountName=" . urlencode($accountName);
    }

    public function handle(Request $request)//done
    {

        try {
            $validator = validator($request->all(), [
                'transferType' => 'required|string',
                'transferAmount' => 'required|numeric|min:1',
                'referenceCode' => 'required|string',
                'content' => 'nullable|string',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid webhook payload',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $authorization = trim($request->header('Authorization'));

            $expected = 'Apikey ' . trim(config('payment.secret'));

            if ($authorization !== $expected) {

                return response()->json([

                    'success' => false,

                    'message' => 'Unauthorized'

                ], 401);
            }

            $gateway = $request->gateway;

            $transactionDate = $request->transactionDate;

            $accountNumber = $request->accountNumber;

            $subAccount = $request->subAccount;

            $transferType = $request->transferType;
            if ($request->transferType !== 'in') {

                return response()->json([

                    'success' => false,

                    'message' => 'Invalid transfer type'

                ], 400);
            }

            $transferAmount = $request->transferAmount;

            $accumulated = $request->accumulated;

            $code = $request->code;

            $content = trim($request->content ?? $request->description ?? '');

            $referenceCode = $request->referenceCode;

            $description = $request->description;

            preg_match(
                '/BOOKING[-_ ]?0*(\d+)/i',
                $content,
                $matches
            );

            if (empty($matches)) {

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid booking code'
                ], 400);
            }

            $bookingId = (int) $matches[1];

            $booking = Booking::find($bookingId);

            if (!$booking) {

                return response()->json([

                    'success' => false,

                    'message' => 'Booking not found'

                ], 404);
            }

            $exists = Transaction::where('reference_number', $referenceCode)->exists();

            if ($exists) {

                return response()->json([

                    'success' => true,

                    'message' => 'Duplicate transaction'

                ]);
            }

            Transaction::create([

                'booking_id' => $booking->id,

                'gateway' => $gateway,

                'transaction_date' => $transactionDate,

                'account_number' => $accountNumber,

                'sub_account' => $subAccount,

                'amount_in' =>
                    $transferType == 'in'
                    ? $transferAmount
                    : 0,

                'amount_out' =>
                    $transferType == 'out'
                    ? $transferAmount
                    : 0,

                'accumulated' => $accumulated,

                'code' => $code,

                'transaction_content' => $content,

                'reference_number' => $referenceCode,

                'body' => $description,

                'raw_data' => $request->all()
            ]);

            if (
                $booking->payment_status
                === 'paid'
            ) {

                return response()->json([

                    'success' => true,

                    'message' => 'Already paid'
                ]);
            }

            if (
                $transferAmount
                < $booking->total_price
            ) {

                return response()->json([

                    'success' => false,

                    'message' => 'Amount mismatch'

                ], 400);
            }

            $booking->update([

                'payment_status' => 'paid',

                'payment_method' => 'bank_transfer',

                'status' => 'confirmed',

                'paid_at' => now(),

                'confirmed_at' => now()
            ]);

            SendPaymentSuccessEmailJob::dispatch(
                $booking
            );

            return response()->json([

                'success' => true,

                'message' => 'Payment success'
            ]);

        } catch (\Exception $e) {
            Log::error('SePay webhook error', [
                'message' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return response()->json([

                'success' => false,

                'message' => 'Webhook error'

            ], 500);
        }
    }

}
