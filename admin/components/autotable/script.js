'use strict'

function addTableTr() {
    
}

window.addEventListener('DOMContentLoaded', function() {
    let table = document.querySelector('.main-table'),
        sqlTable = table.getAttribute('data-table'),
        identityField = table.getAttribute('data-identity'), 
        row = null;

    let listCol = [...table.querySelectorAll('.main-table__th')].map(el => {
        return el.getAttribute('data-key');
    });

    const postModal = new HystModal({
        linkAttributeName: "data-hystmodal",
        beforeOpen: function(modal) {
            let modalStart = modal.starter;
            let nodeId = modalStart.getAttribute('data-id');

            modal.openedWindow.setAttribute('data-node', nodeId);
            
            if(nodeId) {
                let inputs = modal.openedWindow.querySelector('.table-form').querySelectorAll('.form-item');
                row = modalStart.closest('.main-table__item');
        
                let fields = {};
        
                row.querySelectorAll('[data-field]').forEach(element => {
                    let f = element.getAttribute('data-field');
                    fields[f] = element.textContent.replace(/\n/g,'').trim();
                })
        
                inputs.forEach(input => {
                    let f = input.getAttribute('name');

                    if(input.type == 'checkbox'){
                        input.checked = fields[f] == 0 ? false : true;
                    } else {
                        input.value = fields[f] == 'NULL' ? '' : fields[f];
                    }
                });
            } 
        },
        afterClose: function(modal){
            let inputs = modal.openedWindow.querySelector('.table-form').querySelectorAll('.form-item');
            inputs.forEach(input => {
                input.value = '';
            });
        },
    });

    let btnDel = document.querySelector('#btn-delElements');

    let arrIdentity = new Array();

    table.addEventListener('change', (event) => {
        let id = event.target.getAttribute('data-id'),
            pos = arrIdentity.indexOf(id);
        if(pos == -1) {
            arrIdentity.push(id);
        } else {
            arrIdentity.splice(pos, 1);
        }

        console.log(arrIdentity);
    })

    btnDel.addEventListener('click', (e) => {
        if(arrIdentity.length === 0) return;

        let fData = new FormData();

        fData.append('ids', arrIdentity);
        fData.append('table', sqlTable);
        fData.append('identityField', identityField);
        fData.append('method', 'delete');

        postData('/admin/api/table', fData, {})
            .then(data => {
                showUserMessage('Успех', 'Удалено успешно', 'success');

                data.ids.forEach(id => {
                    let row = table.querySelector('.main-table__item[data-id="' + id + '"]');
                    row.remove();
                })   
                
                arrIdentity = [];
            })
            .catch((error) => {
                let cookie = decodeURIComponent(getCookie('query_error'));
                let errorMessage = cookie ? JSON.parse(cookie).message : 'Ошибка при попытке удалить пост.';

                showUserMessage('Ошибка', errorMessage, 'error');
            })
    })

            
    let btnAdd = document.querySelector('#btn-addElement');
        
    btnAdd.addEventListener('click', (e) => {
        e.preventDefault();

        let form = document.querySelector('.table-form'),
            fData = new FormData(form),
            method = 'insert',
            nodeId = document.querySelector('#update-form').getAttribute('data-node');

        if(nodeId !== 'null') {
            method = 'update';
            fData.append('nodeId', nodeId);
        }
        
        fData.append('method', method);
        fData.append('table', sqlTable);
        fData.append('identityField', identityField);

        postData('/admin/api/table', fData, {})
            .then(data => {
                showUserMessage('Успех', 'Сохранение успешно', 'success');
                let inputs = document.querySelector('.table-form').querySelectorAll('.form-item');

                if(nodeId == "null") {
                    row = getElement('tr', ['main-table__item'], {
                        innerHTML: `<td class="main-table__tr">
                        <input type="checkbox" name="check_row" data-id='${data.id}'>
                    </td>`
                    }, {
                        id: data.id
                    });
                }

                console.log(row);

                let inputValues = {};

                inputs.forEach(input => {
                    let f = input.getAttribute('name');
                    let val = input.value ? input.value : "NULL";

                    inputValues[f] = val;
                })

                if(nodeId == "null") {
                    inputValues[identityField] = data.id
                } else {
                    inputValues[identityField] = nodeId;
                }

                listCol.forEach(col => {
                    if(nodeId == "null") {
                        let item = getElement('td', ['main-table__tr'], {
                            textContent: inputValues[col]
                        });
                        
                        row.append(item);
                    } else {
                        row.querySelector('[data-field=' + col +']').textContent = inputValues[col];
                    }
                })

                if(nodeId == "null") {
                    row.innerHTML += `<td 
                        class="main-table__tr edit-btn" 
                        data-id='${data.id}'
                        data-hystmodal='#update-form'
                    >
                        ред.
                    </td>`;

                    table.append(row);
                }
            })
            .catch((error) => {
                showUserMessage('Ошибка', error.message, 'error');
            })
    })
})