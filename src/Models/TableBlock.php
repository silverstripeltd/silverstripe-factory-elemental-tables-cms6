<?php

namespace Signify\Factory\Models;

use DNADesign\Elemental\Models\BaseElement;
use Signify\Factory\Models\TableItem;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\GridField\GridFieldFilterHeader;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\View\Requirements;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;

class TableBlock extends BaseElement
{
    private static $table_name = 'TableBlock';

    private static $singular_name = 'Table block';

    private static $plural_name = 'Table blocks';

    private static $class_description = 'Table block';

    private static $cms_icon_class = 'font-icon-block-table-data';

    private static $db = [
        'TableDescription' => 'HTMLText',
        'TableCaption' => 'Varchar',
        'NumberOfColumns' => 'Int',
        'FirstRowIsHeader' => 'Boolean',
        'FirstColumnIsHeader' => 'Boolean',
        'LastRowIsFooter' => 'Boolean',
        'AlignHeadRowV' => "Enum('top,middle,bottom','top')",
        'AlignHeadRowH' => "Enum('left,center,right', 'left')",
        'AlignFootRowV' => "Enum('top,middle,bottom','top')",
        'AlignFootRowH' => "Enum('left,center,right', 'left')",
        'AlignHeadColV' => "Enum('top,middle,bottom','top')",
        'AlignHeadColH' => "Enum('left,center,right', 'left')",
        'AlignBodyCelV' => "Enum('top,middle,bottom','top')",
        'AlignBodyCelH' => "Enum('left,center,right', 'left')",
        'BorderOuter' => 'Boolean',
        'BorderHeader' => 'Boolean',
        'BorderFooter' => 'Boolean',
        'BorderFirstColumn' => 'Boolean',
        'BorderRows' => 'Boolean',
        'BorderCols' => 'Boolean',
        'PropCol1' => 'Int',
        'PropCol2' => 'Int',
        'PropCol3' => 'Int',
        'PropCol4' => 'Int',
        'PropCol5' => 'Int',
        'PropCol6' => 'Int',
        'PropCol7' => 'Int',
        'PropCol8' => 'Int',
        'ZebraRows' => 'Boolean',
    ];

    private static $has_many = [
        'TableItems' => TableItem::class,
    ];

    private static $owns = [
        'TableItems',
    ];

    private static $defaults = [
        'NumberOfColumns'  => 4,
        'FirstRowIsHeader' => true,
        'FirstColumnIsHeader' => false,
        'LastRowIsFooter' => false,
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $tableDescription = HTMLEditorField::create(
            'TableDescription'
        );
        $tableDescription->setEditorConfig('tableTinyMCE');
        $tableDescription->setRows(6);
        $fields->replaceField('TableDescription', $tableDescription);

        $tableCaption = TextareaField::create('TableCaption');
        $tableCaption->setRows(2);
        $fields->replaceField('TableCaption', $tableCaption);

        $gridField = $fields->fieldByName('Root.TableItems.TableItems');

        if ($gridField) {
            $gridConfig = $gridField->getConfig();
            $gridConfig->addComponent(new GridFieldSortableRows('SortOrder'));
            $gridConfig->removeComponentsByType(GridFieldAddExistingAutocompleter::class);
            $gridConfig->removeComponentsByType(GridFieldDeleteAction::class);
            $gridConfig->removeComponentsByType(GridFieldFilterHeader::class);
            $gridConfig->addComponent(new GridFieldDeleteAction(false));
        }

        // Use dropdown field so we can limit the range of numbers
        $numberOfColumns = DropdownField::create(
            'NumberOfColumns',
            'Number of Columns',
            array_combine(
                range(1, 8),
                range(1, 8)
            )
        );
        $fields->replaceField('NumberOfColumns', $numberOfColumns);

        foreach ([
            'AlignHeadRowV' => 'Header Vertical Alignment',
            'AlignHeadRowH' => 'Header Horizontal Alignment',
            'AlignFootRowV' => 'Footer Vertical Alignment',
            'AlignFootRowH' => 'Footer Horizontal Alignment',
            'AlignHeadColV' => 'Header Column Vertical Alignment',
            'AlignHeadColH' => 'Header Column Horizontal Alignment',
            'AlignBodyCelV' => 'Cell Vertical Alignment',
            'AlignBodyCelH' => 'Cell Horizontal Alignment',
        ] as $key => $value) {
            $fields->removeFieldFromTab('Root.Main', $key);
            $fields->addFieldToTab(
                'Root.Settings',
                DropdownField::create(
                    $key,
                    $value,
                    $this->dbObject($key)->enumValues()
                )
            );
        }

        foreach ([
            'FirstRowIsHeader' => 'ExtraClass',
            'BorderHeader' => 'FirstRowIsHeader',
            'LastRowIsFooter' => 'AlignHeadRowH',
            'BorderFooter' => 'LastRowIsFooter',
            'FirstColumnIsHeader' => 'AlignFootRowH',
            'BorderFirstColumn' => 'FirstColumnIsHeader',
        ] as $field => $previous) {
            $fields->removeFieldFromTab('Root.Main', $field);
            $item = CheckboxField::create($field);
            $fields->insertAfter($previous, $item);
        }

        $cellSettings = LiteralField::create('CellSettings', '<p>Cell Settings</p>');
        $fields->insertAfter('AlignHeadColH', $cellSettings);
        foreach ([
            'BorderOuter' => 'CellSettings',
            'BorderRows' => 'BorderOuter',
            'BorderCols' => 'BorderRows',
            'ZebraRows' => 'BorderCols',
        ] as $field => $previous) {
            $fields->removeFieldFromTab('Root.Main', $field);
            $item = CheckboxField::create($field);
            $fields->insertAfter($previous, $item);
        }

        $cellSettings = LiteralField::create('PropSettings', '<p>Percentage of table width for column:</p>');
        $fields->insertAfter('AlignBodyCelH', $cellSettings);
        $cols = $this->NumberOfColumns;
        foreach (range(1, $cols) as $i) {
            $fieldName = 'PropCol' . $i;
            $fieldTitle = 'Proportion allocated to Column ' . $i;
            $field = DropdownField::create(
                $fieldName,
                $fieldTitle,
                [
                    10 => '10%',
                    20 => '20%',
                    30 => '30%',
                    40 => '40%',
                    50 => '50%',
                    60 => '60%',
                    70 => '70%',
                    80 => '80%',
                    90 => '90%',
                ],
            );
            $fields->addFieldToTab('Root.Settings', $field);
        }
        foreach (range($cols + 1, 9) as $i) {
            $fieldName = 'PropCol' . $i;
            $fields->removeByName($fieldName);
        }

        $fields->removeFieldFromTab('Root.Settings', 'ExtraClass');
        Requirements::customCSS("#Root_TableItems .form__field-holder.form__field-holder--no-label {
            flex: 1 0 auto;
            max-width: 100%;
            margin-left: 0;
            margin-right: 0;
          }");
        return $fields;
    }

    /**
     * Returns a preview of the selected settings for display in the CMS.
     * @return string
     */
    public function getCMSPreview()
    {
        if ($numRows = $this->TableItems()->count()) {
            $plurality = $numRows == 1 ? '' : 's';
            return "$numRows row$plurality. $this->NumberOfColumns columns.";
        }

        return 'Not configured or populated yet';
    }

    /**
     * {@inheritDoc}
     */
    protected function provideBlockSchema()
    {
        $blockSchema = parent::provideBlockSchema();
        $blockSchema['content'] = $this->getCMSPreview();
        return $blockSchema;
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return 'Table';
    }

    /**
     * {@inheritDoc}
     */
    public function inlineEditable()
    {
        return false;
    }

    public function getTotalColumns()
    {
        return $this->NumberOfColumns;
    }

    /**
     * Get the Table items
     *
     * Not include the header and footer
     *
     * @return ArrayList
     */
    public function getBody()
    {
       
        $body = $this->TableItems();

        if ($body->count() > 0) {

            if ($this->FirstRowIsHeader == true) {
                $body = $body->exclude([
                    'ID' => $body->First()->ID
                ]);
            }

            if ($this->LastRowIsFooter == true) {
                $body = $body->exclude([
                    'ID' => $body->Last()->ID
                ]);
            }
        }
        return $body;
    }

    /**
     * Get TableDescription field
     *
     * @return string
     */
    public function TableDescription()
    {
        $description = $this->dbObject('TableDescription');
        $this->extend('formatDescription', $description);

        return $description;
    }
}
