define([
    'Magento_Ui/js/form/element/abstract',
    'ko'
], function (Element, ko) {
    'use strict';

    return Element.extend({
        defaults: {
            template: 'Perspective_ImageAiVariations/form/element/canvas'
        },
        size: ko.observable(),
        color: ko.observable(),

        /**
         * Invokes initialize method of parent class,
         * contains initialization logic
         */
        initialize: function () {
            this._super();
            return this;
        },
        onRender: function (element) {
            const fileInput = document.querySelector("#originalImage");
            this.drawOnImage();

            fileInput.addEventListener("change", async (e) => {
                const [file] = fileInput.files;

                // displaying the uploaded image
                const image = document.createElement("img");
                image.src = await this.fileToDataUri(file);

                // enabling the brush after after the image
                // has been uploaded
                image.addEventListener("load", () => {
                    this.drawOnImage(image);
                });

                return false;
            });
            const sizeElement = document.querySelector("#sizeRange");
            this.size(sizeElement.value);
            sizeElement.oninput = (e) => {
                this.size(e.target.value);
            };

            const colorElement = document.getElementsByName("colorRadio");

            colorElement.forEach((c) => {
                if (c.checked) this.color(c.value);
            });

            colorElement.forEach((c) => {
                c.onclick = () => {
                    this.color(c.value);
                };
            });
        },
        drawOnImage: function (image = null) {
            const canvasElement = document.getElementById(this.uid);
            const context = canvasElement.getContext("2d");

            // if an image is present,
            // the image passed as parameter is drawn in the canvas
            if (image) {
                image.height = 1024;
                image.width = 1024;

                const imageWidth = image.width;
                const imageHeight = image.height;
                // rescaling the canvas element
                canvasElement.width = imageWidth;
                canvasElement.height = imageHeight;

                context.drawImage(image, 0, 0, 1024, 1024);
            }

            const clearElement = document.getElementById("clear");
            clearElement.onclick = () => {
                context.clearRect(0, 0, canvasElement.width, canvasElement.height);
                document.querySelector("#originalImage").value = "";
            };

            let isDrawing;

            canvasElement.onmousedown = (e) => {
                isDrawing = true;
                const rect = canvasElement.getBoundingClientRect();
                const mx = e.clientX;// - rect.left;  // Коригувати позицію миші по осі x
                const my = e.clientY;// - rect.top;   // Коригувати позицію миші по осі y
                context.beginPath();
                context.lineWidth = this.size();
                context.strokeStyle = this.color();
                context.lineJoin = "round";
                context.lineCap = "round";
                context.moveTo(mx, my);
            };

            canvasElement.onmousemove = (e) => {
                if (isDrawing) {
                    const rect = canvasElement.getBoundingClientRect();
                    const mx = e.clientX;//- rect.left;  // Коригувати позицію миші по осі x
                    const my = e.clientY;//- rect.top;   // Коригувати позицію миші по осі y
                    context.lineTo(mx, my);
                    context.stroke();
                }
            };

            canvasElement.onmouseup = function () {
                isDrawing = false;
                context.closePath();
            };
        },
        fileToDataUri: function (field) {
            return new Promise((resolve) => {
                const reader = new FileReader();

                reader.addEventListener("load", () => {
                    resolve(reader.result);
                });

                reader.readAsDataURL(field);
            });
        },
        generateData: function () {
            const canvas = document.getElementById(this.uid);
            canvas.toBlob(function (blob) {
                const fileInput = document.getElementById('originalImage');
                const file = fileInput.files[0];

                // Створюємо об'єкт FormData та додаємо до нього зображення та файл
                const formData = new FormData();
                formData.append('canvasImage', blob, 'canvas_image.png');
                formData.append('originalImage', file);

                // Здійснюємо запит на сервер для завантаження зображення та файлу
                const xhr = new XMLHttpRequest();
                const url = '/rest/V1/generate/edits'; // Замініть на свій URL
                xhr.open('POST', url, true);
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            console.log('Завантажено успішно:', xhr.responseText);
                        } else {
                            console.error('Помилка завантаження:', xhr.status, xhr.statusText);
                        }
                    }
                };
                xhr.send(formData);
            }, 'image/png');
        }

    });
});
