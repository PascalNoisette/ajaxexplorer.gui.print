<?php
/*
 *
 * Copyright (C) 2012 Pascal Noisette
 *
 * This file is part of gui.print an Ajaxplorer plugin
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Library General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor Boston, MA 02110-1301,  USA
 *
 */
defined('AJXP_EXEC') or die( 'Access not allowed');

class Printer extends AJXP_Plugin {

	public function printAction($action, $httpVars, $fileVars){
		$newFile    = AJXP_Utils::decodeSecureMagic($httpVars["file"]);
		if ($message = $this->_printFileIntoDirectory($newFile)) {
			$this->_sendAjaxResponse($message);
		}		
	}
	protected function _printFileIntoDirectory($file) {
		$realFile = $this->_getRealFileName($file);
		$command = "lp  2>&1 " . escapeshellarg($realFile);
		
		$output = array(); $return_var = 0;
		exec ($command, $output, $return_var);
		return implode("\n", $output);
	}
	protected function _getRealFileName($filename) {
		$repo = ConfService::getRepository();
                $repo->detectStreamWrapper();
                $wrapperData = $repo->streamData;
                $urlBase = $wrapperData["protocol"]."://".$repo->getId();
		$realFile = call_user_func(array($wrapperData["classname"], "getRealFSReference"), rtrim($urlBase, '/').   $filename);
		return $realFile;
	}
	protected function _sendAjaxResponse($message){
		AJXP_XMLWriter::header();
		AJXP_XMLWriter::sendMessage($message, null);
		AJXP_XMLWriter::close();
		session_write_close();
		exit();
	}

}

?>
