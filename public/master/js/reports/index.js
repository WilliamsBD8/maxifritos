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
        if(td.data_day != null){
            $(`.total_day_pri_${td.id}`).html(formatPrice(parseFloat(td.data_day.total)))
            $(`.total_day_inv_${td.id}`).html(td.data_day.total_inv)
        }
    })
}

function graphicWeek() {
    const data = [];
    type_documents.map((td) => {
        const serie = {
            name: `${td.name}`,
            data: [0, 0, 0, 0, 0, 0, 0],
            color: td.color.rgb,
        };
        let total = 0;
        td.semanal.map((tds) => {
            let dia = obtenerDiaSemana(tds.fecha);
            serie.data[dia[0]] = tds.total;
            total += parseFloat(tds.total);
        });
        $(`.week-type-document-${td.id}`).html(formatPrice(total));
        data.push(serie);
    });
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
    if (typeof total_semanaEl !== undefined && total_semanaEl !== null) {
        const total_semana = new ApexCharts(total_semanaEl, total_semanaConfig);
        total_semana.render();
    }
}

function graphicMonth() {
    const data = [];
    const numDays = new Date().getDate();
    let daySums = Array(numDays).fill(0); // Array para verificar qué días tienen datos

    type_documents.forEach((td) => {
        const serie = {
            name: `${td.name}`,
            type: td.id == 1 ? "column" : "line",
            data: Array(numDays).fill(0),
            color: td.color.rgb,
        };

        let total = 0;

        td.month.forEach((tds) => {
            serie.data[tds.fecha - 1] = tds.total;
            total += parseFloat(tds.total);
            daySums[tds.fecha - 1] += parseFloat(tds.total); // Sumar al total del día
        });

        $(`.month-type-document-${td.id}`).html(formatPrice(total));
        data.push(serie);
    });

    // Filtrar los días donde todos los valores son 0
    const validDays = daySums
        .map((sum, index) => (sum > 0 ? index : null))
        .filter((index) => index !== null);

    // Si no hay días con datos, no renderizar la gráfica
    if (validDays.length === 0) {
        console.log("No hay datos para mostrar.");
        document.querySelector("#shipmentStatisticsChart").innerHTML =
            "<p>No hay datos disponibles</p>";
        return;
    }

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

    if (shipmentEl !== null) {
        const shipment = new ApexCharts(shipmentEl, shipmentConfig);
        shipment.render();
    }
}

function graphicYear() {
    const data = [];
    let hasData = false; // Bandera para saber si hay datos válidos
    const today = new Date();
    const currentMonth = today.getMonth(); // Mes actual (0 = Enero, 11 = Diciembre)
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

    type_documents.forEach((td) => {
        let serie = {
            name: td.name,
            data: Array(12).fill(0), // Inicializa con ceros para cada mes
            color: td.color.rgb,
        };

        let total = 0;
        td.data_year.forEach((tdy) => {
            const monthIndex = tdy.mes - 1; // Convertimos el mes a índice (0-based)
            if (monthIndex <= currentMonth) {
                serie.data[monthIndex] = parseFloat(tdy.total);
                total += parseFloat(tdy.total);
            }
        });

        if (total > 0) hasData = true; // Si hay algún total > 0, activamos la bandera

        $(`.year-type-document-${td.id}`).html(formatPrice(total));
        data.push({
            ...serie,
            data: serie.data.slice(0, currentMonth + 1), // Recortamos hasta el mes actual
        });
    });

    if (!hasData) {
        document.querySelector("#monthlyBudgetChart").innerHTML =
            "<p>No hay datos disponibles</p>";
        return;
    }

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

    if (monthlyBudgetEl !== null) {
        const monthlyBudget = new ApexCharts(monthlyBudgetEl, monthlyBudgetConfig);
        monthlyBudget.render();
    }
}

function graphicCustomer() {
    type_documents.map(td => {

        const data = td.customers.map(customer => parseFloat(customer.total));
        const data_names = td.customers.map(customer => customer.name);

        const salesCountryChartEl = document.querySelector(`#customer-${td.id}`),
            salesCountryChartConfig = {
                chart: {
                    type: "bar",
                    height: 250,
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
                        fontSize: "0.9375rem",
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
                    config.colors.primary,
                    config.colors.success,
                    config.colors.warning,
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
                    categories: data_names,
                    labels: {
                        show: false,
                    },
                    axisBorder: {
                        show: false,
                    },
                    axisTicks: {
                        show: false,
                    },
                },
                yaxis: {
                    show: false,
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
        if (
            typeof salesCountryChartEl !== undefined &&
            salesCountryChartEl !== null
        ) {
            const salesCountryChart = new ApexCharts(
                salesCountryChartEl,
                salesCountryChartConfig
            );
            salesCountryChart.render();
        }
    })
}

async function sendFilter(e){
    e.preventDefault();
    let filter = {
        seller_id: $('#seller_filter').val()
    }
    let url = base_url(['dashboard/reports/data_index']);

    const {data} = await proceso_fetch(url, filter);
    type_documents.length = 0;
    type_documents.push(...data);

    const seller = sellers.find(s => s.id == $('#seller_filter').val())

    if(seller)
        $('.title-report').html(`Reporte General: ${seller.name}`)

    $('#canvasFilter .btn-close').click();
    graphicWeek();
    graphicDay();
    graphicMonth();
    graphicYear();
    graphicCustomer();
}

// let data = ;
// td.semanal.map(tds => {
//     let dia = obtenerDiaSemana(tds.fecha);
//     data[dia[0]] = tds.total
// })

window.onload = () => {
    graphicWeek();
    graphicDay();
    graphicMonth();
    graphicYear();
    graphicCustomer();
};
