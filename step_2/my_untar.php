<?php

	include "resolve_conflicts.php";

	array_shift($argv);
	if ($argc > 0){
		manager($argv);
	}

	function manager(array $paths){
		foreach ($paths as $archive_name) {
			$archive = fopen($archive_name, "r");
			$asArr = json_decode(fread($archive, filesize($archive_name)), TRUE);
			fclose($archive);
			rec_create_file($asArr, realpath(getcwd()));
		}
	}

	function rec_create_file(array $data, string $parent){
		foreach ($data as $filename => $contents) {
			$newpath = $parent . "/" . basename($filename);
			if (is_array($contents)){
				if (resolve_conflicts($newpath)){
					mkdir($newpath);
					rec_create_file($contents, $newpath);
				}
			} else {
				if (resolve_conflicts($newpath)){
					$file = fopen($newpath, "w");
					fwrite($file, utf8_decode($contents));
					fclose($file);
				}
			}
		}
	}
