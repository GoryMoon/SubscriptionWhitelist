<template>
    <div>
        <div v-if="loaded">
            <line-chart
                :chart-data="chartData"
                :options="options"
            />
            <b-progress :value="refreshCounter" max="300" height="2px" class="mt-2"></b-progress>
        </div>
        <div v-if="!loaded" class="d-flex justify-content-center mb-5 py-5">
            <b-spinner
                style="width: 3rem; height: 3rem;"
                label="Loading chart..."
            />
        </div>
    </div>
</template>
<script>
import {Line} from 'vue-chartjs'
import LineChart from "./LineChartComponent";
import api from '../ky'

export default {
    extends: Line,
    components: {
        LineChart,
    },
    data() {
        return {
            timer: '',
            loaded: false,
            chartData: null,
            refreshCounter: 0,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            min: 0,
                            stepSize: 1,
                        }
                    }],
                    xAxes: [{
                        type: 'time',
                        distribution: 'series',
                        time: {
                            unit: 'minute',
                            displayFormats: {
                                minute: 'HH:mm'
                            }
                        }
                    }]
                },
                legend: {
                    display: false,
                },
                tooltips: {
                    intersect: false
                }
            },
        }
    },
    mounted() {
        this.loaded = false;
        this.getData();
        this.enableUpdateInterval();
    },
    methods: {
        getData() {
            api.get('', {searchParams: [['hours', 48]]}).then(async value => {
                let data = await value.json();
                this.setChartData(data);
                this.loaded = true;
            });
        },
        setChartData(data) {
            this.chartData = {
                datasets: [
                    {
                        label: 'requests',
                        backgroundColor: '#4b367c',
                        data: data,
                    }
                ]
            }
        },
        disableUpdateInterval() {
            clearInterval(this.timer);
            this.timer = '';
        },
        enableUpdateInterval() {
            this.timer = setInterval(this.updateRefreshCounter, 200);
        },
        updateRefreshCounter() {
            this.refreshCounter++;
            if (this.refreshCounter >= 300) { // 60 seconds = 60000 mx / 200 ms
                this.updateList();
                this.refreshCounter = 0;
            }
        },
        updateList() {
            if (this.chartData != null) {
                api.get('', {searchParams: [['hours', 2]]}).then(async value => {
                    let data = await value.json();
                    let oldData = this.chartData.datasets[0].data;

                    for (let point of data) {
                        let updated = false;
                        for (let i = 0; i < oldData.length; i++) {
                            let oldPoint = oldData[i];
                            if (oldPoint.x === point.x)
                            {
                                oldData[i].y = point.y;
                                updated = true;
                                break;
                            }
                        }
                        if (!updated) {
                            oldData = _.concat(_.drop(oldData, 1), point);
                        }
                    }

                    this.setChartData(oldData);
                });
            }
        }
    },
    beforeDestroy() {
        this.disableUpdateInterval();
    },
}
</script>
