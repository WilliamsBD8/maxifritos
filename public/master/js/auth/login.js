async function onSubmit(event) {
  event.preventDefault();
  $('#card-error').hide();
  const url = base_url(['validation']);
  const data = {
    email_username: $('#email-username').val(),
    password: $('#password').val(),
    captcha: $('#captcha').val()
  }
  if(data.email_username == '' || data.password == '' || data.captcha == ''){
    $('#card-error h5').html('Campos obligatorios.');
    $('#card-error p').html('Por favor revisa que los campos no esten vacios.');
    return $('#card-error').show();
  }
  await proceso_fetch(url, data).then(respond => {
    alert('Credenciales confirmadas', msg = 'Será redireccinado al Home')
    setTimeout(() => {
      window.location.href = respond.url;
    }, 2000);
  });
}