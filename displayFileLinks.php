<?php
/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Christian Jürges <christian.juerges@xwave.ch>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * FAL Support
 * See more here: http://wiki.typo3.org/File_Abstraction_Layer
 *
 * @param    array $markerArray : array filled with markers from the getItemMarkerArray function in tt_news class. see: EXT:tt_news/pi/class.tx_ttnews.php
 * @param    [type]        $conf: ...
 * @return    array        the changed markerArray
 */
function user_displayFileLinks($markerArray, $conf)
{
	$pObj = &$conf['parentObj']; // make a reference to the parent-object
	$row = $pObj->local_cObj->data;
	$markerArray['###FILE_LINK###'] = '';
	$markerArray['###TEXT_FILES###'] = '';

	//load TS config for newsFiles from tt_news
	$conf_newsFiles = $pObj->conf['newsFiles.'];
	//Important: unset path
	$conf_newsFiles['path'] = '';

	$local_cObj = GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer');

	//workspaces
	if (isset($row['_ORIG_uid']) && ($row['_ORIG_uid'] > 0)) {
		// draft workspace
		$uid = $row['_ORIG_uid'];
	} else {
		// live workspace
		$uid = $row['uid'];
	}
	// Check for translation ?

	$fileRepository = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\FileRepository');
	$fileObjects = $fileRepository->findByRelation('tt_news', 'tx_falttnews_fal_media', $uid);

	if (is_array($fileObjects)) {
		$files_stdWrap = GeneralUtility::trimExplode('|', $pObj->conf['newsFiles_stdWrap.']['wrap']);
		$filelinks = '';
		$debugArray = array();
		foreach ($fileObjects as $key => $file) {
			$referenceProperties = $file->getReferenceProperties();
			$origReferenceProperties = $file->getOriginalFile()->getProperties();

			// cyz, Michael Stein: add titles to links
			// changed 25.01.2015 by Ephraim Härer | RENOLIT SE
			// set title
			if (!empty($referenceProperties['title']) && !is_null($referenceProperties['title'])) {
				$conf_newsFiles['title'] = $referenceProperties['title'];
			} else if (!empty($origReferenceProperties['title']) && !is_null($origReferenceProperties['title'])) {
				$conf_newsFiles['title'] = $origReferenceProperties['title'];
			} else {
				$conf_newsFiles['title'] = $origReferenceProperties['name'];
			}

			// set alt text
			if (!empty($referenceProperties['alternative']) && !is_null($referenceProperties['alternative'])) {
				$conf_newsFiles['altText'] = $referenceProperties['alternative'];
			} else if (!empty($origReferenceProperties['alternative']) && !is_null($origReferenceProperties['alternative'])) {
				$conf_newsFiles['altText'] = $origReferenceProperties['alternative'];
			} else {
				$conf_newsFiles['altText'] = $conf_newsFiles['title'];
			}

			// set title text
			if (!empty($referenceProperties['title']) && !is_null($referenceProperties['title'])) {
				$conf_newsFiles['titleText'] = $referenceProperties['title'];
			} else {
				$conf_newsFiles['titleText'] = $origReferenceProperties['title'];
			}

			$conf_newsFiles['labelStdWrap.']['override'] = ' ' . $conf_newsFiles['title'] . ' - ';

			$local_cObj->start($file->getOriginalFile()->getProperties());
			$filelinks .= $local_cObj->filelink(rawurldecode($file->getPublicUrl()), $conf_newsFiles);
			/*
			  $debugArray[] = array(
			  'ref' => $referenceProperties,
			  'orig' => $origReferenceProperties,
			  'conf_newsFiles' => $conf_newsFiles,
			  'PublicUrl' => rawurldecode($file->getPublicUrl()),
			  'filelinks' => $local_cObj->typolink( $file->getPublicUrl(), $conf_newsFiles),
			  );
			 */
		}


		if ($filelinks) {
			$markerArray['###FILE_LINK###'] = $filelinks . $files_stdWrap[1];
			$markerArray['###TEXT_FILES###'] = $files_stdWrap[0] . $pObj->local_cObj->stdWrap($pObj->pi_getLL('textFiles'), $pObj->conf['newsFilesHeader_stdWrap.']);
		}
		//TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($debugArray);
	}
	return $markerArray;
}
