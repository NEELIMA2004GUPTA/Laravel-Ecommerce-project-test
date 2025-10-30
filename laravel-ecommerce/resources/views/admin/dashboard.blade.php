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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div class="p-6 bg-white shadow rounded">
                    <h3 class="text-lg font-semibold">Total Users</h3>
                    <p class="text-3xl font-bold">{{ $totalUsers }}</p>
                </div>

                <div class="p-6 bg-white shadow rounded">
                    <h3 class="text-lg font-semibold">Total Orders</h3>
                    <p class="text-3xl font-bold">{{ $totalOrders }}</p>
                </div>

                <div class="p-6 bg-white shadow rounded">
                    <h3 class="text-lg font-semibold">Total Sales</h3>
                    <p class="text-3xl font-bold">₹{{ number_format($totalSales, 2) }}</p>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let salesChart = null;

function loadChart(range = 'monthly') {
    fetch(`/admin/sales-data/${range}`)
        .then(r => r.json())
        .then(data => {

            let labels = data.map(i => i.label);
            let revenues = data.map(i => i.revenue);

            const ctx = document.getElementById('salesChart').getContext('2d');

            if (salesChart) salesChart.destroy();

            salesChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Sales Revenue",
                        data: revenues,
                        borderWidth: 2,
                        tension: 0.4,
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

        });
}

document.getElementById('rangeSelect').addEventListener('change', e => loadChart(e.target.value));
loadChart();
</script>

        </div>
    </div>
</x-app-layout>


