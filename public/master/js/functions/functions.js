$(document).on("focus", "input", function () {
    setTimeout(() => {
        this.scrollIntoView({ block: "center", behavior: "smooth" });
    }, 300);
});


async function proceso_fetch(url, data, time = 1, method = 'POST') {
  console.log([!url.includes(".will"), $.param(data)]);
    toastr.clear();
    const isEmpty = (obj) => {
        return Object.keys(obj).length === 0;
    };
    const valid = isEmpty(data);
    if(!valid && time > 0){
        Swal.fire({
            showConfirmButton: false,
            allowOutsideClick: false,
            customClass: {},
            willOpen: function () {
                Swal.showLoading();
            }
        });
    }
    return fetch(url, {
        method: method,
        headers: { 'Content-Type': url.includes("localhost") ? 'application/x-www-form-urlencoded' : 'application/json' },
        body: url.includes("localhost") ? $.param(data) : JSON.stringify(data)
    }).then(async response => {
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(JSON.stringify({
                msg: errorData.msg || 'Error desconocido',
                title: errorData.title || 'Error en la consulta',
                error: errorData.error || 'Error general'
            }));
        }
        const responseData = await response.json();
        return new Promise(resolve => {
            setTimeout(() => {
                Swal.close();
                resolve(responseData);
            }, !valid ? time : 0);
        });
    }).catch(error => {
        console.log(error.message);
        const error_parse = JSON.parse(error.message);
        console.log(error_parse);
        return new Promise((_, reject) => {
            setTimeout(() => {
                if(error_parse.msg === 'Error desconocido'){
                  Swal.fire({
                    icon:'error',
                    title: error_parse.title,
                    text: error_parse.error,
                    allowOutsideClick: false,
                    customClass: {
                      confirmButton: 'btn btn-primary waves-effect'
                    },
                  })
                }else{
                  Swal.close();
                  alert(error_parse.title, error_parse.msg, 'error');
                }
                reject(error_parse);
            }, !valid ? time : 0);
        });
    });
}

function proceso_fetch_get(url) {
  return fetch(url).then(response => {
      if (!response.ok) throw Error(response.status);
      return response.json();
  }).catch(error => {
    console.error(error);
  });
}

function alert(title = 'Alert', msg = 'Alert', icon = 'success', time=0, maxOpened = 5){
  var shortCutFunction = icon,
      prePositionClass = 'toast-top-right';

  prePositionClass =
      typeof toastr.options.positionClass === 'undefined' ? 'toast-top-right' : toastr.options.positionClass;
  toastr.options = {
      maxOpened,
      autoDismiss: true,
      closeButton: true,
      newestOnTop: true,
      progressBar: false,
      preventDuplicates: true,
      timeOut: time,             // Duración en milisegundos (0 significa que no se cierra automáticamente)
      extendedTimeOut: time,
      onclick: null,
      tapToDismiss: true,
  };
  var $toast = toastr[shortCutFunction](msg, title); // Wire up an event handler to a button in the toast, if it exists
  if (typeof $toast === 'undefined') {
    return;
  }
}

function base_url(array = []) {
  var url = localStorage.getItem('url');
  if (array.length == 0) return `${url}`;
  else return `${url}${array.join('/')}`;
}

const separador_miles = (numero) => {
  const formatter = new Intl.NumberFormat('es-CO', {
      style: 'decimal',
      minimumFractionDigits: 2,
  });
  return formatter.format(numero);
};

const format_number = (numero) => {
  return parseFloat(numero.replace(/[a-zA-Z]/g, '').replace(/\./g, '').replace(',', '.'));
}

function updateFormattedValue(input) {
  let value = input.value;

  // Remover letras, puntos de miles y convertir coma decimal a punto
  value = value.replace(/[a-zA-Z]/g, '').replace(/\./g, '').replace(',', '.');

  // Convertir el valor en número flotante
  let numericValue = parseFloat(value);

  if (!isNaN(numericValue)) {
      // Formatear el valor como número con separadores de miles
      const formattedValue = separador_miles(numericValue);

      // Posición del cursor antes de actualizar el valor
      const cursorPosition = input.selectionStart;

      // Actualizar el valor del input
      input.value = formattedValue;

      // Restaurar la posición del cursor
      setTimeout(() => {
          input.setSelectionRange(cursorPosition, cursorPosition);
      }, 0);
  }
}


function formatPrice(price){
    const formatter = new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 2
    })
    return formatter.format(price)
}

function loadSelect () {
  const selectPicker = $('.selectpicker'),
    select2 = $('.select2'),
    select2Icons = $('.select2-icons');

  // Bootstrap Select
  // --------------------------------------------------------------------
  if (selectPicker.length) {
    selectPicker.selectpicker();
    handleBootstrapSelectEvents();
  }

  // Select2
  // --------------------------------------------------------------------

  // Default
  if (select2.length) {
    select2.each(function () {
      var $this = $(this);
      select2Focus($this);
      $this.wrap('<div class="position-relative"></div>').select2({
        dropdownParent: $this.parent()
      });
    });
  }

  // Select2 Icons
  if (select2Icons.length) {
    // custom template to render icons
    function renderIcons(option) {
      if (!option.id) {
        return option.text;
      }
      var $icon = "<i class='" + $(option.element).data('icon') + " me-2'></i>" + option.text;

      return $icon;
    }
    select2Focus(select2Icons);
    select2Icons.wrap('<div class="position-relative"></div>').select2({
      dropdownParent: select2Icons.parent(),
      templateResult: renderIcons,
      templateSelection: renderIcons,
      escapeMarkup: function (es) {
        return es;
      }
    });
  }
};

async function coordenadas(){
  const position = await new Promise((resolve, reject) => {
    navigator.geolocation.getCurrentPosition(resolve, reject);
  });
  return {
    lat:position.coords.latitude,
    lng:position.coords.longitude
  }
}

async function view_map_detail(coord){
  coord = coord.split(',');
  Swal.fire({
      title: 'Ubicación',
      html:`
          <div style="height:300px;width:100%" id="map"></div>
      `,
      customClass: {
          confirmButton: "btn btn-primary",
      },
      didOpen: () => {
          map = new google.maps.Map(document.getElementById('map'), {
              center: {lat: parseFloat(coord[0].trim()), lng: parseFloat(coord[1].trim())},
              zoom: 20,
          });
          marker = new google.maps.Marker({
              position: {lat: parseFloat(coord[0].trim()), lng: parseFloat(coord[1].trim())},
              map: map,
              draggable: false, // Permitir arrastrar el marcador
          });
      }
  });
}

function downloadBase64(fileContent, fileName, type){
  const blob = new Blob([Uint8Array.from(atob(fileContent), c => c.charCodeAt(0))], {
    type,
  });
  const url = window.URL.createObjectURL(blob);

  const a = document.createElement('a');
  a.href = url;
  a.download = fileName;
  document.body.appendChild(a);
  a.click();
  a.remove();

  window.URL.revokeObjectURL(url);
}

function loadSelectProducts(){
    var $this = $('#products_id');
    select2Focus($this);
    $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: "Seleccione un producto",
        matcher: function(params, data) {
            if ($.trim(params.term) === '') {
                return data;
            }
            const term = params.term.toLowerCase();
            if (user.role_id == 3) { // Busqueda por codigo solo para los cotizadores
                if (data.text.toLowerCase().startsWith(term)) {
                    return data;
                }
            } else {
                if (data.text.toLowerCase().includes(term)) {
                    return data;
                }
            }
            return null;
        },
        language: {
            noResults: function() {
                return "No hay coincidencias desde el inicio";
            }
        },
        dropdownParent: $this.parent()
    });
}