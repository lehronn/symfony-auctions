# fast howto.md
[TOC]
## php
The double arrow operator, =>, is used as an access mechanism for arrays. This means that what is on the left side of it will have a corresponding value of what is on the right side of it in array context. This can be used to set values of any acceptable type into a corresponding index of an array. The index can be associative (string based) or numeric.
```php
$myArray = array(
    0 => 'Big',
    1 => 'Small',
    2 => 'Up',
    3 => 'Down'
);
```
The object operator, ->, is used in object scope to access methods and properties of an object. It’s meaning is to say that what is on the right of the operator is a member of the object instantiated into the variable on the left side of the operator. Instantiated is the key term here.
```php
// Create a new instance of MyObject into $obj
$obj = new MyObject();
// Set a property in the $obj object called thisProperty
$obj->thisProperty = 'Fred';
// Call a method of the $obj object named getProperty
$obj->getProperty();
```
PHP has two object operators.
The first, ->, is used when you want to call a method on an instance or access an instance property.
The second, ::, is used when you want to call a static method, access a static variable, or call a parent class's version of a method within a child class.
## git
```bash
echo "# symfony-auctions" >> README.md
git init
git add README.md
git commit -m "first commit"
git remote add origin https://github.com/lehronn/symfony-auctions.git
git push -u origin master
```
## symfony instalacja installer i odpalanie serwera

symfony new nazwa-projektu 3.4 tworzy nowy projekt za pomocą symfony installera
trzeba ściągną symfony installer, stworzy plik bat i wrzuci symfony i symfony.bat do zmiennej środowiskowej

```bash
php bin/console server:start #uruchamia serwer w tle.
php bin/console server:run #uruchamia w konsoli więc wystarczy ctrl+c by zatrzymać serwer.
```

domyślnie serwer uruchamia się pod adresem http://localhost:8000.

## twig

{{ }} wstawianie wartości zmiennych
{% block komponent %} {% endblock %} istrukcje sterujące IF FOR itd albo wstawianie komponentów o danej nazwie.
{% extends %} dziedziczenie

%kernel.project_dir% - to parametr trzymający ścieżkę naszego projektu.

{{ asset('favicon.ico') }} - wyszukuje i podaje adres pliku w katalogu projektu.

jeśli chcesz wyświetlić zmienną innego typu niż string czy number musisz go przekonwertować:

```twig
<p>Auction will expired: {{ auction.expiresAt | date("d.m.Y H:i") }}.</p>
```

jeżeli chcesz automatycznie importować style formularzy z bootstrap4 do każdego formularza zbudowanego formbuilderem musisz edytować plik app/config/config.yml
dodając:

```yaml
twig:
    form_themes: ['bootstrap_4_layout.html.twig']
```

## Doctrine

```bash
bin/console doctrine:database:create #tworzy nową bazę danych

#pod windowsem przed bin trzeba użyć komendy php!

php bin/console doc:dat:cr #skrót do tworzenia nowej bazy danych.
php bin/console doctrine:generate:entity #tworzy klasę z encją (czyli tworzy jakby tabelę w baze danych).

php bin/console doctrine:schema:create #tworzy tabele w bazie danych na podstawie klas z encją.

php bin/console doctrine:schema:update #aktualizuje tabele w bazie danych na podstawie zmian w klasach z encją. trzeba wymuszać taką zmianę --force lub --dump-sql żeby wypluło w konsoli zmiany.
```

##Instalacja FOSUserBundle
Patrz uważnie jakie wersje biblioteki instalujesz i czy dokumentacja też dotyczy tej wersji bo na stronie Symfony z jakiegoś powodu pokazują dokumentację z 1.3 a nie 2.

Gdyby były problemy z user-emailem to przepisz z filmiku config, bo w online dokumentacji brakuje tej konfiguracji.
Gdyby były problemy z nieznanym csrf to też przepisz config z filmiku, różni się tylko nazwą.

Gdyby był problem z nieznalezioną encją zamień w pliku User.php import na:
""FOS\UserBundle\Model\User"" i tak dalej reszta jest ok...as baseUser czy coś takiego


OGARNĄĆ JAK DZIAŁA PARAM CONVERTER
