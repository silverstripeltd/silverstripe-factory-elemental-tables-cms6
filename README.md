
# Signify SilverStripe Factory Elemental Table Block

An elemental block to enable more control over the responsiveness of tables.

## Require via composer.json

```json
"repositories": [
    {
        "type": "vcs",
        "url": "git@github.com:signify-nz/silverstripe-factory-elemental-tables-cms6.git"
    }
],
"require": {
    "signify-nz/silverstripe-factory-elemental-tables": "^2"
}
```

## Usage

### Content editable fields

* Title
* Description field
* Caption field

### Settings

* Set the Number of Columns
* Set the Number of Rows
* Provide the CSS classes for alignments and styles, e.g. "Zebra rows"
* Set the width for each column

### Styles

You need to add CSS styles on your site to theme the table block.

## Extension

There are two extension points to allow for overriding how the
description and cells are rendered:

* `formatDescription` on `TableBlock`
* `formatCells` on `TableItem`

Both extension points pass an object by reference and expect that object
to be modified or replaced. `formatDescription` is passed a DBField of
the description; `formatCells` is passed an `ArrayList` of `DBField`s
representing each (visible) cell.

### Screenshots

Main content
![content](docs/en/img/content.png)

Edit cells
![content](docs/en/img/table-items.png)

Settings
![content](docs/en/img/settings-1.png)
![content](docs/en/img/settings-2.png)
