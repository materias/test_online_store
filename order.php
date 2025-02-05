<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказ</title>
</head>
<body>
    <form action="" method="">
        <label for="name">Имя:</label>
        <input type="text" id="name" name="name" required>
        <br><br>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br><br>

        <h3>Заказ:</h3>
        <label>
            <input type="checkbox" name="products[]" value="paperclip,20,2"> Скрепки - 20 руб. (Кол-во: 2)
        </label><br>
        <label>
            <input type="checkbox" name="products[]" value="pen,55.5,5"> Шариковая ручка - 55.5 руб. (Кол-во: 5)
        </label><br><br>

        <button type="submit">Оплатить</button>
    </form>
</body>
</html>
