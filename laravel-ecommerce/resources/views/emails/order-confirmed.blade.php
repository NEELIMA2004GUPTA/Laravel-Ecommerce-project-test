<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order Confirmed</title>
</head>
<body class="bg-gray-100 py-10">
    <div class="max-w-xl mx-auto bg-white shadow-lg rounded-lg p-6">
        
        <h2 class="text-2xl font-bold text-gray-800">
            Hello {{ $order->user->name }},
        </h2>
        <p class="text-gray-600 mt-2">
            Your order <strong>#{{ $order->id }}</strong> has been 
            <span class="text-green-600 font-semibold">confirmed</span> successfully! 🎉
        </p>

        <div class="bg-gray-100 border border-gray-300 rounded-lg p-4 mt-6">
            <p class="text-gray-700">
                <strong>Total:</strong> ₹{{ $order->total }}
            </p>
            <p class="text-gray-700 mt-1">
                <strong>Payment:</strong> {{ $order->payment_method }}
            </p>
        </div>

        <div class="text-center mt-8">
            <a href="{{ route('orders') }}"
                class="inline-block bg-blue-600 text-white font-medium px-6 py-3 rounded-lg hover:bg-blue-700">
                View My Orders
            </a>
        </div>

        <p class="text-gray-500 text-sm mt-8 text-center">
            Thanks,<br>
            Shoppix
        </p>
    </div>
</body>
</html>
