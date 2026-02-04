This file contains the prompt for AI model to be used to generate other files
inside this directory containing dummy data for data fixtures.

To generate required attachement containing PHP code with entites,
embeddables and enums, use the following command:

find src/Entity/ -type f | xargs -I{} sh -c 'echo {}; echo ===; echo; echo \`\`\`; cat {}; echo \`\`\`; echo' > ~/php-source-code-files.md

---

You are a helpful assistant whose job is to generate dummy data for data fixtures
for PHP/Symfony application.

Your task
===

Please generate PHP files in a form of `<?php return [ ... ]` statements,
containing dummy data to be used in data fixtures. You will be provided with
PHP code containing entities classes, embeddable classes and enums.

Generate separate downloadable PHP files for each given entity
(don't put the whole output in your answer).
Read comments inside provided code snippets to understand the meaning of each field.
Make sure to follow specified format using examples provided below.

## General rules

- Generate real-world data: up-to-date names of radio stations, broadcasters,
  transmitter locations, etc.
- Make sure the size of the data matches the production environment –
  number of radio stations, number of radio tables, etc.
- Cover edge cases like the radio table with 4000+ stations,
  radio table with 10 stations, radio table with no stations, etc.
- Use Polish names for most radio stations, locations, etc.
- Entity IDs should be sequential (1, then 2, then 3, etc.).
- Make sure to create and keep relations between entities using their IDs.
- Strip any whitespace and any comments from generated PHP code.
- Consider validation rules (PHP attributes) from inside provided PHP classes.
- Always strictly follow data types (integer, string, boolean) from examples
  used in provided code snippets below. However, `null` is always allowed
  in case of nullable fields.
- Use `null` for empty values. Never use empty string.
- Store date and date time values as strings.
- Store enum values as raw values (strings or integers).
- Always consider possible values of enums.
  Make sure to never use values unsupported by enum.

## Source of the data

Take inspiration for radio table data, radio station data, user data from:

- https://radiolista.pl/wszystkie-wykazy
- https://radiolista.pl/sitemap.xml

Follow links to public radio tables and public user profiles.

Please don't copy 1:1. Make up your own values but use these sources as
the source of inspiration.

Entity: User
===

- Number of instances: 400
- PHP file name: `user.php`

```php
<?php

return [
    1 => [
        // name
        'kowalski99_dx',

        // publicProfile
        true,

        // aboutMe
        '<p>Mój odbiornik: <b>wieża Panasonic</b></p><p>Antena: <b>dookólna dachowa</b></p><p>Tematy związane z radiem to moje hobby od 15 lat!<p>',

        // registerDate
        '2012-07-01',

        // lastActivityDate
        '2025-01-29',
    ],

    // next rows here
];
```

## Rules

- `registerDate` between `2012-07-01` and today.
- `lastActivityDate` between `registerDate` and today (sometimes may equal to `registerDate`);
- Make sure some users have longer `aboutMe` descriptions than in the provided
  example (2x longer or 3x longer, for example).
- Sometimes `aboutMe` might be null (rarely).
- Only around 12% of users have `publicProfile` enabled
  (usually, the most active users and with the biggest radio tables).

Entity: RadioTable
===

- Number of instances: 900
- PHP file name: `radio_table.php`

```php
<?php

return [
    1 => [
        // name
        '[FM-DX] Bydgoszcz, Białe Błota',

        // status
        1,

        // columns
        ['frequency', 'name', 'location', 'power', 'polarization', 'country', 'quality', 'rds', 'firstLogDate', 'reception', 'distance', 'maxSignalLevel', 'rdsPi'],

        // sorting
        'frequency',

        // description
        '<p>W komentarzach piszę, jaki sprzęt jest potrzebny do odbioru.</p><p>Wykaz powstawał z większymi przerwami od 2015 roku do dziś.</p><p>Odbiorniki:</p><ul><li>TEF6686 / Pioneer MVH-S100UB</li></ul>',

        // lastUpdateTime
        '2024-01-07',

        // creationTime
        '2020-08-30',

        // owner (user id)
        1,

        // frequencyUnit
        1,

        // maxSignalLevelUnit
        2,

        // appearance.widthType
        1,

        // appearance.customWidth
        1200,

        // appearance.collapsedComments
        false,
    ],

    // next rows here
];
```

## Rules

- Nearly half of radio tables is public (`status` `1`).
- `creationTime` between 2012-07-01 and today.
- `lastUpdateTime` between `creationTime` and today (sometimes may equal to `creationTime`).
- Make sure some radio tables have longer descriptions than in the provided example
  (2x longer or 3x longer, for example).
- Sometimes `description` might be null (rarely).
- `creationTime` must be empty for radio tables created before 2020-03-01 and
  cannot be empty for radio tables created after.
- `frequency` and `name` are always required in `columns`.
- `frequency` is the most popular `sorting`.
- If `appearance.widthType` is set to custom (`3`),
  `appearance.customWidth` must not be null, otherwise it must be null.

Entity: RadioStation
===

- Number of instances: 70 000
- PHP file name: `radio_station.php`

```php
<?php

return [
    1 => [
        // radioTable (radio table id)
        1,

        // name
        'Polskie Radio Jedynka',

        // radioGroup
        'Polskie Radio',

        // country
        'Polska',

        // region
        '',

        // frequency
        '89.70',

        // location
        'Toruń *Komin PGE Toruń*',

        // power
        '7.90',

        // polarization
        'H',

        // multiplex
        '',

        // dabChannel
        '',

        // distance
        55,

        // maxSignalLevel
        14,

        // reception
        0,

        // privateNumber
        1,

        // firstLogDate
        '2019-08-30',

        // quality
        4,

        // type
        2,

        // rds.ps
        [['POLSKIE',' RADIO','JEDYNKA ',' GG:MM','[NAZWA  ','PROGR. ]']],

        // rds.rt
        [' 00-977 WARSZAWA    TE.022 645 9115    FAX.022 645 9515', '*  POLSKIE RADIO SA  * PROGRAM 1  AL. NIEPODLEGLOSCI  77\/85'],

        // rds.pty
        'INFO',

        // rds.pi
        '3211',

        // comment
        'Odbiór falujący; silnie zakłócany przez inną nieznaną stację',

        // externalAnchor
        'https://www.youtube.com/watch?v=dQw4w9WgXcQ',

        // appearance.background
        null,

        // appearance.bold
        false,

        // appearance.italic
        false,

        // appearance.strikethrough
        false,
    ],

    // next rows here
];
```

## Rules

- Make random trends consitent between radio stations added to one radio table.
- Most of the time the value of `frequency` and `power` is a decimal number with
  two digit precision (like `90.15`) but rarely it's a decimal number with
  three digit precision (for example: `105.905`).
- Make sure values of `frequency` match to radio table's `frequencyUnit`.
- Make sure values of `maxSignalLevel` match to radio table's `maxSignalLevelUnit`.
- Sometimes `comment` might be null.
- Sometimes `externalAnchor` might be null.
- Supported forms of `firstLogDate`: `2012-07-01` (year + month + day),
  `2012-07` (year + month, no day), `2012` (year only).
- Most radio stations in one radio table don't use any `appearance` settings.
  Using `background`, `bold`, `italic`, `strikethrough` is exceptional.
- `privateNumber` is empty in most radio tables.
- Music (`1`) is the most popular value of `type`.
- `region` contains the name of the region in the country from `country` field
  (like the voivodeship or the state).
- It doesn't make sense to fill in `rds.rt` and `rds.pty` if `rds.ps` is empty.
- `multiplex` should contain names of digital radio multiplexes.
- `multiplex` and `dabChannel` are empty for analog radio.
- If `dabChannel` is not empty, make sure to fill in `frequency` with
  matching value.
