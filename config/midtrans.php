<?php

require_once dirname(__FILE__) . '/../config/midtrans.php';

\Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY'); // Ganti dengan kunci server Anda
\Midtrans\Config::$clientKey = env('MIDTRANS_CLIENT_KEY'); // Ganti dengan kunci klien Anda
\Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false); // Set true jika di lingkungan produksi
\Midtrans\Config::$isSanitized = env('MIDTRANS_IS_SANITIZED', true); // Set sanitasi
\Midtrans\Config::$is3ds = env('MIDTRANS_IS_3DS', true); // Set 3DS

