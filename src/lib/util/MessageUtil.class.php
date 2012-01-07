<?php
namespace ultimate\util;
use wcf\util\StringUtil;

/**
 * Provides useful functions for messages.
 *
 * @author Jim Martens
 * @copyright 2011-2012 Jim Martens
 * @license http://www.plugins-zum-selberbauen.de/index.php?page=CMSLicense CMS License
 * @package de.plugins-zum-selberbauen.ultimate
 * @subpackage util
 * @category Ultimate CMS
 */
class MessageUtil {
    
    /**
     * Strips some stupid thinks out of text.
     *
     * @param string $text
     */
    public static function stripCrap($text) {
        //unifies new lines
        $cleanedText = StringUtil::unifyNewlines($text);
        return $cleanedText;
    }
}
