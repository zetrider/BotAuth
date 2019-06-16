# BotAuth - вход и регистрация при помощи ботов

Пакет позволяет реализовать аутентификацию при помощи ботов в соц. сетях.
Основная задача упростить аутентификацию для пользователей, которые используют мобильные устройства для входа на сайт через соц. сеть.

Ссылки вида:

* https://vk.me/...
* https://t.me/...
* https://m.me/...

откроют мобильное приложение для начала диалога с ботом. Посетителю не придется повторно вводить логин и пароль в браузере.

Возможно подключить ботов:

* Вконтакте
* Telegram
* FaceBook
* Ваш собственный провайдер (пример ниже)

[Demo https://laravel.zetrider.ru/](https://laravel.zetrider.ru/)
____

## Установка:

1. composer require zetrider/botauth

2. Подключить пакет в config/app.php
    * Провайдер
    ```ZetRider\BotAuth\BotAuthServiceProvider::class,```
    * Фасад (Алиас)

    ```'BotAuth' => ZetRider\BotAuth\Facades\BotAuth::class,```

3. Скопировать конфиг. файл

   ``` php artisan vendor:publish --tag=botauth-config ```

   при необходимости

   ``` php artisan vendor:publish --tag=botauth-views ```

   ``` php artisan vendor:publish --tag=botauth-migrations ```

4. Указать для нужных соц. сетей ссылку в параметре link.

    * https://vk.me/...
    * https://t.me/...
    * https://m.me/...

5. Заполнить ENV файл ключами ботов
   ```BOTAUTH_VKONTAKTE_API_SECRET```

    ```BOTAUTH_VKONTAKTE_API_TOKEN```

    ```BOTAUTH_VKONTAKTE_API_CONFIRM```

    ```BOTAUTH_TELEGRAM_API_TOKEN```

    ```BOTAUTH_TELEGRAM_PROXY```

    ```BOTAUTH_FACEBOOK_API_SECRET```

    ```BOTAUTH_FACEBOOK_API_TOKEN```

    ```BOTAUTH_FACEBOOK_API_CONFIRM```

6. Запустить миграции
    ``` php artisan migrate ```

7. В Middleware VerifyCsrfToken добавить исключение адреса для callback, по умолчанию botauth/callback/*'
```
protected $except = [
    'botauth/callback/*' // Except callback Csrf middleware
];
```

8. Для вашей модели User добавьте трейт:
```use ZetRider\BotAuth\Traits\BotAuthUserTrait;```
который добавит отношение с логинами пользователя из соц. сетей

## Подключение ботов:

### Вконтакте
1. Откройте настройки своего сообщества или создайте новое https://vk.com/groups?w=groups_create
2. В настройках сообщества откройте райздел "Настройки" - "Работа с API"
3. Создайте ключ доступа, выберите пункт "Разрешить приложению доступ к сообщениям сообщества", запишите ключ, его нужно указать в .env ```BOTAUTH_VKONTAKTE_API_TOKEN```
4. На той же странице выберите Callback API, выберите "Версия API" 5.95, укажите в поле "Адрес" callback адрес вашего сайта, пример по умолчанию https://DOMAIN/botauth/callback/vkontakte
5. Ниже укажите строку, которую должен вернуть сервер в .env ```BOTAUTH_VKONTAKTE_API_CONFIRM```
6. В поле "Секретный ключ" придумайте любой секретный ключ, укажите в .env ```BOTAUTH_VKONTAKTE_API_SECRET```
7. После заполнения всех ключей в .env нажмите кнопку "Подтверидть"
8. На этой же странице откройте вкладку "Типы событий", нужно выбрать "Входящие сообщения"
9. Откройте натсройки сообщества, пункт "Сообщения", включите "сообщения сообщества"
10. Откройте настройки сообщества, пункт "Сообщения" - "Настройки для бота", включите "Возможности ботов"

Бот готов к работе.

Пример прямой ссылки на диалог с ботом https://vk.me/zetcode

### Telegram
1. Создайте своего бота через @BotFather
2. Запомните ключ, укажите в .env ```BOTAUTH_TELEGRAM_API_TOKEN```
3. Добавьте веб хук через
```https://api.telegram.org/botYOUR_TOKEN/setWebhook?url=https://DOMAIN/botauth/callback/telegram```
где YOUR_TOKEN ваш токен.
4. При необходимости укажите прокси в .env
```BOTAUTH_TELEGRAM_PROXY```, например socks5h://127.0.0.1:1080

Бот готов к работе.

Пример прямой ссылки на диалог с ботом https://t.me/BotAuthBot

### Facebook

1. У вас должна быть создана страница, если ее нет, добавьте https://www.facebook.com/pages/creation/?ref_type=universal_creation_hub
2. Добвьте новое приложение https://developers.facebook.com/apps/
3. В настройках приложение выберите "Основное", скопируйте "Секрет приложения" в .env ```BOTAUTH_FACEBOOK_API_SECRET```
4. В настройках приложение нужно добавить продукт "Messenger"
5. В настройках продукта "Messenger" создайте токен доступа, укажите его в .env BOTAUTH_FACEBOOK_API_TOKEN
6. В настройках продукта "Messenger" создайте веб хук, в URL обратного вызова укажите https://DOMAIN/botauth/callback/facebook
в поле "Подтвердите маркер" укажите произвольный текст, сохраните в .env BOTAUTH_FACEBOOK_API_CONFIRM
в опциях "Поля подписки" выберите "messages"
нажмите "Подтвердить"
6. После подтверждения сервера в настройках веб хуков выберите страницу, нажмите "Подписаться" выбран нужную страницу
7. В окне "Проверка приложения Messenger" рядом с пунктом "pages_messaging" нажмите "Добавить к заявке"
8. Бот уже готов к работе, но доступен только для администраторов. После подтверждения приложения, он станет доступен для всех посетителей. Отправьте приложение на модерацию.

Пример прямой ссылки на диалог с ботом https://m.me/zetridercode

___

## Важно:
1. Сайт должен работать по https
2. Facebook бот возвращает PSID, который не соответствует публичному ID пользователя.
3. По умолчанию контроллер бота работает с моделью \App\User. Если у вас другой случай, просто создайте свой контроллер и модель на основе примеров из репозитория.

## Как добавить свой провайдер:
Создайте свой класс, который наследует абстрактный класс ```ZetRider\BotAuth\AbstractProvider```

Пример example/ExampleProvider.php

Добавьте в сервис провайдер, например AppServiceProvider в методе boot

```php
// Register example proider
BotAuth::extend('example', function() {
    return new \Path\To\Your\Class\ExampleProvider();
});
```
Провайдер будет обрабатывать запросы в callback по адресу /botauth/callback/example

## События
Событие при успешной обработке нового сообщения от бота

```php
// Catch bot callback
\Event::listen(\ZetRider\BotAuth\Events\MessageNewEvent::class, function($event)
{
    $provider = $event->provider; // ZetRider\BotAuth\AbstractProvider

    $slug = $provider->getProviderSlug();
    $data = $provider->getCallbackResponse();
    $user = $provider->getUser();
    $text = $provider->getText();

    // You can send a message
    // $provider->sendMessage(__('Back to web site'));
});
```