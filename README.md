Charcoal CMS
============

The CMS Charcoal Module (_Content Management System_). Provides basic objects to build a website.
Notably, `Section` (or _page_), `News`, `Event` and `Faq` as well as to ather user data, notably `ContactInquiry`.

This module is heavily dependant on [`charcoal-object`](https://github.com/locomotivemtl/charcoal-object) (and therefore `charcoal-core`) which provides the base `Content` and `UserData` classes the CMS objects are dependant on.

# How to install

The preferred (and only supported) way of installing _charcoal-cms_ is with **composer**:

```shell
★ composer require locomotivemtl/charcoal-cms
```

For a complete, ready-to-use project, start from the [`boilerplate`](https://github.com/locomotivemtl/charcoal-project-boilerplate):

```shell
★ composer create-project locomotivemtl/charcoal-project-boilerplate:@dev --prefer-source
```

## Dependencies

-   [`PHP 5.6+`](http://php.net)
    - PHP 7 is recommended for security and performance reasons.
-   [`locomotivemtl/charcoal-attachment`](https://github.com/locomotivemtl/charcoal-attachment)
-   [`locomotivemtl/charcoal-core`](https://github.com/locomotivemtl/charcoal-core)
    - Core charcoal models and storage class.
    - Provides base Model, which depends on Storable and Describable.
-   [`locomotivemtl/charcoal-object`](https://github.com/locomotivemtl/charcoal-object)
-   [`locomotivemtl/charcoal-translator`](https://github.com/locomotivemtl/charcoal-translator)

### Recommended dependencies

-   [`locomotivemtl/charcoal-admin`](https://github.com/locomotivemtl/charcoal-admin)

# Objects

All objects in the `charcoal-cms` module implements `\Charcoal\Object\Content`, which allows to store creation & modification dates. Many objects also implement the `\Charcoal\Object\PublishableInterface`.


-   **Core objects**
    -   [Section](#section-object)
-   **CMS objects**
    -   [Event](#event-object)
    -   [FAQ](#faq-object)
    -   [News](#news-object)

# Core objects

-   [Section](#section-object)

## Section object

A **section**, in Charcoal, is a reachable page on the website, as part of the full hierarchical site map. They can be displayed in menus or breadcrumbs and be reached with a unique URL (`routable`).

Types of sections:

-   `blocks`
    -   Blocks sections define their content as a structured map of blocks.
-   `content`
    -   Content sections define their content in a single, simple _HTML_ property.
-   `empty`
    -   Empty sections are linked to a template but do not require any custom content.
-   `external`
    -   External sections are simply a redirect to an external (or internal) URL.

All section types, except _external_, make use of a `Template` object to be rendered. Typically, a charcoal `view` make sure of linking the `template` (by default, _mustache_

> Sections are standard Charcoal `Model`, meaning they are _describable_ with a `Metadata` object (which define a map of `properties`) and _storable_ with a `Source` object.

Base section properties:

| Name                 | L10n | Type      | Description |
| -------------------- | :--: | --------- | ----------- |
| **title**            | ✔    | string    |
| **subtitle**         | ✔    | html      |
| **template_ident**   |      | string    |
| **template_options** |      | structure |
| **attachments**      | ✔    | multi-object |

Extra _blocks_ properties:

| Name                 | L10n | Type      | Description |
| -------------------- | :--: | --------- | ----------- |
| **blocks**           | ✔    | structure |

Extra _content_ properties:

| Name                 | L10n | Type | Description |
| -------------------- | :--: | ---- | ----------- |
| **content**          | ✔    | html |

Extra _external_ properties:

| Name                 | L10n | Type | Description |
| -------------------- | :--: | ---- | ----------- |
| **external_url**     | ✔    | url  |

<small>Note that `external` sections ignore the _template\_ident_ & _template\_options_ properties as well as the _metatags_ set of properties.</small>

--

Because _sections_ extends `\Charcoal\Object\Content`, they also have the following standard properties:

| Name                  | L10n | Type | Description |
| --------------------- | :--: | ---- | ----------- |
| **id<sup>1</sup>**    |      |
| **active**            |      |
| **position**          |      |
| **created**           |      |
| **created_by**        |      |
| **last_modified**     |      |
| **last\_modified_by** |      |

<small>[1] By default, the **key** of the section is the **id**.</small>.

--

_Sections_ are hierarchical. They can be indented inside one another to create multi-dimensional site maps. The additional properties are therefore available:

| Name                 | L10n | Type | Description |
| -------------------- | :--: | ---- | ----------- |
| **master**           |      |

The hierarchical interface (`\Charcoal\Object\HierarchicalInterface`) also provide the following methods, amongst others:

-   `hierarchy()`
-   `children()`
-   `siblings()`

--

_Sections_ have _metatags_.

| Name                 | L10n | Type   | Description |
| -------------------- | :--: | ------ | ----------- |
| **meta_title**       | ✔    | string |
| **meta_description** | ✔    | text   |
| **meta_image**       | ✔    | image  |
| **meta_author**      | ✔    | string |

...and more specialized properties, for _facebook_ / _opengraph_.

__

Like all _Content_ objects, _sections_ implement the `\Charcoal\Object\RevisionableInterface`, which tracks all save / update into _revisions_.

### Extending the section object

The `\Charcoal\Cms\Section\*` objects are `final`. To extend, use the `\Charcoal\Cms\AbstractSection` base object instead, to make sure no metadata conflicts arise.


# CMS objects

-   [Event](#event-object)
-   [FAQ](#faq-object)
-   [News](#news-object)

## Event object

## FAQ object

## News object


# Development

To install the development environment:

```shell
$ composer install --prefer-source
```

## API documentation
-   The auto-generated `phpDocumentor` API documentation is available at [https://locomotivemtl.github.io/charcoal-cms/docs/master/](https://locomotivemtl.github.io/charcoal-cms/docs/master/)
-   The auto-generated `apigen` API documentation is available at [https://codedoc.pub/locomotivemtl/charcoal-cms/master/](https://codedoc.pub/locomotivemtl/charcoal-cms/master/index.html)

## Development dependencies

-   `phpunit/phpunit`
-   `squizlabs/php_codesniffer`
-   `satooshi/php-coveralls`

## Continuous Integration

| Service | Badge | Description |
| ------- | ----- | ----------- |
| [Travis](https://travis-ci.org/locomotivemtl/charcoal-cms) | [![Build Status](https://travis-ci.org/locomotivemtl/charcoal-cms.svg?branch=master)](https://travis-ci.org/locomotivemtl/charcoal-cms) | Runs code sniff check and unit tests. Auto-generates API documentation. |
| [Scrutinizer](https://scrutinizer-ci.com/g/locomotivemtl/charcoal-cms/) | [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/locomotivemtl/charcoal-cms/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/locomotivemtl/charcoal-cms/?branch=master) | Code quality checker. Also validates API documentation quality. |
| [Coveralls](https://coveralls.io/github/locomotivemtl/charcoal-cms) | [![Coverage Status](https://coveralls.io/repos/github/locomotivemtl/charcoal-cms/badge.svg?branch=master)](https://coveralls.io/github/locomotivemtl/charcoal-cms?branch=master) | Unit Tests code coverage. |
| [Sensiolabs](https://insight.sensiolabs.com/projects/533b5796-7e69-42a7-a046-71342146308a) | [![SensioLabsInsight](https://insight.sensiolabs.com/projects/44d8d264-207b-417d-bcbd-dd52274fc201/mini.png)](https://insight.sensiolabs.com/projects/44d8d264-207b-417d-bcbd-dd52274fc201) | Another code quality checker, focused on PHP. |

## Coding Style

The Charcoal-App module follows the Charcoal coding-style:

-   [_PSR-1_](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)
-   [_PSR-2_](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
-   [_PSR-4_](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md), autoloading is therefore provided by _Composer_.
-   [_phpDocumentor_](http://phpdoc.org/) comments.
-   Read the [phpcs.xml](phpcs.xml) file for all the details on code style.

> Coding style validation / enforcement can be performed with `composer phpcs`. An auto-fixer is also available with `composer phpcbf`.

## Authors

-   Mathieu Ducharme <mat@locomotive.ca>

## Changelog

### Unreleased

# License

**The MIT License (MIT)**

_Copyright © Locomotive inc._
> See [Authors](#authors).

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
