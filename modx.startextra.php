<?php

if($argc < 2) {
	echo "\n\tbuild_structure.php <project_name_ucfirst_plural>\n";
}
else {
	$modx_pkg_name = $argv[1];
	echo shell_exec("git clone http://github.com/splittingred/modExtra.git ./modExtra; cd modExtra; mkdir ../$modx_pkg_name; git archive HEAD | (cd ../$modx_pkg_name/ && tar -xvf -); cd ../$modx_pkg_name; grep -rl 'modExtra' . | xargs sed -i '' -e 's/modExtra/$modx_pkg_name/g'; grep -rl 'modextra' . | xargs sed -i '' -e 's/modextra/".strtolower($modx_pkg_name)."/g'; grep -rl 'Item' . | xargs sed -i '' -e 's/Item/".substr($modx_pkg_name,0,-1)."/g'; grep -rl 'item' . | xargs sed -i '' -e 's/item/".strtolower(substr($modx_pkg_name,0,-1))."/g'; grep -rl 'active".substr($modx_pkg_name,0,-1)."' . | xargs sed -i '' -e 's/active".substr($modx_pkg_name,0,-1)."/activeItem/g'; grep -rl ',location' . | xargs sed -i '' -e 's/,location/,item/g';");

	$list = explode("\n",shell_exec("cd $modx_pkg_name; find . |grep modextra"));

	$number = 0;
	foreach($list as $value) {
		$number = ($number > substr_count($value,"modextra") ? $number : substr_count($value,"modextra"));
	}
	unset($list);

	for($i=0;$i<$number;$i++) {
		$list = explode("\n",shell_exec("cd $modx_pkg_name; find . |grep modextra"));
		foreach(array_reverse($list) as $value) {
			if($value !== "" && file_exists("$modx_pkg_name/".end(explode("./",$value, 0)))) {
				shell_exec("cd $modx_pkg_name; mv $value ".str_replace("modextra",strtolower($modx_pkg_name), $value));
			}
		}
		unset($value);
		unset($list);
	}
	$list = explode("\n",shell_exec("cd $modx_pkg_name; find . |grep item"));
	foreach(array_reverse($list) as $value) {
		if($value !== "" && file_exists("$modx_pkg_name/".end(explode("./",$value, 0)))) {
			shell_exec("cd $modx_pkg_name; mv $value ".str_replace("item",strtolower(substr($modx_pkg_name,0,-1)), $value));
		}
	}
	unset($value);
	unset($list);

	echo "Done\n";
	if(file_exists($argv[2])) {
		echo "\nStarting Copyright string replacement\n";
		$copystring = " * Copyright 2010 by Shaun McCormick <shaun+".strtolower($modx_pkg_name)."@modx.com>
 *
 * ".$modx_pkg_name." is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * ".$modx_pkg_name." is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * ".$modx_pkg_name."; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
";

		$list = explode("\n",shell_exec('cd '.$modx_pkg_name.'; grep -rl "'.$copystring.'" .'));
		foreach($list as $filename) {
			if(!empty($filename) && file_exists("$modx_pkg_name/$filename")) {
				echo "Checking $modx_pkg_name/$filename\n";
				$output = file_get_contents("$modx_pkg_name/$filename");
				$output = str_replace($copystring, file_get_contents($argv[2]), $output);
				file_put_contents("$modx_pkg_name/$filename", $output);
			}
		}
	}
	exit("\n\nFinished\n");
}
?>