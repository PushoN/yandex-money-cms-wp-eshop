#yandexmoney-wp-eshop

Модуль оплаты yandexmoney-wp-eshop необходим для интеграции с сервисом [Яндекс.Касса](http://kassa.yandex.ru/) на базе CMS WordPress и компонента магазина eShop. 

Доступные платежные методы, если вы работаете как юридическое лицо:
* **Банковские карты** -  Visa (включая Electron), MasterCard и Maestro любого банка мира
* **Электронные деньги** - Яндекс.Деньги, WebMoney и QIWI Wallet
* **Наличные** - [Более 170 тысяч пунктов](https://money.yandex.ru/pay/doc.xml?id=526209) оплаты по России
* **Баланс телефона** - Билайн, МегаФон и МТС
* **Интернет банкинг** - Альфа-Клик, Сбербанк Онлайн, MasterPass и Промсвязьбанк
* **Кредитование** - Доверительный платеж (Куппи.ру)

###Требования к CMS WordPress:
* версия 3.8;
* компонент eShop

###Установка модуля
Для установки данного модуля необходимо;
* распаковать содержимое [архива](https://github.com/yandex-money/yandex-money-cms-wp-eshop/archive/master.zip) в папку `wp-content/plugins` Вашего сайта
* активировать плагин ***eShop Yandex.Money*** (`Плагины` - `Установленные` - `eShop Yandex.Money` - `Активировать`)
* следовать пунктам [инструкции](https://github.com/yandex-money/yandex-money-cms-wp-eshop/raw/master/WordPress.pdf)

Описание пунктов настройки модуля (`eShop Yandex.Money` -> `settings`):
* Выбор типа магазина:
	* Off - привязка к яндекс-деньгам выключена, заказы просто отправляются на email магазина
	* Standard form (no agreement) - форма для приема денег без регистрации (доступны только оплата яндекс-деньгами и банковской картой)
	* Protocol form (with agreement) - форма для приема денег с регистрацией (доступны все виды оплаты: яндекс-деньги, банковская карта, терминалы и мобильные телефоны)
* Включение или отключение тестового режима магазина
* Заполнение параметров для соответствующей формы:
	* для формы без регистрации:
		* Shop account - номер счета в системе яндекс-деньги
		* Secret - секретное слово
		* Payment description - описание платежа, которое будет отображаться для покупателей
		* Payment types - какие виды платежей принимать на сайте
		* так же для полноценной работы магазина в настройках яндекс-денег необходимо настроить checkAviso url и payment url (https://адрес_сайта/eshop/check))
	* для формы с регистрацией
		* Shop identificator - идентификатор магазина (получается при регистрации)
		* Showcase identificator - Идентификатор витрины магазина (получается при регистрации)
		* Password - пароль (получается при регистрации)
		* Success page - страница на которую попадет покупатель при успешной оплате покупки (необходимо предварительно создать её в Pages)
		* Fail page - страница на которую попадет покупатель при ошибке или отказе оплаты (необходимо предварительно создать в Pages)
		* Payment types - какие виды платежей принимать на сайте
* Заполнение общих настроек магазина:
	* EShop index page - главная страница каталога (необходимо предварительно создать страница её в Pages, желательно использовать адрес /eshop, т.к. дальнейшая структура каталога будет использовать этот префикс)
	* Cart page - страница корзины (например, /eshop/cart)
	* Email for orders - E-mail на который будет отправляться информация о заказах
	* Company name - название компании
	* Currency name - название валюты
* Создание структуры каталогов и товаров в `eShop Yandex.Money` -> `categories` и `eShop Yandex.Money` -> `items`

Пожалуйста, обязательно делайте бекапы!

###Лицензионный договор.
Любое использование Вами программы означает полное и безоговорочное принятие Вами условий лицензионного договора, размещенного по адресу https://money.yandex.ru/doc.xml?id=527132 (далее – «Лицензионный договор»). 
Если Вы не принимаете условия Лицензионного договора в полном объёме, Вы не имеете права использовать программу в каких-либо целях.

###Нашли ошибку или у вас есть предложение по улучшению модуля?
Пишите нам cms@yamoney.ru
При обращении необходимо:
* Указать наименование CMS и компонента магазина, а также их версии
* Указать версию платежного модуля (доступна на странице со списком плагинов)
* Описать проблему или предложение
* Приложить снимок экрана (для большей информативности)