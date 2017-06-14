<?php
$arrayList = array
(
        0 => array
        (
                'id'=> 1,
                'name' => 'cat_name_1',
                'list' => array
                (
                        1 => 'swgdgbdg',
                        2 => 'xcbcxb'
                )
                
        ),
        1 => array
        (
                'id' => 3,
                'name' => 'cat_name_3',
                'list' => array
                (
                        0 => array
                        (
                                'id' => 1,
                                'name' => 'cat_name_1',
                                'list' => array
                                (
                                        1 => '543h54h',
                                        2 => '54hrhhfr2'
                                 )
                                
                         ),
                        1 => array
                        (
                                'id' => 2,
                                'name' => 'cat_name_2',
                                'list' => array
                                (
                                        1 => '543h54ha',
                                        2 => '54hrhhfr123'
                                )
                                
                        )
                        
                
                )
          )
);

function getArrayList($array, &$resultArray = []){
    $tempArray = [];
    $hasChrien = false;
    $trueArray = $array;
    $count = count($array);
    for($i=0; $i<$count;$i++){
        $tempArray = array_shift($array);
        if(is_array($tempArray) && array_key_exists('list', $tempArray)){
            $hasChrien = true;
            getArrayList($tempArray['list']);
        }
    }
    if($hasChrien == false){
        return $trueArray;
    }
}
$resultArray = [];
getArrayList($arrayList,$resultArray);
var_dump($resultArray);
?>