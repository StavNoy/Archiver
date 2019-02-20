<?php

	/*
	** main function is 'conflict_resolver', which returns 'true' if it is resolved or 'false' if it is not
	** this function recieves the path to a file, and if the file exist already, prompts user in stdin:
	**		does the user want to delete original / skip archive file / quit  ,with option to remember choice
	**		in case the original is a non - empty directory, it is deleted recursively
	*/

	/*
	** fonction principale est 'conflict_resolver', qui renvoie «true» s'il est résolu ou «false» s'il ne l'est pas.
	** cette fonction reçoit le chemin d'accès à un fichier et, si le fichier existe déjà, affiche l'utilisateur en stdin:
	** l'utilisateur veut-il supprimer l'original / ignorer le fichier d'archive / quitter, avec une option permettant de mémoriser le choix
	** dans le cas où l'original est un répertoire non vide, il est supprimé récursivement
	*/

	$ask = true;
	$is_delete = false;

	function resolve_conflicts(string $path){
		global $ask, $is_delete;
		if (file_exists($path)){
			if ($ask){
				echo basename($path) . " Existe deja a "  . $path ."\nEcraser? [yes/no/always yes/always no/quit all]" . PHP_EOL;
				$answer = read_stdin();
				rec_manage_answer($answer);
			}
			if ($is_delete){
				rec_del($path);
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}


	function read_stdin() {
		$stdin = fopen("php://stdin","r");
		$input = fgets($stdin,20);
		$input = trim($input);
		fclose ($stdin);
		return $input;
	}

	function rec_manage_answer(string $answer){
		global $ask, $is_delete;
		switch($answer){
			case "always yes" :
				$ask = false;
			case "yes" :
				$is_delete = true;
				return;
			case "always no" :
				$ask = false;
			case "no":
				$is_delete = false;
				return;
			case "quit all" :
				exit();
			default :
				echo "S'il vouz plait, repondez avec [yes/no/always yes/always no/quit all]" . PHP_EOL;
				$new_answer = read_stdin();
				return rec_manage_answer($new_answer);
		}
	}

	function rec_del(string $path){
		if (is_dir($path)){
			$dir = opendir($path);
			while ($subfile = readdir($dir)){
				if (basename($subfile) != "." && basename($subfile) != ".."){
					rec_del($path . "/" . $subfile);
				}
			}
			closedir($dir);
			rmdir($path);
		} else {
			unlink($path);
		}
	}