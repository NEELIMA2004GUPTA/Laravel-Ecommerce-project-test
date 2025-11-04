<x-app-layout>
    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-3">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 text-red-800 p-2 rounded mb-3">
            {{ session('error') }}
        </div>
    @endif
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 auto-rows-fr">

                <div class="p-6 bg-white shadow rounded">
                <h3 class="text-lg font-semibold mb-4">Total Users Overview</h3>

                <div class="grid grid-cols-2 gap-2 text-center">
                    <div class="p-3 border rounded bg-gray-50">
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-xl font-bold">{{ $totalUsers }}</p>
                    </div>
                    <div class="p-3 border rounded bg-green-50">
                        <p class="text-sm font-medium text-gray-600">Active Users</p>
                        <p class="text-xl font-bold text-green-700">{{ $activeUsers }}</p>
                    </div>

                    <div class="p-3 border rounded bg-red-50">
                        <p class="text-sm font-medium text-gray-600">Blocked Users</p>
                        <p class="text-xl font-bold text-red-700">{{ $blockedUsers }}</p>
                    </div>

                 </div>
                </div>

                <div class="p-6 bg-white shadow rounded md:col-span-2">
                    <!-- Order Overview -->
                    <h3 class="text-lg font-semibold mb-4">Orders Overview</h3>
                    <div class="grid grid-cols-5 gap-4 text-center">

                        <div class="p-3 border rounded bg-yellow-50">
                            <p class="text-xs font-medium text-gray-600">Pending</p>
                            <p class="text-xl font-bold text-yellow-700">{{ $pendingOrders }}</p>
                        </div>

                        <div class="p-3 border rounded bg-blue-50">
                            <p class="text-xs font-medium text-gray-600">Confirmed</p>
                            <p class="text-xl font-bold text-blue-700">{{ $confirmedOrders }}</p>
                        </div>

                        <div class="p-3 border rounded bg-purple-50">
                            <p class="text-xs font-medium text-gray-600">Shipped</p>
                            <p class="text-xl font-bold text-purple-700">{{ $shippedOrders }}</p>
                        </div>

                        <div class="p-3 border rounded bg-green-50">
                            <p class="text-xs font-medium text-gray-600">Delivered</p>
                            <p class="text-xl font-bold text-green-700">{{ $deliveredOrders }}</p>
                        </div>

                        <div class="p-3 border rounded bg-red-50">
                            <p class="text-xs font-medium text-gray-600">Cancelled</p>
                            <p class="text-xl font-bold text-red-700">{{ $cancelledOrders }}</p>
                        </div>

                        <div class="p-3 border rounded bg-gray-50 col-span-5">
                            <p class="text-xs font-medium text-gray-600">Total</p>
                            <p class="text-xl font-bold">{{ $totalOrders }}</p>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Top Products -->
            <div class="p-6 bg-white shadow rounded">
                <h3 class="font-semibold mb-3 text-lg">Top Selling Products</h3>
                <table class="w-full border">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-2 border">Product</th>
                            <th class="p-2 border">Qty Sold</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topProducts as $p)
                            <tr>
                                <td class="p-2 border">{{ $p->product->title }}</td>
                                <td class="p-2 border">{{ $p->qty_sold }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Recent Orders -->
            <div class="p-6 bg-white shadow rounded">
                <h3 class="font-semibold mb-3 text-lg">Recent Orders</h3>
                <table class="w-full border">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-2 border">Order ID</th>
                            <th class="p-2 border">Customer</th>
                            <th class="p-2 border">Total</th>
                            <th class="p-2 border">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                            <tr>
                                <td class="p-2 border">#{{ $order->id }}</td>
                                <td class="p-2 border">{{ $order->user->name }}</td>
                                <td class="p-2 border">₹{{ $order->total }}</td>
                                <td class="p-2 border">{{ $order->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-6 bg-white shadow rounded mt-6">
    <div class="flex justify-between items-center mb-3">
        <h3 class="font-semibold text-lg">Sales Chart</h3>

        <select id="rangeSelect" class="border p-2 rounded" style="width: 150px;">
            <option value="daily">Daily</option>
            <option value="weekly">Weekly</option>
            <option value="monthly" selected>Monthly</option>
            <option value="yearly">Yearly</option>
        </select>
    </div>

    <div style="height: 350px;">
        <canvas id="salesChart"></canvas>
    </div>
</div>
<div class="p-6 bg-white shadow rounded md:col-span-2">
    <h3 class="text-lg font-semibold mb-4">Coupon Summary</h3>

    <div class="grid grid-cols-4 gap-4 text-center">
        <div class="p-3 border rounded bg-blue-50">
            <p class="text-sm text-gray-600">Daily</p>
            <p class="text-xl font-bold text-blue-700">{{ $dailyCoupons }}</p>
        </div>

        <div class="p-3 border rounded bg-yellow-50">
            <p class="text-sm text-gray-600">Weekly</p>
            <p class="text-xl font-bold text-yellow-700">{{ $weeklyCoupons }}</p>
        </div>

        <div class="p-3 border rounded bg-green-50">
            <p class="text-sm text-gray-600">Monthly</p>
            <p class="text-xl font-bold text-green-700">{{ $monthlyCoupons }}</p>
        </div>

        <div class="p-3 border rounded bg-purple-50">
            <p class="text-sm text-gray-600">Yearly</p>
            <p class="text-xl font-bold text-purple-700">{{ $yearlyCoupons }}</p>
        </div>
    </div>
</div>
<div class="p-6 bg-white shadow rounded mt-6">
    <h3 class="text-lg font-semibold mb-4 text-center">Coupon Status Chart</h3>
    <div class="flex justify-center items-center" style="height: 300px;">
        <canvas id="couponChart" style="max-width: 300px;"></canvas>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    let salesChart = null;

    function loadChart(range = 'monthly') {
        fetch(`/admin/sales-data/${range}`)
            .then(response => response.json())
            .then(data => {

                const labels = data.map(i => i.label);
                const revenues = data.map(i => i.revenue);

                const ctx = document.getElementById('salesChart').getContext('2d');

                if (salesChart !== null) {
                    salesChart.destroy();
                }

                salesChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: "Sales Revenue",
                            data: revenues,
                            borderWidth: 2,
                            tension: 0.4
                        }]
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false 
                    }
                });
            });
    }

    const rangeSelect = document.getElementById('rangeSelect');
    if (rangeSelect) {
        rangeSelect.addEventListener('change', e => loadChart(e.target.value));
    }

    // Default Load
    loadChart();

    // Coupon Chart
    const couponCtx = document.getElementById('couponChart').getContext('2d');

    const couponData = JSON.parse(`@json([
        (int)($activeCoupons ?? 0),
        (int)($inactiveCoupons ?? 0),
        (int)($expiredCoupons ?? 0)
    ])`);

    new Chart(couponCtx, {
        type: 'pie',
        data: {
            labels: ['Active', 'Inactive', 'Expired'],
            datasets: [{
                data: couponData,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: { 
                legend: { position: 'bottom' } 
            }
        }
    });

});
</script>


</x-app-layout>


