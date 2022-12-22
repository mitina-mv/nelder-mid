'use strict'

async function getData(url = '') {
    const response = await fetch(url);
    if (!response.ok) {
        throw new Error(`Ошибка по адресу ${url} статус ${response.status}`)
    }
    return await response.json();
}

async function postData(url = '', data = {}, headers = {
    'Content-Type': 'application/json'
}) {
    const response = await fetch(url, {
        method: 'POST',
        mode: 'cors',
        cache: 'no-cache',
        credentials: 'same-origin',
        headers: headers,
        redirect: 'follow',
        referrerPolicy: 'no-referrer',
        body: data
    });
    if (!response.ok) {
        throw new Error(`Ошибка по адресу ${url} статус ${response.status}`)
    }
    return await response.json();
}


const getElement = (tagName, classNames, attrs, dataAttrs, content) => {
    const element = document.createElement(tagName);

    if (classNames)
        element.classList.add(...classNames);

    if (attrs) {
        for (let attr in attrs) {
            element[attr] = attrs[attr];
        }
    }

    if (dataAttrs) {
        for (let attr in dataAttrs) {
            element.dataset[attr] = dataAttrs[attr];
        }
    }

    if (content)
        element.innerHTML = content;

    return element;
}

const getCookie = (cname) => {
    let name = cname + "=";
    let ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i].trim();

        if (c.indexOf(name) == 0)
            return c.substring(name.length, c.length);
    }

    return null;
}

const showUserMessage = (title, info, status) => {
    let mes = getElement('div', ['message', 'message_' + status], {
        innerHTML: `<div class="message-head">${title}</div>
        <div class="message-info">${info}</div>
        <div class="message-close">x</div>
        `
    });

    document.body.append(mes);
    setTimeout(() => mes.remove(), 3000);
}


const getCookieError = () => {
    let cookie = decodeURIComponent(getCookie('query_error'));
    let errorMessage = cookie ? JSON.parse(cookie).message : false;

    return errorMessage;
}

window.addEventListener('DOMContentLoaded', () => {
    let fileInput = document.querySelectorAll('.input__load-file');

    fileInput.forEach(item => {
        item.addEventListener('change', function(e) {
            if (this.files[0]) {
                let fr = new FileReader();

                let name = this.name,
                    fileLoadBlock = document.querySelector(`label.input__load-file-block[for=${name}]`);
            
                fr.addEventListener("load", function () {
                    fileLoadBlock.style.backgroundImage = "url(" + fr.result + ")";
                    fileLoadBlock.querySelector('span').style.color = 'transparent';
                }, false);
            
                fr.readAsDataURL(this.files[0]);
            }
        })
    })
})