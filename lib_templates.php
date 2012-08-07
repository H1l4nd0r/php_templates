<?php
	//registerPlugin('templates');

	function tmpl_use($filename){
		if(!file_exists($filename)) return false;
		$content = preg_replace('/\n*\r*/','',file_get_contents($filename));
		$tree = array();
		$tree['_/'] = array();
		build_tree($tree, '/', $content);
		return $tree;
	}

	// is recursive !
	function tmpl_respawn(&$tree, $path, $values = 0){
		respawn_tree($tree['/'], $tree['_/'], preg_split('!/!', $path, -1, PREG_SPLIT_NO_EMPTY) , $values);
	}

	function tmpl_glue(&$tree){
		return glue_tree($tree['_/']);
	}

	// respawns specified paths and stores values from embeded variables
	// is recursive !
	// $tree - original tree
	// $rtree - respawned tree
	function respawn_tree(&$tree, &$rtree, $path, $values = 0){
		if(!$tree) return;

		$pathhasmore = count($path);
		$key = array_shift($path);
		$valuesRow = $values;


		if(is_array($values) && is_array(current($values)) ){
				$valuesRow = current($values);
		}

		if(is_array($values)) reset($values);
		do{
			// is first instance of the branch?
			if(!isset($rtree['_result'])){
				$rtree = array();
				$rtree['_text'] = & $tree['_text'];
				$rtree['_keys'] = & $tree['_keys'];
				$rtree['_result'] = array();
				array_push($rtree['_result'], ($pathhasmore) ? array('_data' => 0) : array('_data' => $valuesRow));
			}else if(!$pathhasmore){
				array_push($rtree['_result'], array('_data' => $valuesRow));
			}

			if(in_array($key,$tree['_keys'])){
				$iterscount = count($rtree['_result']) - 1;
				respawn_tree($tree[$key], $rtree['_result'][$iterscount][$key], $path, $valuesRow);
			}

		}while(is_array($values) && is_array($valuesRow=next($values)));
	}

	// glues output from respawned tree
	// is recursive !
	// $rtree - respawned tree
	function glue_tree(& $tree){
		if(!$tree) return;

		$output = '';
 		foreach($tree['_result'] as $iteration){
			$subout = $tree['_text'];
			foreach($tree['_keys'] as $key){
				if(isset($iteration[$key])){
					$value = glue_tree($iteration[$key]);
					$subout = str_replace('##' . $key . '##', $value, $subout);
				}else
					$subout = str_replace('##' . $key . '##', '', $subout);
			}
			if(is_array($iteration['_data']))
				foreach($iteration['_data'] as $fkey => $fvalue)
					$subout = str_replace('{' . $fkey . '}', $fvalue, $subout);
			$output .= $subout;
		}
		return $output;

	}

	// builds structure tree from original template file
	// is recursive !
	function build_tree(&$tree, $key, $subPart){

		preg_match_all('!\<t\:([a-z0-9]+)\s*\>(.*)\</t:\1\>!i', $subPart,$results,PREG_SET_ORDER);
		$tree[$key]['_text'] = preg_replace('!\<t\:([a-z0-9]+)\s*\>(.*)\</t:\1\>!i', '##\1##', $subPart);
		$tree[$key]['_keys'] = array();

		foreach($results as $match){
			build_tree($tree[$key],$match[1],$match[2]);
			array_push($tree[$key]['_keys'],$match[1]);
		}

	}

?>
