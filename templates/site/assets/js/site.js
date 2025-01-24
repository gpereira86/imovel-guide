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

    console.log(cpf, cpf.length)
    if (cpf.length < 14) {
        e.preventDefault();
        alert('O número do CPF deve ter pelo menos 11 números.');
        return false;
    }

    if (nome.length < 2) {
        e.preventDefault();
        alert('O nome deve ter pelo menos 2 caracteres.');
        return false;
    }

    if (creci.length < 2) {
        e.preventDefault();
        alert('O número do Creci deve ter pelo menos 2 caracteres.');
        return false;
    }

});

function clearFormAndRedirect(url) {

    let form = document.getElementById('formData');
    form.reset();

    window.location.href = url;
}