# One Solr plugin for Craft CMS 3.x

Craft Solr Integration

Forked from https://github.com/dionsnoeijen/dssolr

![Screenshot](resources/img/plugin-logo.png)

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require onedesign/one-solr

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for One Solr.

## Configuring One Solr

1. In `app/config` create a `one-solr.php` file with the following configs
```
return [
	'credentials' => [
		'host' => getenv('SOLR_HOST'),
		'port' => getenv('SOLR_PORT'),
		'path' => getenv('SOLR_PATH'),
		'core' => getenv('SOLR_CORE')
	],
	'stepSize' => 10,
	'sectionIdField' => 'section_id_i',
];
```
Set your environment vars as normal, you'll probably need to use `/` for SOLR_PATH

2. Create a `onesolr` directory in `templates`

For each section Solr map you want to generate, create a `.json` file and place in `templates/onesolr`. It will need to accept `sectionId`, `entryId`, `limit`, and `offset`. It should look something like this:
```
{% if sectionId is defined %}
	{% set entries = craft.entries.sectionId(sectionId).status('enabled').limit(limit).offset(offset).all() %}
{% else %}
	{% set entries = craft.entries({id: entryId}).all() %}
{% endif %}

[
	{% for entry in entries %}
		{
			"id": "{{ entry.id }}",
			"section_id_i": {{ entry.sectionId | raw }},
			"entry_id_i": {{ entry.id }},
			"article_type_i": 4,
			"title": {{ entry.title | json_encode() | raw }}
		}
		{% if loop.index != entries|length %},{% endif %}
	{% endfor %}
]
```
## One Solr Roadmap

Some things to do, and ideas for potential features:
- Refactor the JSON files to just use a config of entry variables to store
- Refactor js
- Use jobs to update entries instead of relying on JS sending multiple batches

* Release it

Brought to you by [Michael Ramuta](https://github.com/onedesign)
