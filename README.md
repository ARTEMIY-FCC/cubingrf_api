# cubingrf_api
Неофициальное json API для получения информации о соревнованиях
# Документация CubingRF API

## Базовый URL

```
https://speedcubing.by/cubingrf_api/data/competitions.json
```

## Важно

Данное API является **неофициальным**. Оно создано для удобства разработчиков и энтузиастов спидкубинга, но не является частью официального сайта CubingRF.

## Описание

CubingRF API предоставляет список официальных соревнований по спидкубингу. Данные обновляются регулярно.

## Формат ответа

* Content type: `application/json`
* Структура: массив соревнований

## Объект соревнования

```json
{
  "name": "Название соревнования",
  "date": "ДД месяц ГГГГ",
  "city": "Город",
  "url": "Ссылка на страницу соревнования",
  "events": ["3х3х3", "2х2х2", "Pyraminx"]
}
```

## Пример ответа

```json
[
  {
    "name": "Neva Open 2024",
    "date": "28 сентября 2024",
    "city": "Санкт-Петербург",
    "url": "https://cubingrf.org/competitions/NevaOpen2024",
    "events": [
      "3х3х3",
      "2х2х2",
      "4х4х4",
      "Clock",
      "Pyraminx"
    ]
  }
]
```

## Примеры подключения

### JavaScript (fetch)

```javascript
fetch('https://speedcubing.by/cubingrf_api/data/competitions.json')
  .then(response => response.json())
  .then(data => {
    console.log("Список соревнований:", data);
  })
  .catch(error => console.error("Ошибка при загрузке данных:", error));
```

### Python (requests)

```python
import requests

url = "https://speedcubing.by/cubingrf_api/data/competitions.json"
response = requests.get(url)

if response.status_code == 200:
    competitions = response.json()
    print("Список соревнований:")
    for comp in competitions:
        print(f"{comp['name']} ({comp['date']}, {comp['city']})")
else:
    print("Ошибка при загрузке данных:", response.status_code)
```

