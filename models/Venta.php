<?php 
class Venta{
    private int $idVenta;
    private int $idUsuario;
    private float $total;
    private float $fecha;
    private string $metodoPago;

    public function __construct(
        int $idVenta,
        int $idUsuario,
        float $total,
        string $fecha,
        string $metodoPago,
    ){
        $this -> idVenta = $idVenta;
        $this -> idUsuario = $idUsuario;
        $this -> total =  $total;
        $this -> fecha = $fecha;
        $this -> metodopago =  $metodoPago;
    }

    //getters
    public function getIdVenta(): int{
        return $this -> idVenta; 
    }

    public function getIdUsuario(): int {
        return $this -> idUsuario;
    }

    public function getTotal(): float{
        return $this -> total;
    }

    public function getMetodoPago(): string{
        return $this ->metodopago;
    }

    //setters
    public function setIdVenta(int $idVenta): void{
     $this -> idVenta = $idVenta;
    }

    public function setIdUsuario(int $idUsuario): void{
     $this -> idUsuario = $idUsuario
    }

    public function setTotal(float $total): void{
     $this -> total = $total;
    }

    public function setMetodoPago(string $metodopago): void{
     $this -> metodopago = $metodopago;
    }
}

?>