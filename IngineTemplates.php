<?php

    class IngineTemplates{

        private $context;
        private $tree;

        public function __construct($context, $fnameOcontent, $isContent = false){

            $this->context = $context; 

            $filepath = PAGES . $this->context->getParam('lang') . '/'. $fnameOcontent;
            if(DEBUG===true) echo 'TEMPLATE'.$filepath . '#', file_exists($filepath),'#';

            $content = '';
            if(!$isContent){ // template is not in file but given as string
                if(!file_exists($filepath)) return new Exception('Not found template [' . $fnameOcontent . ']');
                $content = file_get_contents($filepath);
            }else{
                $content = $fnameOcontent;
            }
    
            $tree = [];
            $tree['_/'] = [];
            $this->build_tree($tree, '/', $content);
            $this->tree = $tree;
        }

        public function respawn($path, $values = 0){
            $this->respawn_tree($this->tree['/'], $this->tree['_/'], preg_split('!/!', $path, -1, PREG_SPLIT_NO_EMPTY) , $values);
        }

        public function respawn_n($path, $matrix = []){
            foreach ($matrix as $vector)
                $this->respawn_tree($this->tree['/'], $this->tree['_/'], preg_split('!/!', $path, -1, PREG_SPLIT_NO_EMPTY) , $vector);
        }

        public function glue(){
            return $this->glue_tree($this->tree['_/']);
        }

        public function __toString(){
            return $this->glue();
        }

    
        // respawns specified paths and stores values from embeded variables
        // is recursive !
        // $tree - original tree
        // $rtree - respawned tree
        private function respawn_tree(&$tree, &$rtree, $path, $values = 0){
            if(!$tree) return;
    
            $pathhasmore = count($path);
            $key = array_shift($path);
            $valuesRow = $values;
    
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
                    $this->respawn_tree($tree[$key], $rtree['_result'][$iterscount][$key], $path, $valuesRow);
                }
    
            }while(is_array($values) && is_array($valuesRow=next($values)));
        }
    
        // glues output from respawned tree
        // is recursive !
        // $rtree - respawned tree
        private function glue_tree(& $tree){
            if(!$tree) return '';
    
            $output = '';
             foreach($tree['_result'] as $iteration){
                $subout = $tree['_text'];
                foreach($tree['_keys'] as $key){
                    if(isset($iteration[$key])){
                        $value = $this->glue_tree($iteration[$key]);
                        $subout = str_replace('##' . $key . '##', $value, $subout);
                    }else
                        $subout = str_replace('##' . $key . '##', '', $subout);
                }
                if(is_array($iteration['_data']))
                    foreach($iteration['_data'] as $fkey => $fvalue)
                        $subout = str_replace('{' . $fkey . '}', strval($fvalue), $subout);
                $output .= $subout;
            }
            return $output;
    
        }
    
        // builds structure tree from original template file
        // is recursive !
        private function build_tree(&$tree, $key, $subPart){
    
            preg_match_all('!'.TDO.'t\:([a-z0-9\-]+)\s*'.TDC.'(.*)'.TDO.'/t:\1'.TDC.'!ism', $subPart,$results,PREG_SET_ORDER);
            $tree[$key]['_text'] = preg_replace('!'.TDO.'t\:([a-z0-9\-]+)\s*'.TDC.'(.*)'.TDO.'/t:\1'.TDC.'!ism', '##\1##', $subPart);
            $tree[$key]['_keys'] = array();
    
            foreach($results as $match){
                $this->build_tree($tree[$key],$match[1],$match[2]);
                array_push($tree[$key]['_keys'],$match[1]);
            }
    
        }
    }

?>
