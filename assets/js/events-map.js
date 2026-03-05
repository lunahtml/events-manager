/**
 * Events Map Scripts
 */
(function ($) {
    'use strict';

    console.log('Events map script loaded');

    class EventsMap {
        constructor() {
            console.log('EventsMap constructor');
            this.initMaps();
        }

        initMaps() {
            console.log('Ищем контейнеры карт...');
            const containers = $('.event-map-container');
            console.log('Найдено контейнеров:', containers.length);

            containers.each((index, container) => {
                const $container = $(container);
                const address = $container.data('address');
                const provider = $container.data('provider');
                const eventId = $container.data('event-id');
                const mapId = `map-${eventId}`;

                console.log(`\n=== Карта #${index + 1} ===`);
                console.log('Event ID:', eventId);
                console.log('Provider:', provider);
                console.log('Address:', address);
                console.log('Map ID:', mapId);

                if (provider === 'google') {
                    this.initGoogleMap(mapId, address);
                } else {
                    this.initYandexMap(mapId, address);
                }
            });
        }

        initYandexMap(mapId, address) {
            console.log('\n--- Инициализация Яндекс.Карты ---');
            console.log('Проверяем загрузку ymaps...');

            if (typeof ymaps === 'undefined') {
                console.error('❌ Яндекс.Карты не загружены!');
                console.log('Проверьте:');
                console.log('1. API ключ в настройках плагина');
                console.log('2. Скрипт загружается в <head>');
                return;
            }

            console.log('✅ Яндекс.Карты загружены, версия:', ymaps.version);

            ymaps.ready(() => {
                console.log('✅ API готов к использованию');

                console.log('Геокодируем адрес:', address);

                ymaps.geocode(address).then(
                    (res) => {
                        console.log('✅ Геокодирование успешно');

                        const firstGeoObject = res.geoObjects.get(0);
                        if (!firstGeoObject) {
                            console.error('❌ Адрес не найден');
                            return;
                        }

                        const coords = firstGeoObject.geometry.getCoordinates();
                        console.log('📌 Координаты:', coords);
                        console.log('📍 Полный адрес:', firstGeoObject.properties.get('text'));

                        // Создаем карту
                        const map = new ymaps.Map(mapId, {
                            center: coords,
                            zoom: 15,
                            controls: ['zoomControl', 'fullscreenControl']
                        });

                        console.log('✅ Карта создана');

                        // Добавляем метку
                        const placemark = new ymaps.Placemark(coords, {
                            balloonContent: address,
                            hintContent: address
                        }, {
                            preset: 'islands#redDotIcon'
                        });

                        map.geoObjects.add(placemark);
                        console.log('✅ Метка добавлена');

                        // Добавляем поиск по карте
                        map.controls.add('searchControl');

                    },
                    (error) => {
                        console.error('❌ Ошибка геокодирования:', error);
                        console.log('💡 Проверьте:');
                        console.log('   1. API ключ корректен');
                        console.log('   2. Адрес существует');

                        // Показываем сообщение об ошибке на карте
                        const mapDiv = document.getElementById(mapId);
                        if (mapDiv) {
                            mapDiv.innerHTML = `<div style="padding: 20px; text-align: center; background: #f8d7da; color: #721c24; border-radius: 8px;">
                                <strong>Ошибка загрузки карты</strong><br>
                                Не удалось найти адрес: ${address}<br>
                                Проверьте API ключ в настройках
                            </div>`;
                        }
                    }
                );
            });
        }

        initGoogleMap(mapId, address) {
            console.log('Google Maps (не используется)');
        }
    }

    $(document).ready(() => {
        console.log('DOM готов, создаем EventsMap');
        new EventsMap();
    });

})(jQuery);