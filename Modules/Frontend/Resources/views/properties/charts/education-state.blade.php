<div id="educationStatusChart" style="height: 300px; width: 100%;"></div>

@push('scripts')
<script src="https://www.amcharts.com/lib/4/core.js"></script>
<script src="https://www.amcharts.com/lib/4/charts.js"></script>
<script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>
<script>
    am4core.ready(function() {

        am4core.useTheme(am4themes_animated);
        var _isRtl = $('html').attr('dir') == 'rtl';
        var chart = am4core.create("educationStatusChart", am4charts.PieChart);
            chart.innerRadius = am4core.percent(50);
            chart.radius = am4core.percent(75);
            chart.legend = new am4charts.Legend();
            chart.legend.contentAlign = _isRtl ? "right" : "left";
            chart.legend.position = _isRtl ? "right" : "left";
            chart.legend.fontSize = 12;
            chart.legend.reverseOrder = _isRtl;
            chart.legend.itemContainers.template.reverseOrder = _isRtl;
            chart.legend.markers.template.width = 15;
            chart.legend.markers.template.height = 15;
            chart.legend.valueLabels.template.disabled = true;
            // chart.legend.width = 400;
            chart.data = "{{ $educationStatus }}";
            chart.data = JSON.parse(chart.data.replace(/&quot;/g,'"'));
            chart.rtl = _isRtl;
            // chart.exporting.menu = new am4core.ExportMenu();
            // chart.exporting.menu.align = "left";
            // chart.exporting.menu.verticalAlign = "top";

        // var title = chart.titles.create();
        //     title.text = "{{__('cms::areas.custom_fields.demographic_data.education_status.title')}}";
        //     title.fontSize = 14;
        //     title.align = "center";
        //     title.marginBottom = 0;

        var topContainer = chart.chartContainer.createChild(am4core.Container);
            topContainer.layout = "absolute";
            topContainer.toBack();
            topContainer.paddingBottom = 15;
            // topContainer.width = am4core.percent(30);

        var pieSeries = chart.series.push(new am4charts.PieSeries());
            pieSeries.dataFields.value = "count";
            // pieSeries.dataFields.radiusValue = "count";
            pieSeries.dataFields.category = "value";
            pieSeries.slices.template.stroke = am4core.color("#fff");
            pieSeries.slices.template.strokeWidth = 1;
            pieSeries.slices.template.strokeOpacity = 0.8;
            pieSeries.slices.template.cursorOverStyle = [
                {
                    "property": "cursor",
                    "value": "pointer"
                }
            ];
            // series.slices.template.cornerRadius = 6;
            pieSeries.colors.step = 1;

            // pieSeries.hiddenState.properties.endAngle = -90;
            pieSeries.slices.template.states.getKey("hover").properties.scale = 1.02;
            pieSeries.slices.template.states.getKey("hover").properties.shiftRadius = 0.05;
            pieSeries.alignLabels = false;
            // pieSeries.labels.template.bent = true;
            pieSeries.ticks.template.disabled = true;
            pieSeries.alignLabels = false;
            pieSeries.labels.template.text = "";
            // pieSeries.labels.template.radius = am4core.percent(-50);
            // pieSeries.labels.template.fill = am4core.color("white");
            // pieSeries.legendSettings.valueText = "({value}) {value.percent.formatNumber('#.0')}%";
            pieSeries.legendSettings.valueText = "";
            pieSeries.legendSettings.labelText = "";
            pieSeries.ticks.template.adapter.add("hidden", hideSmall);
            pieSeries.labels.template.adapter.add("hidden", hideSmall);

            function hideSmall(hidden, target) {
                return target.dataItem.values.value.percent == 0 ? true : false;
            }

        // var shadow = pieSeries.filters.push(new am4core.DropShadowFilter);
        //     shadow.opacity = 0.2;

        // var hoverState = pieSeries.slices.template.states.getKey("hover");

        // var hoverShadow = hoverState.filters.push(new am4core.DropShadowFilter);
        //     hoverShadow.opacity = 0.7;
        //     hoverShadow.blur = 5;
    });

</script>

@endpush
