const cardColor = config.colors.cardColor;
const labelColor = config.colors.textMuted;
const headingColor = config.colors.headingColor;
const borderColor = config.colors.borderColor;
const bodyColor = config.colors.bodyColor;
const legendColor = config.colors.bodyColor;

// Color constant
const chartColors = {
    column: {
        series1: '#826af9',
        series2: '#d2b0ff',
        bg: '#f8d3ff'
    },
    donut: {
        series1: '#fdd835',
        series2: '#32baff',
        series3: '#ffa1a1',
        series4: '#7367f0',
        series5: '#29dac7'
    },
    area: {
        series1: '#ab7efd',
        series2: '#b992fe',
        series3: '#e0cffe'
    }
};

const meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

const type_documents = typeDocumentData();

console.log(type_documents);

const currentDate = new Date();
const months = [];
const series_date = [];

for (let i = 0; i < 12; i++) {
  const date = new Date(currentDate.getFullYear(), currentDate.getMonth() - i, 1);
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  months.push(`${year}-${meses[month - 1]}`);
}
type_documents.map(td => {
    let data = [];
    for (let i = 0; i < 12; i++) {
        const date = new Date(currentDate.getFullYear(), currentDate.getMonth() - i, 1);
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const date_td = td.dates.find(d => (d.month == month && d.year == year));
        data.push(date_td == undefined ? 0 : date_td.total_invoices)
    }
    series_date.push(
        {
            name: td.name,
            data: data.reverse()
        }
    )
})
months.reverse();

const info_dc = {
    abbr: [],
    total: []
}

type_documents.map(i => {
    info_dc.abbr.push(i.code)
    info_dc.total.push(i.total)
})

console.log(info_dc);

const salesCountryChartEl = document.querySelector("#total_documentos"),
    salesCountryChartConfig = {
        chart: {
            type: "bar",
            parentHeightOffset: 0,
            toolbar: {
                show: false,
            },
        },
        series: [
            {
                name: "Documentos",
                data: info_dc.total,
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
            formatter:  (val) => formatPrice(val),
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
            enabled: false,
        },
        legend: {
            show: false,
        },
        colors: [
            config.colors.primary,
            config.colors.info,
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
            categories: info_dc.abbr,
            labels: {
                formatter: function (val) {
                    if (val >= 1000000) {
                        // Formatear para millones
                        return (val / 1000000).toFixed(0) + "M";
                    } else if (val >= 1000) {
                        // Formatear para miles
                        return (val / 1000).toFixed(0) + "K";
                    } else {
                        // Dejarlo en unidades
                        return val.toFixed(0);
                    }
                },
                style: {
                    fontSize: "13px",
                    colors: labelColor,
                    fontFamily: "Inter",
                },
            },
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false,
            },
        },
        yaxis: {
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
if (typeof salesCountryChartEl !== undefined && salesCountryChartEl !== null) {
    const salesCountryChart = new ApexCharts(
        salesCountryChartEl,
        salesCountryChartConfig
    );
    salesCountryChart.render();
}



const areaChartEl = document.querySelector('#lineAreaChart'),
    areaChartConfig = {
      chart: {
        height: 400,
        fontFamily: 'Inter',
        type: 'area',
        parentHeightOffset: 0,
        toolbar: {
          show: false
        }
      },
      dataLabels: {
        enabled: false
      },
      stroke: {
        show: false,
        curve: 'straight'
      },
      legend: {
        show: true,
        position: 'top',
        horizontalAlign: 'start',
        fontSize: '13px',
        markers: {
          width: 10,
          height: 10
        },
        labels: {
          colors: legendColor,
          useSeriesColors: false
        }
      },
      grid: {
        borderColor: borderColor,
        xaxis: {
          lines: {
            show: true
          }
        }
      },
      colors: [chartColors.area.series3, chartColors.area.series2, chartColors.area.series1],
      series: series_date,
      xaxis: {
        categories: months,
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        },
        labels: {
          style: {
            colors: labelColor,
            fontSize: '13px'
          }
        }
      },
      yaxis: {
        labels: {
          style: {
            colors: labelColor,
            fontSize: '13px'
          }
        }
      },
      fill: {
        opacity: 1,
        type: 'solid'
      },
      tooltip: {
        shared: false
      }
    };
  if (typeof areaChartEl !== undefined && areaChartEl !== null) {
    const areaChart = new ApexCharts(areaChartEl, areaChartConfig);
    areaChart.render();
  }

  let map;
  let marker;

  async function initMap() {

    const position = await new Promise((resolve, reject) => {
      navigator.geolocation.getCurrentPosition(resolve, reject);
    });

    const lat = position.coords.latitude;  // Latitud
    const lng = position.coords.longitude; // Longitud
    console.log(`Latitud: ${lat}, Longitud: ${lng}`);
    // Inicializar el mapa en una ubicación predeterminada
    const initialLocation = { lat, lng }; // Bogotá, Colombia
    map = new google.maps.Map(document.getElementById('map'), {
      center: initialLocation,
      zoom: 20,
    });

    // Añadir un marcador al mapa
    marker = new google.maps.Marker({
      position: initialLocation,
      map: map,
      draggable: true, // Permitir arrastrar el marcador
    });

    // Escuchar el evento de arrastre del marcador
    // marker.addListener('dragend', (event) => {
    //   const lat = event.latLng.lat();
    //   const lng = event.latLng.lng();

    //   // Mostrar las coordenadas en la página
    //   document.getElementById('coordinates').innerText =
    //     `Latitud: ${lat}, Longitud: ${lng}`;
    // });

    // Permitir que el usuario coloque el marcador al hacer clic en el mapa
    // map.addListener('click', (event) => {
    //   const clickedLocation = event.latLng;

    //   // Mover el marcador al punto seleccionado
    //   marker.setPosition(clickedLocation);

    //   // Mostrar las coordenadas
    //   document.getElementById('coordinates').innerText =
    //     `Latitud: ${clickedLocation.lat()}, Longitud: ${clickedLocation.lng()}`;
    // });
  }

window.initMap()