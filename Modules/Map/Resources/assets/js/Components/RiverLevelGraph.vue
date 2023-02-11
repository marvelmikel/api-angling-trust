<template>
    <div class="river-level-graph">
        <div id="chart" style="height: 350px"></div>
    </div>
</template>

<script>
    import * as am4core from "@amcharts/amcharts4/core";
    import * as am4charts from "@amcharts/amcharts4/charts";

    export default {
        props: ['readings', 'stats'],

        mounted() {
            this.chart = am4core.create("chart", am4charts.XYChart);

            let data = [];

            this.readings.forEach((reading, index) => {
                if (this.stats.typical_range) {
                    data.push({
                        date: reading.recorded_at,
                        value: reading.value,
                        typical_range_high: this.stats.typical_range.high,
                        typical_range_low: this.stats.typical_range.low
                    });
                } else {
                    data.push({
                        date: reading.recorded_at,
                        value: reading.value
                    });
                }
            });

            this.chart.data = data;

            this.chart.dateFormatter.inputDateFormat = "yyyy-MM-dd HH:mm:ss";

            let dateAxis = this.chart.xAxes.push(new am4charts.DateAxis());
            let valueAxis = this.chart.yAxes.push(new am4charts.ValueAxis());

            dateAxis.title.text = 'Date';
            valueAxis.title.text = 'Meters';

            if (this.stats.typical_range) {
                let typicalRange = this.chart.series.push(new am4charts.LineSeries());
                typicalRange.dataFields.openValueY = "typical_range_high";
                typicalRange.dataFields.valueY = "typical_range_low";
                typicalRange.dataFields.dateX = "date";
                typicalRange.strokeWidth = 2;
                typicalRange.strokeDasharray = "3,3";
                typicalRange.fillOpacity = 0.1;
                typicalRange.fill = "#FFFFFF";
                typicalRange.stroke = "#FFFFFF";

                let typicalRangeHigh = this.chart.series.push(new am4charts.LineSeries());
                typicalRangeHigh.dataFields.valueY = "typical_range_high";
                typicalRangeHigh.dataFields.dateX = "date";
                typicalRangeHigh.strokeWidth = 2;
                typicalRangeHigh.strokeDasharray = "3,3";
                typicalRangeHigh.fill = "#FFFFFF";
                typicalRangeHigh.stroke = "#FFFFFF";
            }

            let series = this.chart.series.push(new am4charts.LineSeries());
            series.dataFields.valueY = "value";
            series.dataFields.dateX = "date";
            series.tooltipText = '{value}';
            series.strokeWidth = 2;
            series.fillOpacity = 0.5;
            series.minBulletDistance = 15;
            series.stroke = "#fff";
            series.fill = "#fff";

            series.tooltip.background.cornerRadius = 20;
            series.tooltip.background.strokeOpacity = 0;
            series.tooltip.pointerOrientation = "vertical";
            series.tooltip.label.minWidth = 40;
            series.tooltip.label.minHeight = 40;
            series.tooltip.label.textAlign = "middle";
            series.tooltip.label.textValign = "middle";

            let bullet = series.bullets.push(new am4charts.CircleBullet());
            bullet.circle.strokeWidth = 2;
            bullet.circle.radius = 4;
            bullet.circle.fill = am4core.color("#fff");

            let bullethover = bullet.states.create("hover");
            bullethover.properties.scale = 1.3;

            this.chart.cursor = new am4charts.XYCursor();
            this.chart.cursor.behavior = "panXY";
            this.chart.cursor.xAxis = dateAxis;
            this.chart.cursor.snapToSeries = series;

            this.chart.scrollbarY = new am4core.Scrollbar();
            this.chart.scrollbarY.parent = this.chart.leftAxesContainer;
            this.chart.scrollbarY.toBack();

            this.chart.scrollbarX = new am4charts.XYChartScrollbar();
            this.chart.scrollbarX.series.push(series);
            this.chart.scrollbarX.parent = this.chart.bottomAxesContainer;

            // dateAxis.start = 0.79;
            dateAxis.keepSelection = true;
        },

        beforeDestroy() {
            this.chart.dispose();
        },

        data() {
            return {
                chart: null
            }
        }
    }
</script>
