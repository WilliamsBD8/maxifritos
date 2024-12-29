async function proceso_fetch(url, data, time = 2000, method = 'POST') {
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
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
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
                Swal.close();
                alert(error_parse.title, error_parse.msg, 'error');
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

function alert(title = 'Alert', msg = 'Alert', icon = 'success', time=0){
  var shortCutFunction = icon,
      prePositionClass = 'toast-top-right';

  prePositionClass =
      typeof toastr.options.positionClass === 'undefined' ? 'toast-top-right' : toastr.options.positionClass;
  toastr.options = {
      maxOpened: 5,
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
    let partesNumero = numero.toString().split('.');
    partesNumero[0] = partesNumero[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    return partesNumero.join('.')
}
  
function updateFormattedValue(input) {
    var value = input.value
    value = value.replace(/[a-zA-Z]/g, '').replace(/,/g, '');
    const formattedValue = separador_miles(value);
    input.value = formattedValue;
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