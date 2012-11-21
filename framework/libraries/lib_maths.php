<?php


	function lib_maths_percent($mark, $outof) {
		if($outof == 0) {
			return 0;
		}
		return number_format($mark / $outof * 100, 2, ".", ",");
	}

