const apiBase = 'api';
let chart;
let chartReady = false;

document.addEventListener('DOMContentLoaded', () => {
    initSwitchHandlers();
    refreshAll();

    setInterval(fetchLatestData, 4000);
    setInterval(fetchChartData, 8000);
    setInterval(fetchControlState, 5000);
});

function refreshAll() {
    fetchLatestData();
    fetchChartData();
    fetchControlState();
}

// === DATA TERAKHIR ===
function fetchLatestData() {
    fetch(`${apiBase}/get_data.php`)
        .then(r => r.json())
        .then(res => {
            if (res.status === 'OK') {
                const d = res.data;
                setText('temp-value', d.temperature.toFixed(1));
                setText('hum-value', d.humidity.toFixed(1));
                setText('gas-value', d.gas.toFixed(2));
                setText('ph-value', d.ph.toFixed(2));
            } else {
                setText('temp-value', '--');
                setText('hum-value', '--');
                setText('gas-value', '--');
                setText('ph-value', '--');
            }
        })
        .catch(console.error);
}

// === DATA GRAFIK ===
function fetchChartData() {
    fetch(`${apiBase}/get_chart_data.php`)
        .then(r => r.json())
        .then(res => {
            if (res.status !== 'OK') return;
            const rows = res.data || [];
            if (rows.length === 0) return;

            const labels = rows.map(r => (r.created_at || '').slice(11, 16));
            const temps = rows.map(r => r.temperature);
            const hums  = rows.map(r => r.humidity);
            const gases = rows.map(r => r.gas);
            const phs   = rows.map(r => r.ph);

            if (!chartReady) {
                const ctx = document.getElementById('sensorChart').getContext('2d');
                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [
                            { label: 'Temp (°C)', data: temps, borderWidth: 2, tension: 0.35 },
                            { label: 'Humidity (%)', data: hums, borderWidth: 2, tension: 0.35 },
                            { label: 'Gas', data: gases, borderWidth: 2, tension: 0.35 },
                            { label: 'pH', data: phs, borderWidth: 2, tension: 0.35 }
                        ]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: {
                                ticks: { color: '#9ca3af', maxTicksLimit: 6 }
                            },
                            y: {
                                ticks: { color: '#9ca3af' }
                            }
                        },
                        plugins: {
                            legend: {
                                labels: { color: '#e5e7eb', font: { size: 9 } }
                            }
                        }
                    }
                });
                chartReady = true;
            } else {
                chart.data.labels = labels;
                chart.data.datasets[0].data = temps;
                chart.data.datasets[1].data = hums;
                chart.data.datasets[2].data = gases;
                chart.data.datasets[3].data = phs;
                chart.update();
            }
        })
        .catch(console.error);
}

// === CONTROL STATE ===
function fetchControlState() {
    fetch(`${apiBase}/get_control.php`)
        .then(r => r.json())
        .then(res => {
            if (res.status === 'OK') {
                applyControlState(res.fan, res.aerator);
            }
        })
        .catch(console.error);
}

function initSwitchHandlers() {
    const fanSwitch = document.getElementById('fan-switch');
    const aerSwitch = document.getElementById('aerator-switch');

    fanSwitch.addEventListener('click', () => {
        const newState = fanSwitch.classList.contains('active') ? 0 : 1;
        updateControl({ fan: newState });
    });

    aerSwitch.addEventListener('click', () => {
        const newState = aerSwitch.classList.contains('active') ? 0 : 1;
        updateControl({ aerator: newState });
    });
}

function updateControl(payload) {
    const formData = new FormData();
    if (payload.fan !== undefined) formData.append('fan', payload.fan);
    if (payload.aerator !== undefined) formData.append('aerator', payload.aerator);

    fetch(`${apiBase}/update_control.php`, {
        method: 'POST',
        body: formData
    })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'OK') {
                applyControlState(res.fan, res.aerator);
            }
        })
        .catch(console.error);
}

function applyControlState(fan, aerator) {
    toggleClass(document.getElementById('fan-switch'), 'active', fan === 1);
    toggleClass(document.getElementById('aerator-switch'), 'active', aerator === 1);
}

// util kecil
function setText(id, text) {
    const el = document.getElementById(id);
    if (el) el.textContent = text;
}

function toggleClass(el, cls, on) {
    if (!el) return;
    if (on) el.classList.add(cls);
    else el.classList.remove(cls);
}
