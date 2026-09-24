document.addEventListener('DOMContentLoaded', () => {
    const dataEl = document.getElementById('dashboard-data');
    if (!dataEl) return;

    let data;
    try {
        data = JSON.parse(dataEl.textContent);
    } catch (e) {
        console.error('Failed to parse dashboard charts data', e);
        return;
    }

    const ticketsData = data.tickets_by_status;
    const tasksData = data.tasks_by_status;
    const projectsData = data.projects_by_status;
    const priorityData = data.tickets_by_priority;
    const monthlyData = data.tickets_monthly;

    // Track charts globally so we can update them on theme changes
    const charts = {};

    // Resolve theme colors dynamically
    function getThemeColors() {
        const isDark = document.documentElement.classList.contains('dark');
        return {
            text: isDark ? '#94A3B8' : '#71717A',
            grid: isDark ? '#27272A' : '#F4F4F5',
            tooltipBg: isDark ? '#18181B' : '#FFFFFF',
            tooltipBorder: isDark ? '#27272A' : '#E4E4E7',
            tooltipTitle: isDark ? '#FAFAFA' : '#09090B',
            tooltipBody: isDark ? '#A1A1AA' : '#71717A',
            borderColor: isDark ? '#18181B' : '#FFFFFF'
        };
    }

    let themeColors = getThemeColors();

    Chart.defaults.color = themeColors.text;
    Chart.defaults.borderColor = themeColors.grid;
    Chart.defaults.font.family = 'Inter, sans-serif';

    const pieOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { padding: 16, usePointStyle: true, pointStyleWidth: 8 },
            },
            tooltip: {
                backgroundColor: themeColors.tooltipBg,
                titleColor: themeColors.tooltipTitle,
                bodyColor: themeColors.tooltipBody,
                borderColor: themeColors.tooltipBorder,
                borderWidth: 1,
                padding: 12,
                usePointStyle: true,
            },
        },
    };

    const doughnutOptions = {
        ...pieOptions,
        cutout: '58%',
    };

    function emptyChartPlugin(message) {
        return {
            id: 'emptyOverlay',
            afterDraw(chart) {
                const { ctx, width, height } = chart;
                const chartData = chart.data.datasets[0]?.data || [];
                if (chartData.some(v => v > 0)) return;
                ctx.save();
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillStyle = '#A1A1AA';
                ctx.font = '13px Inter, sans-serif';
                ctx.fillText(message, width / 2, height / 2);
                ctx.restore();
            },
        };
    }

    if (document.getElementById('ticketsChart')) {
        charts.tickets = new Chart(document.getElementById('ticketsChart'), {
            type: 'pie',
            data: {
                labels: ticketsData.labels,
                datasets: [{
                    data: ticketsData.data,
                    backgroundColor: ticketsData.colors,
                    borderWidth: 2,
                    borderColor: themeColors.borderColor,
                    hoverOffset: 8,
                }],
            },
            options: pieOptions,
            plugins: [emptyChartPlugin('Belum ada data tiket')],
        });
    }

    if (document.getElementById('tasksChart')) {
        charts.tasks = new Chart(document.getElementById('tasksChart'), {
            type: 'doughnut',
            data: {
                labels: tasksData.labels,
                datasets: [{
                    data: tasksData.data,
                    backgroundColor: tasksData.colors,
                    borderWidth: 2,
                    borderColor: themeColors.borderColor,
                    hoverOffset: 6,
                }],
            },
            options: doughnutOptions,
            plugins: [emptyChartPlugin('Belum ada data tugas')],
        });
    }

    if (document.getElementById('projectsChart')) {
        charts.projects = new Chart(document.getElementById('projectsChart'), {
            type: 'bar',
            data: {
                labels: projectsData.labels,
                datasets: [{
                    label: 'Jumlah Proyek',
                    data: projectsData.data,
                    backgroundColor: projectsData.colors.map(() => '#EA580C'), // VexaHost Orange
                    borderRadius: 6,
                    borderSkipped: false,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: themeColors.tooltipBg,
                        borderColor: themeColors.tooltipBorder,
                        borderWidth: 1,
                        titleColor: themeColors.tooltipTitle,
                        bodyColor: themeColors.tooltipBody,
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: themeColors.text },
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: themeColors.text },
                        grid: { color: themeColors.grid },
                    },
                },
            },
            plugins: [emptyChartPlugin('Belum ada data proyek')],
        });
    }

    if (document.getElementById('priorityChart')) {
        charts.priority = new Chart(document.getElementById('priorityChart'), {
            type: 'pie',
            data: {
                labels: priorityData.labels,
                datasets: [{
                    data: priorityData.data,
                    backgroundColor: ['#EA580C', '#F59E0B', '#EF4444'], // VexaHost Green for low/normal
                    borderWidth: 2,
                    borderColor: themeColors.borderColor,
                    hoverOffset: 8,
                }],
            },
            options: {
                ...pieOptions,
                plugins: {
                    ...pieOptions.plugins,
                    legend: { position: 'right', labels: { usePointStyle: true } },
                },
            },
            plugins: [emptyChartPlugin('Belum ada data prioritas')],
        });
    }

    if (document.getElementById('monthlyChart')) {
        const gradientCtx = document.getElementById('monthlyChart').getContext('2d');
        const makeGradient = () => {
            const grad = gradientCtx.createLinearGradient(0, 0, 0, 280);
            grad.addColorStop(0, 'rgba(234, 88, 12, 0.25)'); // VexaHost Orange gradient
            grad.addColorStop(1, 'rgba(234, 88, 12, 0)');
            return grad;
        };

        charts.monthly = new Chart(document.getElementById('monthlyChart'), {
            type: 'line',
            data: {
                labels: monthlyData.labels,
                datasets: [{
                    label: 'Tiket Baru',
                    data: monthlyData.data,
                    borderColor: '#EA580C', // VexaHost Orange
                    backgroundColor: makeGradient(),
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#EA580C',
                    pointBorderColor: themeColors.borderColor,
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: themeColors.tooltipBg,
                        borderColor: themeColors.tooltipBorder,
                        borderWidth: 1,
                        titleColor: themeColors.tooltipTitle,
                        bodyColor: themeColors.tooltipBody,
                    },
                },
                scales: {
                    x: {
                        grid: { color: themeColors.grid },
                        ticks: { color: themeColors.text },
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: themeColors.text },
                        grid: { color: themeColors.grid },
                    },
                },
            },
        });
    }

    // Listen for the custom theme-changed event dispatched by our layout toggleTheme()
    window.addEventListener('theme-changed', (e) => {
        themeColors = getThemeColors();

        // Update globally
        Chart.defaults.color = themeColors.text;
        Chart.defaults.borderColor = themeColors.grid;

        // Re-render each chart
        Object.keys(charts).forEach(key => {
            const chart = charts[key];
            if (!chart) return;

            // Update axis ticks and grids
            if (chart.options.scales) {
                Object.keys(chart.options.scales).forEach(scaleKey => {
                    const scale = chart.options.scales[scaleKey];
                    if (scale.grid) {
                        scale.grid.color = themeColors.grid;
                    }
                    if (scale.ticks) {
                        scale.ticks.color = themeColors.text;
                    }
                });
            }

            // Update border colors on pie/doughnut sets
            if (chart.data.datasets && chart.data.datasets[0]) {
                if (['pie', 'doughnut'].includes(chart.config.type)) {
                    chart.data.datasets[0].borderColor = themeColors.borderColor;
                } else if (chart.config.type === 'line') {
                    chart.data.datasets[0].pointBorderColor = themeColors.borderColor;
                }
            }

            // Update Tooltip details
            if (chart.options.plugins && chart.options.plugins.tooltip) {
                const tooltip = chart.options.plugins.tooltip;
                tooltip.backgroundColor = themeColors.tooltipBg;
                tooltip.borderColor = themeColors.tooltipBorder;
                tooltip.titleColor = themeColors.tooltipTitle;
                tooltip.bodyColor = themeColors.tooltipBody;
            }

            chart.update();
        });
    });
});
