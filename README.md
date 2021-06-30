# GeroyRegionov 
Проект автоматического постинга в социальных сетях контента используя api (http запросы).
**Важно!** В данном readme описано взаимодействие с системой с точки зрения конечного пользователя, а не товарища, который захочет поднять данную систему у себя. 
Если что - пишите на почту schtil@schtil.com
Или телега @schtil

# Общая структура запросов
Взаимодействие происходит по средствам GET запроса по адресу: 

> geroyregionov.schtil.com/**method**?**param1**=**value1**&**param2**=**value2**


Где:
- method - название метода
- param1 / param2 - имя get-параметра
- value1 / value2 - значение get-параметра

Для взаимодействия с системой обязательным параметром к каждому запросу является **access_token**, который может получить у администратора. 

В ответ приходит json-строка, где всегда есть ключ "status", который передает состояние запроса.
ok - всё прошло хорошо.
error - произошла ошибка, см ключи error_code и error_msg.

Некоторые параметры для выполнения запроса можно не передавать каждый раз, а сохранить на сервере. Список таких полей указаны в данной таблице и предоставлено описание для них.
(Как задавать/Какие поля - указано далее)

# Доступные провайдеры и поля
![image](https://sun9-52.userapi.com/impg/V2DGxlpR_hFSlvEj4lBrH-IrsaVsdoSv4GOF8Q/Aralv7xrX4k.jpg?size=1021x942&quality=96&sign=6fd604b51ff31f88cd8fc163c0044839&type=album)

# Доступные методы и их параметры
![image](https://sun9-58.userapi.com/impg/L7yibYqQyYKhvPOcb2Hvd1q62HU9sMXcISRmpA/ngKBUiHfjFE.jpg?size=646x891&quality=96&sign=be4ec54ce56e1ac02139e1be9195d293&type=album)

# Справка по кодам ошибок
![image](https://sun9-67.userapi.com/impg/gveeUl1OWFwHjmnkIPrBFq2_-tGoCOM7rQLp0A/er6MBGK51Qg.jpg?size=1038x745&quality=96&sign=1dfaf91f009c924334281b9588e1d07c&type=album)
