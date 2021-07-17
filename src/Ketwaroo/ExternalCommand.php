<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ketwaroo;

/**
 * Description of ExternalCommand
 *
 * @author Administrator
 */
class ExternalCommand {
    
    
    const OS_POLYFILL = [
        'WINNT'=>[
            'which'=>'where'
        ]
        
    ];
    
    /**
     * get a defined polyfill if avaible. By default assumes Linux OS.
     * 
     * @param type $command
     * @return string
     */
    public static function commandPolyFill(string $command) {
       return static::OS_POLYFILL[PHP_OS][$command]??$command;
    }
    /**
     * polyfill for linux which
     * 
     * @param string $bin
     * @return string
     */
    public static function which(string $bin) {
      
        $whichBin = static::commandPolyFill('which');
        
        return trim(`{$whichBin} {$bin}`);
        
    }
}
