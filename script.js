'use strict'

window.addEventListener('DOMContentLoaded', () => {

    let countVarInput = document.querySelector('[name=countx]');

    countVarInput.addEventListener('change', () => {
        let countVar = countVarInput.value;

        document.querySelector('#coord-body').innerHTML = '';

        for(let i = 1; i <= countVar; ++i){
            let item = getElement('div', ['coord-element'], {
                innerHTML: `<label for="coord${i}">x${i}: </label>
                    <input type="number" name="coord[]">`
            });

            document.querySelector('#coord-body').append(item);
        }
    })

    let btnSubmit = document.querySelector('#submitForm');
    let form = document.querySelector('#nelderMid');

    btnSubmit.addEventListener('click', (e) => {
        e.preventDefault();

        let fData = new FormData(form);

        postData('/admin/api/result', fData, {})
            .then((data) => {
                
            })
            .catch((error) => {
                showUserMessage('Ошибка', error.message, 'error');
            })
    })
})