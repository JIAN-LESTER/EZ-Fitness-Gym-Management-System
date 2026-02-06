<!-- resources/views/components/lazy-chart.blade.php -->
<div x-data="lazyChart('{{ $chartId }}', '{{ $endpoint }}')" 
     x-init="init"
     class="relative">
    
    <!-- Loading State -->
    <div x-show="loading" class="flex items-center justify-center h-64">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
            <p class="text-sm text-gray-600 mt-3">Loading chart...</p>
        </div>
    </div>

    <!-- Error State -->
    <div x-show="error" class="flex items-center justify-center h-64">
        <div class="text-center">
            <svg class="w-12 h-12 text-red-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-gray-600">Failed to load chart</p>
            <button @click="retry" class="mt-2 text-blue-500 hover:text-blue-700 text-sm font-medium">
                Retry
            </button>
        </div>
    </div>

    <!-- Chart Canvas -->
    <div x-show="!loading && !error" x-cloak>
        <canvas :id="chartId"></canvas>
    </div>
</div>

<script>
function lazyChart(chartId, endpoint) {
    return {
        chartId: chartId,
        endpoint: endpoint,
        loading: true,
        error: false,
        chart: null,
        observer: null,

        init() {
            // Use Intersection Observer for lazy loading
            this.observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && this.loading) {
                        this.loadChart();
                        this.observer.disconnect(); // Load only once
                    }
                });
            }, {
                rootMargin: '50px' // Start loading slightly before visible
            });

            this.observer.observe(this.$el);
        },

        async loadChart() {
            try {
                const response = await fetch(this.endpoint);
                if (!response.ok) throw new Error('Failed to load chart data');
                
                const data = await response.json();
                this.renderChart(data);
                this.loading = false;
            } catch (err) {
                console.error('Chart loading error:', err);
                this.error = true;
                this.loading = false;
            }
        },

        renderChart(data) {
            const ctx = document.getElementById(this.chartId).getContext('2d');
            this.chart = new Chart(ctx, data.config);
        },

        retry() {
            this.error = false;
            this.loading = true;
            this.loadChart();
        }
    }
}
</script>

<style>
[x-cloak] { 
    display: none !important; 
}
</style>