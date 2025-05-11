<?php
/*Implementa los metodos abstractos para codificar y decodificar el Json */
abstract class ModelObject{
    abstract static public function fromJson($json):ModelObject;
    abstract public function toJson():String;
}
