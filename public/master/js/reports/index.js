let cardColor, labelColor, headingColor, borderColor, bodyColor;

const type_documents    = DocumentsData();
const sellers           = sellersData();


if (isDarkStyle) {
    cardColor = config.colors_dark.cardColor;
    labelColor = config.colors_dark.textMuted;
    headingColor = config.colors_dark.headingColor;
    borderColor = config.colors_dark.borderColor;
    bodyColor = config.colors_dark.bodyColor;
} else {
    cardColor = config.colors.cardColor;
    labelColor = config.colors.textMuted;
    headingColor = config.colors.headingColor;
    borderColor = config.colors.borderColor;
    bodyColor = config.colors.bodyColor;
}

function graphicDay() {
    type_documents.map(td => {
        $(`#div_${td.id} h2`).html(td.data_day ? formatPrice(parseFloat(td.data_day.total)): 0)
        $(`#div_${td.id} .inv`).html(`Documentos: ${td.data_day ? td.data_day.total_inv: 0}`)
        $(`#div_${td.id} .cli`).html(`Clientes: ${td.data_day ? td.data_day.total_cus: 0}`)
    })
}

function graphicWeek(data) {
    const total_semanaEl = document.querySelector(`#total_semana`),
        total_semanaConfig = {
            chart: {
                height: 180,
                type: "bar",
                parentHeightOffset: 0,
                toolbar: {
                    show: false,
                },
            },
            plotOptions: {
                bar: {
                    borderRadius: 12,
                    grouped: true,
                    //   distributed: true,
                    columnWidth: "55%",
                    endingShape: "rounded",
                    startingShape: "rounded",
                },
            },
            series: data,
            tooltip: {
                y: {
                    formatter: function (val) {
                        return formatPrice(Math.abs(val));
                    },
                },
            },
            legend: {
                show: false,
            },
            dataLabels: {
                enabled: false,
            },
            colors: data.map((d) => d.color),
            grid: {
                show: false,
                padding: {
                    top: -15,
                    left: -7,
                    right: -4,
                },
            },
            states: {
                hover: {
                    filter: {
                        type: "none",
                    },
                },
                active: {
                    filter: {
                        type: "none",
                    },
                },
            },
            xaxis: {
                axisTicks: {
                    show: false,
                },
                axisBorder: {
                    show: false,
                },
                categories: DiaSemana(),
                labels: {
                    style: {
                        colors: bodyColor,
                    },
                },
            },
            yaxis: { show: false },
            responsive: [
                {
                    breakpoint: 1200,
                    options: {
                        chart: {
                            height: 266,
                        },
                    },
                },
            ],
        };
        
    const total_semana = new ApexCharts(total_semanaEl, total_semanaConfig);
    total_semana.render();
}

function graphicMonth(data, validDays) {

    

    // Filtrar los datos para solo incluir los días válidos
    data.forEach((serie) => {
        serie.data = validDays.map((dayIndex) => serie.data[dayIndex]);
    });

    // Crear etiquetas del eje X solo con los días que tienen datos
    const xCategories = validDays.map((dayIndex) => `${dayIndex + 1} `);

    // Configuración del gráfico
    const shipmentEl = document.querySelector("#shipmentStatisticsChart"),
        shipmentConfig = {
            series: data,
            chart: {
                height: 180,
                type: "line",
                stacked: false,
                parentHeightOffset: 0,
                toolbar: { show: false },
                zoom: { enabled: false },
            },
            markers: false,
            stroke: {
                curve: "smooth",
                width: [0, 3],
                lineCap: "round",
            },
            legend: { show: false },
            grid: {
                strokeDashArray: 8,
                borderColor,
            },
            colors: data.map((d) => d.color),
            fill: {
                opacity: [1, 1],
            },
            plotOptions: {
                bar: {
                    columnWidth: "30%",
                    startingShape: "rounded",
                    endingShape: "rounded",
                    borderRadius: 4,
                },
            },
            dataLabels: { enabled: false },
            xaxis: {
                categories: xCategories, // Usar solo días con datos
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: "13px",
                        fontFamily: "Inter",
                        fontWeight: 400,
                    },
                },
            },
            yaxis: {
                show: false,
                tickAmount: 4,
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: "13px",
                        fontFamily: "Inter",
                        fontWeight: 400,
                    },
                    formatter: function (val) {
                        return formatPrice(val);
                    },
                },
            },
            responsive: [
                {
                    breakpoint: 1400,
                    options: {
                        chart: { height: 200 },
                        xaxis: {
                            labels: {
                                style: {
                                    fontSize: "10px",
                                },
                            },
                        },
                        legend: {
                            itemMargin: {
                                vertical: 0,
                                horizontal: 10,
                            },
                            fontSize: "13px",
                            offsetY: 12,
                        },
                    },
                },
                {
                    breakpoint: 1399,
                    options: {
                        chart: { height: 415 },
                        plotOptions: {
                            bar: {
                                columnWidth: "50%",
                            },
                        },
                    },
                },
                {
                    breakpoint: 982,
                    options: {
                        plotOptions: {
                            bar: {
                                columnWidth: "30%",
                            },
                        },
                    },
                },
                {
                    breakpoint: 480,
                    options: {
                        chart: { height: 250 },
                        legend: { offsetY: 7 },
                    },
                },
            ],
        };


    $('#shipmentStatisticsChart').html("")
    
    const shipment = new ApexCharts(shipmentEl, shipmentConfig);
    shipment.render();
}

function graphicYear(data, currentMonth) {
     // Mes actual (0 = Enero, 11 = Diciembre)
    const months = [
        "Ene",
        "Feb",
        "Mar",
        "Abr",
        "May",
        "Jun",
        "Jul",
        "Ago",
        "Sep",
        "Oct",
        "Nov",
        "Dic",
    ];

    const monthlyBudgetEl = document.querySelector("#monthlyBudgetChart"),
        monthlyBudgetConfig = {
            chart: {
                height: 250,
                type: "area",
                parentHeightOffset: 0,
                offsetY: -8,
                toolbar: { show: false },
            },
            tooltip: { enabled: true },
            dataLabels: { enabled: false },
            stroke: {
                width: 2,
                curve: "smooth",
            },
            series: data, // Usamos los datos generados dinámicamente
            grid: {
                show: false,
                padding: {
                    left: 10,
                    top: 0,
                    right: 12,
                },
            },
            fill: {
                type: "gradient",
                gradient: {
                    opacityTo: 0.7,
                    opacityFrom: 0.5,
                    shadeIntensity: 1,
                    stops: [0, 90, 100],
                },
            },
            xaxis: {
                categories: months.slice(0, currentMonth + 1), // Solo hasta el mes actual
                labels: { show: true },
                axisTicks: { show: false },
                axisBorder: { show: false },
            },
            yaxis: { show: false },
            markers: {
                size: 3,
                strokeWidth: 2,
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return formatPrice(Math.abs(val));
                    },
                },
            },
            responsive: [
                { breakpoint: 1200, options: { chart: { height: 255 } } },
                { breakpoint: 992, options: { chart: { height: 300 } } },
                { breakpoint: 768, options: { chart: { height: 240 } } },
            ],
        };

    $("#monthlyBudgetChart").html("")

    const monthlyBudget = new ApexCharts(monthlyBudgetEl, monthlyBudgetConfig);
    monthlyBudget.render();
}

function graphicTop(data, categories, identification) {
    const salesCountryChartEl = document.querySelector(`#${identification}`),
        salesCountryChartConfig = {
            chart: {
                type: "bar",
                height: 200,
                width: "100%",
                parentHeightOffset: 0,
                toolbar: {
                    show: false,
                },
            },
            series: [
                {
                    name: "Total",
                    data: data,
                },
            ],
            plotOptions: {
                bar: {
                    borderRadius: 10,
                    barHeight: "60%",
                    horizontal: true,
                    distributed: true,
                    startingShape: "rounded",
                    dataLabels: {
                        position: "bottom",
                    },
                },
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return formatPrice(val); // Agrega separadores de miles
                },
                textAnchor: "start",
                offsetY: 8,
                offsetX: 11,
                style: {
                    fontWeight: 500,
                    fontSize: "0.8rem",
                    fontFamily: "Inter",
                },
            },
            tooltip: {
                enabled: true,
                y: {
                    formatter: function (val) {
                        return formatPrice(Math.abs(val));
                    },
                },
            },
            legend: {
                show: false,
            },
            colors: [
                // config.colors.primary,
                // config.colors.success,
                // config.colors.warning,
                config.colors.info,
                config.colors.danger,
            ],
            grid: {
                strokeDashArray: 8,
                borderColor,
                xaxis: { lines: { show: true } },
                yaxis: { lines: { show: false } },
                padding: {
                    top: -18,
                    left: 21,
                    right: 33,
                    bottom: 10,
                },
            },
            xaxis: {
                categories: categories,
                labels: {
                    show:false,
                    formatter: function (val) {
                        if (val >= 1000000) {
                            return (val / 1000000).toFixed(0) + "M";
                        } else if (val >= 1000) {
                            return (val / 1000).toFixed(0) + "K";
                        } else {
                            return val.toFixed(0);
                        }
                    },
                    style: {
                        fontSize: '13px',
                        colors: labelColor,
                        fontFamily: 'Inter'
                      }
                },
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: false,
                },
            },
            yaxis: {
                show: true,
                labels: {
                    style: {
                        fontWeight: 500,
                        fontSize: "0.9375rem",
                        colors: headingColor,
                        fontFamily: "Inter",
                    },
                },
            },
            states: {
                hover: {
                    filter: {
                        type: "none",
                    },
                },
                active: {
                    filter: {
                        type: "none",
                    },
                },
            },
        };



    $(`#${identification}`).html("")

    const salesCountryChart = new ApexCharts(
        salesCountryChartEl,
        salesCountryChartConfig
    );
    salesCountryChart.render();
}

async function sendFilter(e){
    e.preventDefault();
    let filter = {
        seller_id: $('#seller_filter').val()
    }
    let url = base_url(['dashboard/reports/data_index']);

    const { data } = await proceso_fetch(url, filter);
    type_documents.length = 0;

    Array.prototype.push.apply(type_documents, data);

    const seller = sellers.find(s => s.id == $('#seller_filter').val())

    $('.seller-title').html(seller ? seller.name : "")
    
    loadGraphics();

    $('#canvasFilter .btn-close').click();
}

function resetFilter(){
    $('#seller_filter').val(null).trigger('change'); // Limpia Select2
    $("#formFilter").submit();
}

async function loadGraphics(){

    $("#content-semana").html(`<div id="total_semana"></div>`);
    $("#content-mes").html(`<div id="shipmentStatisticsChart"></div>`);
    $("#content-year").html(`<div id="monthlyBudgetChart"></div>`);
    
    // type_documents.map((td) => {
    //     $(`#navs-seller-${td.id}`).html(`<div class="w-100" id="seller-${td.id}"></div>`)
    //     $(`#navs-customer-${td.id}`).html(`<div class="w-100" id="customer-${td.id}"></div>`)
    // })
    
    // await new Promise(resolve => setTimeout(resolve, 1000));

    graphicDay();
    const data_week = []
    const data_month = []
    const data_year = []
    const numDays = new Date().getDate();
    let daySums = Array(numDays).fill(0);

    const today = new Date();
    const currentMonth = today.getMonth();

    type_documents.map((td) => {

        // $(`#navs-seller-${td.id}`).html(`<div id="seller-${td.id}"></div>`)

        const data_sellers = td.sellers.map(s => parseFloat(s.total));
        const data_sellers_names = td.sellers.map(s => s.name.slice(0, 10));
        graphicTop(data_sellers, data_sellers_names, `seller-${td.id}`)

        const data_customers = td.customers.map(c => parseFloat(c.total));
        const data_customers_names = td.customers.map(c => c.name.slice(0, 10));
        graphicTop(data_customers, data_customers_names, `customer-${td.id}`)

        const serie_week = {
            name: `${td.name}`,
            data: [0, 0, 0, 0, 0, 0, 0],
            color: td.color.rgb,
        };
        td.semanal.map((tds) => {
            let dia = obtenerDiaSemana(tds.fecha);
            serie_week.data[dia[0]] = tds.total;
        });
        data_week.push(serie_week);
        $(`.week-type-document-${td.id}`).html(formatPrice(td.semanal.reduce((sum, tds) => sum + parseFloat(tds.total), 0)));

        const serie_month = {
            name: `${td.name}`,
            type: td.id == 1 ? "column" : "line",
            data: Array(numDays).fill(0),
            color: td.color.rgb,
        };

        td.month.forEach((tds) => {
            serie_month.data[tds.fecha - 1] = tds.total;
            daySums[tds.fecha - 1] += parseFloat(tds.total); // Sumar al total del día
        });

        $(`.month-type-document-${td.id}`).html(formatPrice(td.month.reduce((sum, tdm) => sum + parseFloat(tdm.total), 0)));
        data_month.push(serie_month);

        const serie = {
            name: td.name,
            data: Array(12).fill(0), // Inicializa con ceros para cada mes
            color: td.color.rgb,
        };

        td.data_year.forEach((tdy) => {
            const monthIndex = tdy.mes - 1; // Convertimos el mes a índice (0-based)
            if (monthIndex <= currentMonth) {
                serie.data[monthIndex] = parseFloat(tdy.total);
            }
        });

        $(`.year-type-document-${td.id}`).html(formatPrice(td.data_year.reduce((sum, tdy) => sum + parseFloat(tdy.total), 0)));
        data_year.push({
            ...serie,
            data: serie.data.slice(0, currentMonth + 1), // Recortamos hasta el mes actual
        });
    })
    graphicWeek(data_week)


    // Filtrar los días donde todos los valores son 0
    const validDays = daySums
        .map((sum, index) => (sum > 0 ? index : null))
        .filter((index) => index !== null);

    // Si no hay días con datos, no renderizar la gráfica
    if (validDays.length === 0) {
        console.log("No hay datos para mostrar.");
        document.querySelector("#shipmentStatisticsChart").innerHTML =
            "<p>No hay datos disponibles</p>";
    }else graphicMonth(data_month, validDays)
    graphicYear(data_year, currentMonth);
}

$(() => {
    loadGraphics();
    
    $(`#seller_filter`).select2({
        dropdownParent: $('#canvasFilter')
    });
});
