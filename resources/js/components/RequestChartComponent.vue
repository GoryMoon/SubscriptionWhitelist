<template>
    <div>
        <line-chart
                ref="line_chart"
                id="request-chart"
                :data="getData()"
                xkey="time"
                ykeys='["requests"]'
                grid="true"

        ></line-chart>
    </div>
</template>
<script>
import { LineChart } from 'vue-morris'
import moment from 'moment'


export default {
    components: {
        LineChart
    },
    props: ['data'],
    mounted() {
        window.addEventListener('resize', _.debounce(this.handleResize, 100, { leading: false, trailing: true}));
    },
    methods: {
        getData() {
            let localData = JSON.parse(this.data);
            let result = [];
            for (let i = 0; i < localData.length; i++) {
                let time = moment(localData[i].time, "YYYY-M-DTH:m:sZ").format("YYYY-MM-DD HH:mm:ss");
                let requests = localData[i].requests;
                result.push({
                    time: time,
                    requests: requests
                })
            }

            return result;
        },
        handleResize() {
            this.$refs.line_chart.chart.redraw();
        }
    }
}
</script>