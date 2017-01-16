<?php
########################################################################
# Extension Manager/Repository config file for ext "fal_ttnews".
#
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'FAL for tt_news',
	'description' => 'Adds FAL support to tt_news image and media fields.',
	'category' => 'fe',
	'version' => '0.5.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => 'bottom',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Christian Jürges',
	'author_email' => 'christian.juerges@xwave.ch',
	'author_company' => 'xWave GmbH',
	'constraints' => array(
		'depends' => array(
			'tt_news' => '7.6.0-7.99.99',
			'php' => '5.4.0-0.0.0',
			'typo3' => '7.6.0-7.99.99',
		),
		'conflicts' => array(),
		'suggests' => array(),
	),
);
