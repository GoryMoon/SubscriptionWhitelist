<template>
    <line-chart
        :data="getData()"
        :options="options"
    />
</template>
<script>
import { Line } from 'vue-chartjs'
import moment from 'moment'
import LineChart from "./LineChartComponent";


export default {
    extends: Line,
    components: {
        LineChart,
    },
    props: ['data'],
    data() {
        return {
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
                                minute: 'hh:mm'
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
            }
        }
    },
    methods: {
        getData() {
            let localData = JSON.parse(this.data);

            let labels = [];
            for (let i = 47; i >= 0; i--)
                labels.push(moment().add(-i, 'h').seconds(0).minutes(0).format('YYYY-MM-DD HH:mm:ss'));

            let result = [];
            for (let i = localData.length - 1; i >= 0; i--) {
                result.push(localData[i].requests)
            }

            return {
                labels: labels,
                datasets: [
                    {
                        label: 'requests',
                        backgroundColor: '#4b367c',
                        data: result
                    }
                ]
            };
        }
    },
}
</script>
