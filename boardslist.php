<?php


function boardslist($option){
    
    $url = 'https://planches.4feuilles.org';
    
    $short = array('/gen/','/ani/','/tec/','/jeu/','/ale/','/art/','/cul/');
    
    $long = array('Générale','Anime','Technologie','Jeux vidéo','Aléatoire','Art','Porn');
    

    if ($option == 1) {
        
        foreach (array_combine($short, $long) as $short => $long) {
            $list[] = '<a href="'.$url.'' . $short . '" target="_top">['.$short.' - '. $long .']</a>';
        }
        
    } elseif ($option == 2){
        
        foreach (array_combine($short, $long) as $short => $long) {
            $list[] = '<li><a href="'.$url.'' . $short . '" target="_top"><button class="button-mobile" >'.$short.' - '. $long .'</button></a></li>';
        }
        
    } elseif ($option == 3){
        
        foreach (array_combine($short, $long) as $short => $long) {
            $list[] = '<a href="'.$url.'' . $short . '" target="_top"><button>'.$short.'</button></a> ';
        }
        
    } elseif ($option == 4){
        
        foreach (array_combine($short, $long) as $short => $long) {
            $list[] = '<a href="'.$url.'' . $short . 'imgboard.php?mode=admin" target="_top">['.$short.']</a>';
        }
        
    }
    
    $result = implode($list);
    
    return $result;
    
}