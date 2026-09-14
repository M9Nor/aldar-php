<div id="{{$title}}" style="height: 300px; width: 100%;"></div>

@push('scripts')
<script src="https://www.amcharts.com/lib/4/core.js"></script>
<script src="https://www.amcharts.com/lib/4/charts.js"></script>
<script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>
<script>
    am4core.ready(function() {

        am4core.useTheme(am4themes_animated);
        var _isRtl = $('html').attr('dir') == 'rtl';
        var chart = am4core.create("{{$title}}", am4charts.XYChart);

            chart.data = "{{ $priceChange }}";
            chart.data = JSON.parse(chart.data.replace(/&quot;/g,'"'));
            chart.rtl = _isRtl;
            // chart.exporting.menu = new am4core.ExportMenu();
            // chart.exporting.menu.align = "left";
            // chart.exporting.menu.verticalAlign = "top";

            chart.legend = new am4charts.Legend();
            chart.legend.contentAlign = "center";
            chart.legend.position = "bottom";
            chart.legend.fontSize = 12;
            chart.legend.reverseOrder = _isRtl;
            chart.legend.itemContainers.template.reverseOrder = _isRtl;
            chart.legend.markers.template.width = 15;
            chart.legend.markers.template.height = 15;
            chart.legend.valueLabels.template.disabled = true;

            var xAxis = chart.xAxes.push(new am4charts.CategoryAxis())
                xAxis.dataFields.category = 'category'
                xAxis.renderer.cellStartLocation = 0.1
                xAxis.renderer.cellEndLocation = 0.9
                xAxis.renderer.grid.template.location = 0;

            var yAxis = chart.yAxes.push(new am4charts.ValueAxis());
                yAxis.renderer.opposite = _isRtl;
                // yAxis.min = -50;
                // yAxis.max = 200;

            function createSeries(value, name) {
                var series = chart.series.push(new am4charts.ColumnSeries())
                    series.dataFields.valueY = value
                    series.dataFields.categoryX = 'category'
                    series.name = name

                    series.events.on("hidden", arrangeColumns);
                    series.events.on("shown", arrangeColumns);

                var bullet = series.bullets.push(new am4charts.LabelBullet())
                    bullet.interactionsEnabled = true
                    bullet.dy = 10;
                    bullet.label.text = '{valueY}'
                    bullet.label.fill = am4core.color('#ffffff')

                return series;
            }

            // chart.data = [
            //     {
            //         category: 'Place #1',
            //         first: 40,
            //         second: 55,
            //         third: 60
            //     },
            //     {
            //         category: 'Place #2',
            //         first: 30,
            //         second: 78,
            //         third: 69
            //     }
            // ]


            createSeries('last_year', "{{ __('cms::areas.custom_fields.price_change.rent.last_year.label') }}");
            createSeries('last_3_years', "{{ __('cms::areas.custom_fields.price_change.rent.last_3_years.label') }}");
            createSeries('last_5_years', "{{ __('cms::areas.custom_fields.price_change.rent.last_5_years.label') }}");

            function arrangeColumns() {

                var series = chart.series.getIndex(0);

                var w = 1 - xAxis.renderer.cellStartLocation - (1 - xAxis.renderer.cellEndLocation);
                if (series.dataItems.length > 1) {
                    var x0 = xAxis.getX(series.dataItems.getIndex(0), "categoryX");
                    var x1 = xAxis.getX(series.dataItems.getIndex(1), "categoryX");
                    var delta = ((x1 - x0) / chart.series.length) * w;
                    if (am4core.isNumber(delta)) {
                        var middle = chart.series.length / 2;

                        var newIndex = 0;
                        chart.series.each(function(series) {
                            if (!series.isHidden && !series.isHiding) {
                                series.dummyData = newIndex;
                                newIndex++;
                            }
                            else {
                                series.dummyData = chart.series.indexOf(series);
                            }
                        })
                        var visibleCount = newIndex;
                        var newMiddle = visibleCount / 2;

                        chart.series.each(function(series) {
                            var trueIndex = chart.series.indexOf(series);
                            var newIndex = series.dummyData;

                            var dx = (newIndex - trueIndex + middle - newMiddle) * delta

                            series.animate({ property: "dx", to: dx }, series.interpolationDuration, series.interpolationEasing);
                            series.bulletsContainer.animate({ property: "dx", to: dx }, series.interpolationDuration, series.interpolationEasing);
                        })
                    }
                }
            }
    });

</script>

@endpush
