<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "iot_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM sensor_data ORDER BY timestamp DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>IoT Monitoring Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/modules/solid-gauge.js"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r hidden md:flex flex-col">
        <div class="h-20 flex items-center justify-center border-b">
            <span class="font-bold text-xl text-blue-700">bNTNG. PROJECT</span>
        </div>
        <nav class="flex-1 px-4 py-6">
            <ul class="space-y-2">
                <li><a href="#" class="flex items-center px-3 py-2 rounded bg-blue-50 text-blue-700 font-semibold"><span class="material-icons mr-2">dashboard</span>Dashboard</a></li>
                <li><a href="#" class="flex items-center px-3 py-2 rounded hover:bg-blue-50"><span class="material-icons mr-2">bar_chart</span>Charts</a></li>
                <li><a href="#" class="flex items-center px-3 py-2 rounded hover:bg-blue-50"><span class="material-icons mr-2">table_chart</span>Tables</a></li>
                <li><a href="#" class="flex items-center px-3 py-2 rounded hover:bg-blue-50"><span class="material-icons mr-2">table_chart</span>API WhatsApp</a></li>
                <li><a href="#" class="flex items-center px-3 py-2 rounded hover:bg-blue-50"><span class="material-icons mr-2">settings</span>Settings</a></li>
                <li><a href="#" class="flex items-center px-3 py-2 rounded hover:bg-blue-50"><span class="material-icons mr-2">help</span>Help</a></li>
                <li><a href="#" class="flex items-center px-3 py-2 rounded hover:bg-blue-50"><span class="material-icons mr-2">logout</span>Logout</a></li>


            </ul>
        </nav>
        <div class="p-4">
            <div class="bg-blue-50 rounded-lg p-3 text-center text-xs text-blue-700">Electrical Monitoring System</div>
        </div>
    </aside>
    <!-- Main Content -->
    <main class="flex-1 p-4 md:p-8">
        <!-- Status Bar -->
        <div id="status-bar" class="flex flex-wrap gap-2 mb-4">
            <div id="status-temp" class="flex items-center gap-1 px-3 py-1 rounded bg-green-100 text-green-700 font-semibold animate-pulse">
                <span class="material-icons text-lg">check_circle</span> Suhu Aman
            </div>
            <div id="status-flame" class="flex items-center gap-1 px-3 py-1 rounded bg-green-100 text-green-700 font-semibold animate-pulse">
                <span class="material-icons text-lg">check_circle</span> Api Aman
            </div>
            <div id="status-gas" class="flex items-center gap-1 px-3 py-1 rounded bg-green-100 text-green-700 font-semibold animate-pulse">
                <span class="material-icons text-lg">check_circle</span> Asap Aman
            </div>
            <div id="status-voltage" class="flex items-center gap-1 px-3 py-1 rounded bg-green-100 text-green-700 font-semibold animate-pulse">
                <span class="material-icons text-lg">check_circle</span> Listrik Aman
            </div>
        </div>
        <!-- Top Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow p-4 flex flex-col items-center">
                <div class="text-gray-500 text-xs mb-1">Voltage</div>
                <div id="stat-voltage" class="text-2xl font-bold text-blue-700">-- V</div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 flex flex-col items-center">
                <div class="text-gray-500 text-xs mb-1">Current</div>
                <div id="stat-current" class="text-2xl font-bold text-blue-700">-- A</div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 flex flex-col items-center">
                <div class="text-gray-500 text-xs mb-1">Power</div>
                <div id="stat-power" class="text-2xl font-bold text-blue-700">-- W</div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 flex flex-col items-center">
                <div class="text-gray-500 text-xs mb-1">Gas Level (MQ-2)</div>
                <div id="stat-gas" class="text-2xl font-bold text-blue-700">--</div>
            </div>
        </div>
        <!-- Energy Summary -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mt-4" id="energy-summary">
            <div class="bg-blue-50 rounded p-2 text-center">
                <div class="text-xs text-blue-700">7 Hari</div>
                <div id="energy-7d" class="font-bold text-blue-700 text-lg">- kWh</div>
            </div>
            <div class="bg-blue-50 rounded p-2 text-center">
                <div class="text-xs text-blue-700">14 Hari</div>
                <div id="energy-14d" class="font-bold text-blue-700 text-lg">- kWh</div>
            </div>
            <div class="bg-blue-50 rounded p-2 text-center">
                <div class="text-xs text-blue-700">30 Hari</div>
                <div id="energy-30d" class="font-bold text-blue-700 text-lg">- kWh</div>
            </div>
            <div class="bg-blue-50 rounded p-2 text-center">
                <div class="text-xs text-blue-700">Bulan Ini</div>
                <div id="energy-month" class="font-bold text-blue-700 text-lg">- kWh</div>
            </div>
        </div>
        <!-- Main Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <!-- Energy Chart -->
            <div class="bg-white rounded-xl shadow p-4 col-span-2">
                <div class="font-semibold text-gray-700 mb-2">Energy Usage</div>
                <div id="energy-chart" class="h-56"></div>
            </div>
            <!-- Gauge Voltage -->
            <div class="bg-white rounded-xl shadow p-4 flex flex-col items-center">
                <div class="font-semibold text-gray-700 mb-2">Voltage Gauge</div>
                <div id="voltage-gauge" class="w-full h-40"></div>
                <div id="voltage-value" class="mt-2 text-blue-700 text-lg font-bold"></div>
            </div>
        </div>
        <!-- Bottom Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Gauge Temperature -->
            <div class="bg-white rounded-xl shadow p-4 flex flex-col items-center">
                <div class="font-semibold text-gray-700 mb-2">Temperature Gauge</div>
                <div id="temp-gauge" class="w-full h-40"></div>
                <div id="temp-value" class="mt-2 text-orange-500 text-lg font-bold"></div>
            </div>
            <!-- Gauge Humidity -->
            <div class="bg-white rounded-xl shadow p-4 flex flex-col items-center">
                <div class="font-semibold text-gray-700 mb-2">Humidity Gauge</div>
                <div id="hum-gauge" class="w-full h-40"></div>
                <div id="hum-value" class="mt-2 text-cyan-500 text-lg font-bold"></div>
            </div>
            <!-- Table Last Data -->
            <div class="bg-white rounded-xl shadow p-4 overflow-x-auto">
                <div class="font-semibold text-gray-700 mb-2">Last Data</div>
                <table class="min-w-full text-xs">
                    <thead>
                        <tr>
                            <th class="py-1 px-2">Waktu</th>
                            <th class="py-1 px-2">Volt</th>
                            <th class="py-1 px-2">Arus</th>
                            <th class="py-1 px-2">Daya</th>
                            <th class="py-1 px-2">Gas</th>
                            <th class="py-1 px-2">Temp</th>
                            <th class="py-1 px-2">Hum</th>
                            <th class="py-1 px-2">Api</th>
                        </tr>
                    </thead>
                    <tbody id="table-body"></tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<script>
let voltageChart, tempChart, humChart;

async function fetchChartData(range = '30d') {
    const res = await fetch('sensor_data_api.php?range=' + range);
    return await res.json();
}

function createGauge(container, min, max, colorStops, unit, value) {
    return Highcharts.chart(container, {
        chart: { type: 'solidgauge', backgroundColor: 'transparent', height: 160 },
        title: null,
        pane: {
            center: ['50%', '60%'],
            size: '100%',
            startAngle: -90,
            endAngle: 90,
            background: {
                backgroundColor: '#f1f5f9',
                innerRadius: '60%',
                outerRadius: '100%',
                shape: 'arc'
            }
        },
        yAxis: {
            min: min,
            max: max,
            stops: colorStops,
            lineWidth: 0,
            tickWidth: 0,
            minorTickInterval: null,
            tickAmount: 2,
            labels: {
                y: 16,
                style: { color: '#64748b', fontSize: '12px' }
            }
        },
        series: [{
            name: unit,
            data: [Number(value) || 0],
            dataLabels: {
                y: -10,
                borderWidth: 0,
                useHTML: true,
                format: `<div style="text-align:center"><span style="font-size:2em;color:#2563eb">{y}${unit}</span></div>`
            }
        }],
        tooltip: { enabled: false },
        credits: { enabled: false }
    });
}

async function renderDashboard() {
    // Ambil data 30 hari untuk summary
    const data = await fetchChartData('30d');

    // Stat Cards
    document.getElementById('stat-voltage').textContent = (data.voltages?.at(-1) ?? '--') + ' V';
    document.getElementById('stat-current').textContent = (data.currents?.at(-1) ?? '--') + ' A';
    document.getElementById('stat-power').textContent = (data.powers?.at(-1) ?? '--') + ' W';
    document.getElementById('stat-gas').textContent = (data.table?.at(-1)?.gas_level ?? '--');

    // Voltage Gauge
    const voltage = Number(data.voltages?.at(-1)) || 0;
    if (!voltageChart) {
        voltageChart = createGauge(
            'voltage-gauge',
            100, 250,
            [
                [0.0, '#ef4444'],
                [0.44, '#ef4444'],
                [0.52, '#22c55e'],
                [0.6, '#22c55e'],
                [0.7, '#f59e42'],
                [1.0, '#f59e42']
            ],
            'V',
            voltage
        );
    } else {
        voltageChart.series[0].setData([voltage]);
    }
    document.getElementById('voltage-value').textContent = voltage + ' V';

    // Temperature Gauge
    const temp = Number(data.temperatures?.at(-1)) || 0;
    if (!tempChart) {
        tempChart = createGauge(
            'temp-gauge',
            0, 60,
            [
                [0.0, '#38bdf8'],
                [0.6, '#22c55e'],
                [0.8, '#f59e42'],
                [1.0, '#ef4444']
            ],
            '°C',
            temp
        );
    } else {
        tempChart.series[0].setData([temp]);
    }
    document.getElementById('temp-value').textContent = temp + ' °C';

    // Humidity Gauge
    const hum = Number(data.humidities?.at(-1)) || 0;
    if (!humChart) {
        humChart = createGauge(
            'hum-gauge',
            0, 100,
            [
                [0.0, '#f59e42'],
                [0.5, '#22c55e'],
                [1.0, '#38bdf8']
            ],
            '%',
            hum
        );
    } else {
        humChart.series[0].setData([hum]);
    }
    document.getElementById('hum-value').textContent = hum + ' %';

    // Energy Chart
    if (!window.energyChart) {
        window.energyChart = Highcharts.chart('energy-chart', {
            chart: { type: 'line', backgroundColor: 'transparent', height: 220 },
            title: { text: null },
            xAxis: { categories: data.timestamps, labels: { style: { color: '#64748b', fontSize: '10px' } } },
            yAxis: { title: { text: 'kWh' }, labels: { style: { color: '#64748b' } } },
            series: [{
                name: 'Energy',
                data: data.energies,
                color: '#2563eb'
            }],
            legend: { enabled: false },
            credits: { enabled: false }
        });
    } else {
        window.energyChart.series[0].setData(data.energies);
        window.energyChart.xAxis[0].setCategories(data.timestamps);
    }

    // Energy Usage Summary (7d, 14d, 30d, bulan ini)
    function sumEnergy(days) {
        const now = new Date();
        let sum = 0;
        for (let i = data.timestamps.length - 1; i >= 0; i--) {
            const t = new Date(data.timestamps[i]);
            if ((now - t) / (1000 * 60 * 60 * 24) <= days) {
                sum += Number(data.energies[i]) || 0;
            }
        }
        return sum.toFixed(2);
    }
    // Bulan ini
    function sumEnergyMonth() {
        const now = new Date();
        let sum = 0;
        for (let i = 0; i < data.timestamps.length; i++) {
            const t = new Date(data.timestamps[i]);
            if (t.getMonth() === now.getMonth() && t.getFullYear() === now.getFullYear()) {
                sum += Number(data.energies[i]) || 0;
            }
        }
        return sum.toFixed(2);
    }
    document.getElementById('energy-7d').textContent = sumEnergy(7) + ' kWh';
    document.getElementById('energy-14d').textContent = sumEnergy(14) + ' kWh';
    document.getElementById('energy-30d').textContent = sumEnergy(30) + ' kWh';
    document.getElementById('energy-month').textContent = sumEnergyMonth() + ' kWh';

    // DHT11 Chart (jika ingin tetap ada)
    if (!window.dhtChart) {
        window.dhtChart = Highcharts.chart('dht11-chart', {
            chart: { type: 'areaspline', backgroundColor: 'transparent', height: 140 },
            title: { text: null },
            xAxis: { categories: data.timestamps, labels: { style: { color: '#64748b', fontSize: '10px' } } },
            yAxis: { title: { text: '°C / %' }, labels: { style: { color: '#64748b' } } },
            series: [
                { name: 'Temp', data: data.temperatures, color: '#f59e42' },
                { name: 'Hum', data: data.humidities, color: '#38bdf8' }
            ],
            legend: { enabled: true },
            credits: { enabled: false }
        });
    } else {
        window.dhtChart.series[0].setData(data.temperatures);
        window.dhtChart.series[1].setData(data.humidities);
        window.dhtChart.xAxis[0].setCategories(data.timestamps);
    }

    // Status Bar Logic
    const last = data.table?.at(-1) ?? {};
    console.log('last:', last);
    console.log('data.table:', data.table);
    // Suhu aman: 15-40°C
    if (typeof last.temperature === 'number' && last.temperature >= 15 && last.temperature <= 40) {
        statusOk('status-temp', 'Suhu Aman');
    } else {
        statusAlert('status-temp', 'Suhu Tidak Aman');
    }
    // Api aman: flame = 0
    if (last.flame === 0 || last.flame === '0') {
        statusOk('status-flame', 'Api Aman');
    } else {
        statusAlert('status-flame', 'Api Terdeteksi!');
    }
    // Asap aman: gas_level < 200
    if (typeof last.gas_level === 'number' && last.gas_level < 200) {
        statusOk('status-gas', 'Asap Aman');
    } else {
        statusAlert('status-gas', 'Asap Terdeteksi!');
    }
    // Listrik aman: voltage 200-240V
    const voltageVal = Number(last.voltage);
    if (!isNaN(voltageVal) && voltageVal >= 200 && voltageVal <= 240) {
        statusOk('status-voltage', 'Listrik Aman');
    } else {
        statusAlert('status-voltage', 'Listrik Tidak Aman');
    }

    // Helper functions
    function statusOk(id, label) {
        const el = document.getElementById(id);
        el.className = "flex items-center gap-1 px-3 py-1 rounded bg-green-100 text-green-700 font-semibold animate-pulse";
        el.innerHTML = `<span class="material-icons text-lg">check_circle</span> ${label}`;
    }
    function statusAlert(id, label) {
        const el = document.getElementById(id);
        el.className = "flex items-center gap-1 px-3 py-1 rounded bg-red-100 text-red-700 font-semibold animate-bounce";
        el.innerHTML = `<span class="material-icons text-lg">error</span> ${label}`;
    }

    // Table Data (selalu tampil meski kosong)
    let tableBody = '';
    if (data.table && data.table.length) {
        data.table.slice(-10).reverse().forEach(row => {
            tableBody += `<tr>
                <td class="py-1 px-2">${row.timestamp ?? '-'}</td>
                <td class="py-1 px-2">${row.voltage ?? '-'}</td>
                <td class="py-1 px-2">${row.current ?? '-'}</td>
                <td class="py-1 px-2">${row.power ?? '-'}</td>
                <td class="py-1 px-2">${row.gas_level ?? '-'}</td>
                <td class="py-1 px-2">${row.temperature ?? '-'}</td>
                <td class="py-1 px-2">${row.humidity ?? '-'}</td>
                <td class="py-1 px-2 ${row.flame ? 'text-red-500 font-bold' : 'text-green-500'}">${row.flame ? 'Terdeteksi' : 'Tidak'}</td>
            </tr>`;
        });
    } else {
        tableBody = `<tr><td colspan="8" class="text-center py-2 text-gray-400">Tidak ada data</td></tr>`;
    }
    document.getElementById('table-body').innerHTML = tableBody;
}

// Initial render
renderDashboard();
setInterval(renderDashboard, 5000);
</script>
<!-- Material Icons CDN (for sidebar icons) -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</body>
</html>
