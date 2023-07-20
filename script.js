'use strict'

window.addEventListener('DOMContentLoaded', () => {

    let countVarInput = document.querySelector('[name=countx]'), 
        docsBlock = document.querySelector('#docs'),
        taskBlock = document.querySelector('#tasks');

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
                docsBlock.innerHTML = data.html
            })
            .catch((error) => {
                showUserMessage('Ошибка', error.message, 'error');
            })
    })

    let addTask = document.querySelector('#addTask'),
        saveTask = document.querySelector('#saveTask');

    addTask.addEventListener('click', (e) => {
        e.preventDefault();
        let file = document.querySelector('[name=addTask]').files[0];

        if(!file) return;

        let fData = new FormData(document.querySelector('#taskAction'))

        postData('/admin/api/gettask', fData, {})
            .then((data) => {
                for(let i = 1; i <= data.countx; ++i) {
                    let item = getElement('div', ['coord-element'], {
                        innerHTML: `<label for="coord${i}">x${i}: </label>
                            <input type="number" name="coord[]" value="${data.coord[i - 1]}">`
                    });
        
                    document.querySelector('#coord-body').append(item);
                }

                for(let key in data) {
                    if(key == 'coord') continue;
                    document.querySelector('input[name='+key+']').value = data[key];
                }
            })
            .catch((error) => {
                showUserMessage('Ошибка', error.message, 'error');
            })
    })

    saveTask.addEventListener('click', (e) => {
        e.preventDefault();

        let fData = new FormData(form);
        if(!fData.get('countx')) return;

        postData('/admin/api/savetask', fData, {})
            .then((data) => {
                let file = getElement('a', ['file'], {
                    href: data.file,
                    download: data.name,
                    textContent: data.name
                });

                taskBlock.append(file);
            })
            .catch((error) => {
                showUserMessage('Ошибка', error.message, 'error');
            })
    })

    let savetaskBlock = document.querySelector("section.savetask");
    let openSaveTaskBtn = document.querySelector('.btn-open-task');

    openSaveTaskBtn.addEventListener('click', function() {
        savetaskBlock.classList.toggle('open');
    })

    let settingskBlock = document.querySelector(".settings");
    let opensettingsBtn = document.querySelector('.btn-settings');

    opensettingsBtn.addEventListener('click', function() {
        settingskBlock.classList.toggle('open');
    })
})