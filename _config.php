<?php

namespace Signify\Factory;

use SilverStripe\Core\Manifest\ModuleLoader;
use SilverStripe\Forms\HTMLEditor\TinyMCEConfig;

// TinyMCEConfig was removed in CMS6 - skip all configuration if not available
if (!class_exists(TinyMCEConfig::class)) {
    return;
}

// Set HTMLEditorField configurations
$tableTinyMCE = TinyMCEConfig::get('tableTinyMCE');
//$tableTinyMCE->setContentCSS(['themes/kaingaora/dist/editor.min.css']);
$tableTinyMCE->setOptions([
    'skin'                => 'silverstripe',
    'forced_root_block'   => 'p',
    'default_link_target' => '_blank',
    'valid_elements'      => "br,"
        . "p[class],"
        . "h3[class],"
        . "h4[class],"
        . "-a[class|href|target],"
        . "-em[class],"
        . "-strong[class],"
        . "-span[class],"
        . "ul[class],"
        . "ol[class],"
        . "li[class]",
    'block_formats'       => 'Paragraph=p;Heading3=h3;Heading4=h4;',
    'browser_spellcheck'  => true,
    'importcss_append'    => true,
]);
$tableTinyMCE->removeButtons(
    'blockquote',
    'bullist',
    'numlist',
    'indent',
    'outdent',
    'bold',
    'italic',
    'underline',
    'alignleft',
    'aligncenter',
    'alignright',
    'alignjustify',
    'formatselect',
    'styleselect',
    'paste',
    'pastetext',
    'removeformat',
    'sslink',
    'table',
    'code'
);
$tableTinyMCE->enablePlugins('charmap')
    ->setOption('charmap_append', [
        ['256', 'A - macron'],
        ['274', 'E - macron'],
        ['298', 'I - macron'],
        ['332', 'O - macron'],
        ['362', 'U - macron'],
        ['257', 'a - macron'],
        ['275', 'e - macron'],
        ['299', 'i - macron'],
        ['333', 'o - macron'],
        ['363', 'u - macron']
    ]);
$cmsModule = ModuleLoader::inst()->getManifest()->getModule('silverstripe/cms');
$adminModule = ModuleLoader::inst()->getManifest()->getModule('silverstripe/admin');
$assetsModule = ModuleLoader::inst()->getManifest()->getModule('silverstripe/asset-admin');
$tableTinyMCE->enablePlugins([
    'sslink' => $adminModule->getResource('client/dist/js/TinyMCE_sslink.js'),
    'sslinkemail' => $adminModule->getResource('client/dist/js/TinyMCE_sslink-email.js'),
    'sslinkfile' => $assetsModule->getResource('client/dist/js/TinyMCE_sslink-file.js'),
    'sslinkinternal' => $cmsModule->getResource('client/dist/js/TinyMCE_sslink-internal.js'),
    'sslinkexternal' => $adminModule->getResource('client/dist/js/TinyMCE_sslink-external.js'),
])->setOption('contextmenu', 'sslink');
$tableTinyMCE->addButtonsToLine(1, 'formatselect', 'bullist', 'numlist', 'bold', 'italic', 'removeformat', 'charmap', 'sslink', 'paste', 'pastetext');

// Set HTMLEditorField configurations
$cellTinyMCE = TinyMCEConfig::get('cellTinyMCE');
$cellTinyMCE->setOptions([
    'skin'                => 'silverstripe',
    'forced_root_block'   => 'p',
    'default_link_target' => '_blank',
    'valid_elements'      => "br,"
        . "p[class],"
        . "h4[class],"
        . "h5[class],"
        . "h6[class],"
        . "img[src|class|alt|title|width|height|data-id|data-shortcode],"
        . "-a[class|href|target],"
        . "-em[class],"
        . "-strong[class],"
        . "-span[class],"
        . "ul[class],"
        . "ol[class],"
        . "li[class]",
    'block_formats'       => 'Paragraph=p;Heading4=h4;Heading5=h5;Heading6=h6;',
    'browser_spellcheck'  => true,
    'importcss_append'    => true
]);
$cellTinyMCE->setOption('image_size_presets', [
    [
        'width' => '300',
        'text' => '300px - will be scaled to fit',
        'name' => 'scalefit',
        'default' => true
    ],
    [
        'i18n' =>  TinyMCEConfig::class . '.ORIGINAL_SIZE',
        'text' => 'Original size',
        'name' => 'originalsize'
    ]
]);
$cellTinyMCE->removeButtons(
    'blockquote',
    'bullist',
    'numlist',
    'indent',
    'outdent',
    'bold',
    'italic',
    'underline',
    'alignleft',
    'aligncenter',
    'alignright',
    'alignjustify',
    'formatselect',
    'styleselect',
    'paste',
    'pastetext',
    'removeformat',
    'sslink',
    'table',
    'code'
);
$cellTinyMCE->enablePlugins('charmap')
    ->setOption('charmap_append', [
        ['256', 'A - macron'],
        ['274', 'E - macron'],
        ['298', 'I - macron'],
        ['332', 'O - macron'],
        ['362', 'U - macron'],
        ['257', 'a - macron'],
        ['275', 'e - macron'],
        ['299', 'i - macron'],
        ['333', 'o - macron'],
        ['363', 'u - macron']
    ]);
$cmsModule = ModuleLoader::inst()->getManifest()->getModule('silverstripe/cms');
$adminModule = ModuleLoader::inst()->getManifest()->getModule('silverstripe/admin');
$assetsModule = ModuleLoader::inst()->getManifest()->getModule('silverstripe/asset-admin');
$cellTinyMCE->enablePlugins([
    'sslink' => $adminModule->getResource('client/dist/js/TinyMCE_sslink.js'),
    'sslinkemail' => $adminModule->getResource('client/dist/js/TinyMCE_sslink-email.js'),
    'sslinkfile' => $assetsModule->getResource('client/dist/js/TinyMCE_sslink-file.js'),
    'sslinkinternal' => $cmsModule->getResource('client/dist/js/TinyMCE_sslink-internal.js'),
    'sslinkexternal' => $adminModule->getResource('client/dist/js/TinyMCE_sslink-external.js'),
])->setOption('contextmenu', 'sslink');
$cellTinyMCE->enablePlugins([
    'ssmedia' => $assetsModule->getResource('client/dist/js/TinyMCE_ssmedia.js'),
])->setOption('contextmenu', 'ssmedia');
$cellTinyMCE->addButtonsToLine(1, 'formatselect', '|', 'bullist', 'numlist', '|', 'bold', 'italic', '|', 'removeformat', 'charmap', '|', 'ssmedia', 'sslink', '|', 'paste', 'pastetext');
