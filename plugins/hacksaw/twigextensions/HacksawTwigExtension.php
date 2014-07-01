<?php

namespace Craft;

use Twig_Extension;
use Twig_Filter_Method;

class HacksawTwigExtension extends Twig_Extension
{
	public function getName()
	{
		return 'Hacksaw';
	}
	
	public function getFilters()
	{
		return array(
			'hacksaw' => new Twig_Filter_Method($this, 'HacksawFilter'),
		);
	}
	
	public function HacksawFilter( $content, $chars='', $words='', $cutoff='', $append='', $allow='', $chars_start='' )
	{
	
		$chars_start = ($chars_start ? $chars_start : 0);
	
		if(isset($cutoff) && $cutoff != "") {
		
			$cutoff_content = $this->_truncate_cutoff($content, $cutoff, $words, $allow, $append);
			
			// Strip the HTML
			$new_content = (strpos($content, $cutoff) ? strip_tags($cutoff_content, $allow) : strip_tags($cutoff_content, $allow));

		} elseif (isset($chars) && $chars != "") {
		
			// Strip the HTML
			$stripped_content = strip_tags($content, $allow);
			
			$new_content = (strlen($stripped_content) <= $chars ? $stripped_content : $this->_truncate_chars($stripped_content, $chars_start, $chars, $append));

		} elseif (isset($words) && $words != "") {

			// Strip the HTML
			$stripped_content = strip_tags($content, $allow);
			
			$new_content = (str_word_count($stripped_content) <= $words ? $stripped_content : $this->_truncate_words($stripped_content, $words, $append));

		} else {

			// Strip the HTML
			$stripped_content = strip_tags($content, $allow);
			
			$new_content = $stripped_content;

		}

		// Return the new content
		return $new_content;
	}

	// Helper Function - Truncate by Word Limit
	function _truncate_words($content, $limit, $append) {
		
		$num_words = str_word_count($content, 0);
		
		if ($num_words > $limit) {
			
			$words = str_word_count($content, 2);
			
			$pos = array_keys($words);

			$content = substr($content, 0, ($pos[$limit]-1)) . $append;
		
		}
		
		return $content;

    }
    
	// Helper Function - Truncate by Character Limit
	function _truncate_chars($content, $chars_start, $limit, $append) {

		// Removing the below to see how it effect UTF-8. 
	    $content = preg_replace('/\s+?(\S+)?$/', '', substr($content, $chars_start, ($limit+1))) . $append;

		return $content;
		
	}
	
	// Helper Function - Truncate by Cutoff Marker
	function _truncate_cutoff($content, $cutoff, $words, $allow, $append) {
	
		$pos = strpos($content, $cutoff);
		
		if ($pos != FALSE) {
			
			$content = substr($content, 0, $pos) . $append;

		} elseif ($words != "") {
			
			$content = $this->_truncate_words(strip_tags($content, $allow), $words, '') . $append;
		}
		
		return $content;

	}	

}