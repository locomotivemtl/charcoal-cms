Charcoal CMS
============

The CMS Charcoal Module (_Content Management System_). Provides basic objects to build a website.
Notably, `Section` (or _page_), `News`, `Event` and `Faq` as well as to ather user data, notably `ContactInquiry`.

This module is heavily dependant on [`charcoal-object`](https://github.com/locomotivemtl/charcoal-object) (and therefore `charcoal-core`) which provides the base `Content` class the CMS objects are dependant on, as well as many trait / interface behaviors.

# How to install

The preferred (and only supported) way of installing _charcoal-cms_ is with **composer**:

```shell
★ composer require locomotivemtl/charcoal-cms
```

For a complete, ready-to-use project, start from the [`charcoal project boilerplate`](https://github.com/locomotivemtl/charcoal-project-boilerplate):

```shell
★ composer create-project locomotivemtl/charcoal-project-boilerplate:@dev --prefer-source
```

## Dependencies

-   [`PHP 5.6+`](http://php.net)
    - PHP 7 is recommended for security and performance reasons.
-   [`locomotivemtl/charcoal-attachment`](https://github.com/locomotivemtl/charcoal-attachment)
    - Content blocks are provided with _attachments_.
-   [`locomotivemtl/charcoal-core`](https://github.com/locomotivemtl/charcoal-core)
    - Core charcoal models and storage class.
    - Provides base Model, which depends on Storable and Describable.
-   [`locomotivemtl/charcoal-object`](https://github.com/locomotivemtl/charcoal-object)
    - CMS objects are based on `\Charcoal\Object\Content`.
-   [`locomotivemtl/charcoal-translator`](https://github.com/locomotivemtl/charcoal-translator)
    - Localization is provided by symfony translator (charcoal).

### Recommended dependencies

-   [`locomotivemtl/charcoal-admin`](https://github.com/locomotivemtl/charcoal-admin)
    - The backend (admin panel).

# Objects

-   **Core objects**
    -   [Section](#section-object)
    -   [Tag](#tag-object)
-   **CMS objects**
    -   [Event](#event-object)
    -   [FAQ](#faq-object)
    -   [News](#news-object)

# Core objects

-   [Section](#section-object)
-   [Tag](#tag-object)

## Section object

A **section**, in Charcoal, is a reachable _page_ on the website, as part of the full hierarchical site map. They can be displayed in menus or breadcrumbs and be reached with a unique URL (`routable`).

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

| Interface     | Name                 | L10n | Type      | Description |
| ------------- | -------------------- | :--: | --------- | ----------- |
| Section       | **section_type**     |      | choice    |
| Section       | **title**            | ✔    | string    |
| Section       | **subtitle**         | ✔    | html      |
| Section       | **summary**          | ✔    | html      |
| Section       | **image**            | ✔    | image     |
| Section       | **template_ident**    |      | string    |
| Section       | **template_options**  |      | structure |
| Section       | **content**           | ✔    | html      |
| Section       | **attachments**       | ✔    | multi-object |
| Section       | **external_url**      | ✔    | url       | For external URLs. Note that all content-related properties are ignored if this property is set. |
| Content       | **id**                |      | id        | The model's `key`. |
| Content       | **active**            |      | bool      | Inactive events should not appear in public API / frontend. |
| Content       | **position**          |      | int       | Default order property. |
| Authorable    | **created_by**        |      | string    | Admin user.
| Authorable    | **last\_modified_by** |      | string    | Admin user.
| Authorable    | **required\_acl_permissions** | array    | To do...
| Timestampable | **created**           |      | date-time |
| Timestampable | **last_modified**     |      | date-time |
| Hierarchical  | **master**            |      | object | `SectionInterface`. |
| Routable      | **slug**              | ✔    | string | Permalink. Auto-generated from title. |

### Interfaces

From model:

- `Describable`: The objects can be defined by Metadata.
- `Storable`: Objects have unique IDs and can be stored in storage / database.

From content:

- `Content`: A "managed" charcoal model (describable with metadata / storable).
- `Authorable`: Creation and modification user (admin) are kept in the storage.
- `Revisionable`: Copy of changes will be kept upon each object update in the storage.
- `Timestampable`: Creation and modification time are kept into the storage.

From charcoal-object

- `Hierarchicale`: The objects can be stacked hierarchically.
- ~~`Publishable`: Objects have publish status, date and expiry. Allows moderation.~~
- `Routable`: Objects are reachable through a URL.

From charcoal-cms:

- `Metatag`: The objects have meta-information for SEO purpose.
- `Searchable`: Extra keywords can be used to help search engine.
- `Templateable`: The objects can be rendered with a template / controller / config combo.

### Extending the section object

The `\Charcoal\Cms\Section\*` objects are `final`. To extend, use the `\Charcoal\Cms\AbstractSection` base object instead, to make sure no metadata conflicts arise.

## Tag object

**Tag** objects link any objects together by providing an extra taxonomy layer. Tags may also be used to enhance internal search engines.

# CMS objects

-   [Event](#event-object)
-   [FAQ](#faq-object)
-   [News](#news-object)

## Event object

Charcoal **Event** is a specialized content object to describe an event, which typically happens at a given date in a certain location.

Base events properties:

| Interface     | Name                  | L10n | Type      | Description |
| ------------- | --------------------- | :--: | --------- | ----------- |
| Event         | **title**             | ✔    | string    |
| Event         | **subtitle**          | ✔    | html      |
| Event         | **summary**           | ✔    | html      |
| Event         | **content**           | ✔    | html      |
| Event         | **image**             | ✔    | image     |
| Event         | **start_date**        |      | date-time |
| Event         | **end_date**          |      | date-time |
| Event         | **info_url**          | ✔    | image     |
| Content       | **id**                |      | id        | The model's `key`. |
| Content       | **active**            |      | bool      | Inactive events should not appear in public API / frontend. |
| Content       | **position**          |      | int       | Default order property. |
| Authorable    | **created_by**        |      | string    | Admin user.
| Authorable    | **last\_modified_by** |      | string    | Admin user.
| Authorable    | **required\_acl_permissions** | array    | To do...
| Timestampable | **created**           |      | date-time |
| Timestampable | **last_modified**     |      | date-time |
| Categorizable | **category**          | ✔    | object    | `EventCategory`, or custom. |
| Publishable   | **publishDate**      |      | date-time |
| Publishable   | **expiryDate**       |      | date-time |
| Publishable   | **publishStatus**    |      | string    | `draft`, `pending`, or `published`. |
| Routable      | **slug**              | ✔    | string    | Permalink. Auto-generated from title. |
| Metatag       | **meta_title**       | ✔    | string    |
| Metatag       | **meta_description** | ✔    | string    |
| Metatag       | **meta_image**       | ✔    | image     |
| Metatag       | **meta_author**      | ✔    | string    |
| Templateable  | **controller_ident** |      | string    |
| Templateable  | **template_ident**   |      | string    |
| Templateable  | **template_options** |      | structure |

### Interfaces

From model:

- `Describable`: The objects can be defined by Metadata.
- `Storable`: Objects have unique IDs and can be stored in storage / database.

From content:

- `Content`: A "managed" charcoal model (describable with metadata / storable).
- `Authorable`: Creation and modification user (admin) are kept in the storage.
- `Revisionable`: Copy of changes will be kept upon each object update in the storage.
- `Timestampable`: Creation and modification time are kept into the storage.

From charcoal-object:

- `Categorizable`: The objects can be put into a category.
- `Publishable`: Objects have publish status, date and expiry. Allows moderation.
- `Routable`: Objects are reachable through a URL.

From charcoal-cms:

- `Metatag`: The objects have meta-information for SEO purpose.
- `Searchable`: Extra keywords can be used to help search engine.
- `Templateable`: The objects can be rendered with a template / controller / config combo.

### Extending the event object

The `\Charcoal\Cms\Event` object is `final`. To extend, use the `\Charcoal\Cms\AbstractEvent` base object instead, to make sure no metadata conflicts arise.

### Event categories

**Event category** objects are simple `charcoal/object/category` used to group / categorize events. The default type is `Charcoal\Cms\EventCategory`.

_Events_ implement the `Categorizable` interface, from charcoal-object.

## FAQ object

**FAQ** objects are a special content type that is split in a "question" / "answer" format.

### FAQ categories

**FAQ category** objects are simple `charcoal/object/category` used to group / categorize FAQ objects. The default type is `Charcoal\Cms\FaqCategory`.

_FAQs_ implement the `Categorizable` interface, from charcoal-object.

## News object

News object are a special content type that with a specific news date.

Base news properties:

| Interface     | Name                  | L10n | Type      | Description |
| ------------- | --------------------- | :--: | --------- | ----------- |
| News          | **title**             | ✔    | string    |
| News          | **subtitle**          | ✔    | html      |
| News          | **summary**           | ✔    | html      |
| News          | **content**           | ✔    | html      |
| News          | **image**             | ✔    | image     |
| News          | **news_date**         |      | date-time |
| News          | **info_url**          | ✔    | image     |
| Content       | **id**                |      | id        | The model's `key`. |
| Content       | **active**            |      | bool      | Inactive news should not appear in public API / frontend. |
| Content       | **position**          |      | int       | Default order property. |
| Authorable    | **created_by**        |      | string    | Admin user.
| Authorable    | **last\_modified_by** |      | string    | Admin user.
| Authorable    | **required\_acl_permissions** | array    | To do...
| Timestampable | **created**           |      | date-time |
| Timestampable | **last_modified**     |      | date-time |
| Categorizable | **category**          | ✔    | object    | `NewsCategory`, or custom. |
| Publishable   | **publishDate**      |      | date-time |
| Publishable   | **expiryDate**       |      | date-time |
| Publishable   | **publishStatus**    |      | string    | `draft`, `pending`, or `published`. |
| Routable      | **slug**              | ✔    | string    | Permalink. Auto-generated from title. |
| Metatag       | **meta_title**       | ✔    | string    |
| Metatag       | **meta_description** | ✔    | string    |
| Metatag       | **meta_image**       | ✔    | image     |
| Metatag       | **meta_author**      | ✔    | string    |
| Templateable  | **controller_ident** |      | string    |
| Templateable  | **template_ident**   |      | string    |
| Templateable  | **template_options** |      | structure |

### Interfaces

From model:

- `Describable`: The objects can be defined by Metadata.
- `Storable`: Objects have unique IDs and can be stored in storage / database.

From content:

- `Content`: A "managed" charcoal model (describable with metadata / storable).
- `Authorable`: Creation and modification user (admin) are kept in the storage.
- `Revisionable`: Copy of changes will be kept upon each object update in the storage.
- `Timestampable`: Creation and modification time are kept into the storage.

From charcoal-object:

- `Categorizable`: The objects can be put into a category.
- `Publishable`: Objects have publish status, date and expiry. Allows moderation.
- `Routable`: Objects are reachable through a URL.

From charcoal-cms:

- `Metatag`: The objects have meta-information for SEO purpose.
- `Searchable`: Extra keywords can be used to help search engine.
- `Templateable`: The objects can be rendered with a template / controller / config combo.

### Extending the news object

The `\Charcoal\Cms\News` object is `final`. To extend, use the `\Charcoal\Cms\AbstractNews` base object instead, to make sure no metadata conflicts arise.

### News categories

**News category** objects are simple `charcoal/object/category` used to group / categorize events. The default type is `Charcoal\Cms\NewsCategory`.

_News_ implement the `Categorizable` interface, from charcoal-object.

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

The `charcoal-cms` module follows the Charcoal coding-style:

-   [_PSR-1_](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)
-   [_PSR-2_](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
-   [_PSR-4_](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md), autoloading is therefore provided by _Composer_.
-   [_phpDocumentor_](http://phpdoc.org/) comments.
-   Read the [phpcs.xml](phpcs.xml) file for all the details on code style.

> Coding style validation / enforcement can be performed with `composer phpcs`. An auto-fixer is also available with `composer phpcbf`.

# Authors

-   [Locomotive](https://locomotive.ca)

# License

Charcoal is licensed under the MIT license. See [LICENSE](LICENSE) for details.
