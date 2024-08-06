<?php

class producto{
    public $id_producto;
    public $nombre_producto;
    public $precio_producto;
    public $descripcion;
    public $archivo;
    

    //constructor
    public function __construct($_id_producto,$_nombre_producto,$_precio_producto,$_descripcion, $_archivo){
        $this->id_producto= $_id_producto;
        $this->nombre_producto= $_nombre_producto;
        $this->precio_producto= $_precio_producto;
        $this->descripcion= $_descripcion;
        $this->archivo="./lentes1.jpg";
    }

    public function imprimir()
    {

        echo '<div class="card m-2" style="width: 18rem;">
            <div class="card-body">
            <h5 class="card-title">'.$this->nombre_producto.'</h5>
            <p class="card-text">'.$this->precio_producto.'</p>
            <p class="card-text">'.$this->descripción.'</p>
            </div>
            <img src="lentes1.jpg" alt="">
            
            </div>';
    }
}

?>