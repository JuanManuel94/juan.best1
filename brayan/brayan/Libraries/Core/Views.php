<?php
    class Views{
        function getView($controller,$view,$data=""){
            $controller = get_class($controller);
            if($controller == "Login"){
                $view = "Views/".$controller."/". $view.".php";
            }else{
                $view = "Views/".$controller."/". $view.".php";
            }
            require_once($view);
        }
    }
