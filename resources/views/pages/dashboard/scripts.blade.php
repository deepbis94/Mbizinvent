<script src="{{ asset('public/assets/js/apexcharts.min.js') }}"></script>
<script>
    function renderSalesChart() {
        const chartEl = document.querySelector('#sales-chart');
        if (!chartEl || typeof ApexCharts === 'undefined') {
            return;
        }

        const chart = new ApexCharts(chartEl, {
            chart: {
                type: 'area',
                height: 280,
                toolbar: { show: false },
            },
            series: [{
                name: 'Billed',
                data: @json($chartSeries),
            }],
            xaxis: {
                categories: @json($chartLabels),
                labels: { rotate: -45, rotateAlways: false },
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        return '₹' + Number(value).toLocaleString('en-IN', {
                            maximumFractionDigits: 0,
                        });
                    },
                },
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            colors: ['#0d6efd'],
            tooltip: {
                y: {
                    formatter: function (value) {
                        return '₹' + Number(value).toLocaleString('en-IN', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                        });
                    },
                },
            },
        });

        chart.render();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', renderSalesChart);
    } else {
        renderSalesChart();
    }
</script>
