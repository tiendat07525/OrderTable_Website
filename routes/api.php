<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;

Route::post('sepay/webhook', [PaymentController::class, 'handle']);//done
