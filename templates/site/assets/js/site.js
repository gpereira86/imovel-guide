document.getElementById('inputCpf').addEventListener('input', function (e) {
    let value = e.target.value.replace(/\D/g, '');
    value = value.slice(0, 11);
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    e.target.value = value;
});

document.querySelector('form').addEventListener('submit', function (e) {

    let cpf = document.getElementById('inputCpf').value;
    let nome = document.getElementById('inputName').value;
    let creci = document.getElementById('inputCreci').value;

});

function clearFormAndRedirect(url) {

    let form = document.getElementById('formData');
    form.reset();

    window.location.href = url;
}

