Charcoal CMS
============

The CMS Charcoal Module (Content Management System).


# Objects

All objects in the `charcoal-cms` module implements `\Charcoal\Object\Content`, which allows to store creation & modification dates. Many objects also implement the `\Charcoal\Object\PublishableInterface`.


# Core objects

- [Section](#section-object)
- [Text](#text-object)
- [Block](#block-object)

## Section object

A **section**, in Charcoal, is a reachable page on the website, as part of the full hierarchical site map. They can be displayed in menus or breadcrumbs and be reached with a unique URL (`routable`).

Types of sections:

- `blocks`
	- Blocks sections define their content as a structured map of blocks.
- `content`
	- Content sections define their content in a single, simple _HTML_ property.
- `empty`
	- Empty sections are linked to a template but do not require any custom content.
- `external`
	- External sections are simply a redirect to an external (or internal) URL.

All section types, except _external_, make use of a `Template` object to be rendered. Typically, a charcoal `view` make sure of linking the `template` (by default, _mustache_

Sections are standard Charcoal `Model`, meaning they are definable with a `Metadata` object (which define a map of `properties`) and storable with a `Source` object.

Base section properties:

| Name                 | L10n | Type      | Description |
| -------------------- | :--: | --------- | ----------- |
| **title**            | ✔    | string    |
| **subtitle**         | ✔    | html      |
| **template_ident**   |      | string    |
| **template_options** |      | structure |

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

The hierarchical interface also provide the following methods, amongst others:

- `hierarchy()`
- `children()`
- `siblings()`

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

## Text object

## Block object

# CMS objects

- [Event](#event-object)
- [FAQ](#faq-object)
- [News](#news-object)

## Event object

## FAQ object

## News object

# Media attachments objects

- [Document](#document-object)
- [Image](#image-object)
- [Video](#video-object)

## Document object

## Image object

## Video object

# Extending objects
