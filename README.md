## Craft Faker plugin

Allows you to use [Faker](https://github.com/fzaninotto/Faker) to output random fake data in your templates. It can go hand in hand with for example [Viget's Front-end Parts Kits](http://viget.com/extend/front-end-parts-kits-in-craft)

### Usage

Here's an example of some faking:

```
{% set faker = craft.faker.factory(['address']) %}
<ul>
{% for i in 1..10 %}
    <li>{{ faker.name }}, living at: {{ faker.address }}. Profile picture: {{ faker.imageUrl(640, 450, 'people') }}</li>
{% endfor %}
</ul>
```

### Localization

To use another locale, like French:
```
{% set faker = craft.faker.locale('fr_FR').fake() %}
```

For a list of supported locales, check out [the Faker docs](https://github.com/fzaninotto/Faker)