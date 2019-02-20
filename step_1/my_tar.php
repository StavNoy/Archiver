<?php
	/*
	** Premier 'argument' est le nom du script alors on fait shift
	** si l'argument prend le nom de ce script s'il est > 0, nous appellons la function manager
	** nous creons un tableau, ou la clef et le nom du ficher et la valeur et la content du fichier
	** si le fichier est un dossier, cette valeur est un tableau avec la meme logique, mais recursivement.
	** si il n'y a pas dossier, la valeur associe un content avec la forme string
	** le string est encode par utf8, pour se conformer avec le Json.
	** L'array principal, s'inscrit dans le fichier archive a la forme json
	*/

	array_shift($argv);
	if ($argc > 0){
		manager($argv);
	}

	function manager(array $paths){
		$output = [];
		foreach ($paths as $filname){
			$output[$filname] = rec_get_value($filname);
		}
		$archive = fopen("output.mytar", "w");
		fwrite($archive, json_encode($output));
		fclose($archive);
	}

	function rec_get_value(string $path){
		$value = FALSE;
		if (is_dir($path)){
			$value = [];
			$dir = opendir($path);
			while ($subfile = readdir($dir)){
				if (basename($subfile) != "." && basename($subfile) != ".."){
					$value[$subfile] = rec_get_value($path . "/" . $subfile);
				}
			}
			closedir($dir);
		} else {
			$file = fopen($path, "r");
			$value = utf8_encode(fread($file, filesize($path)));
			fclose($file);
		}
		return $value;
	}

